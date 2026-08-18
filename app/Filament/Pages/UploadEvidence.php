<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\ConfiguresEvidenceFileUpload;
use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use UnitEnum;

class UploadEvidence extends Page implements HasTable
{
    use ConfiguresEvidenceFileUpload;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCloudArrowUp;

    protected static ?string $navigationLabel = 'ອັບໂຫຼດຫຼັກຖານ';

    protected static string|UnitEnum|null $navigationGroup = 'ຫຼັກຖານ ແລະ ເອກະສານ';

    protected static ?string $title = 'ອັບໂຫຼດຫຼັກຖານ';

    protected string $view = 'filament.pages.upload-evidence';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['department-staff', 'super_admin']) ?? false;
    }

    private function currentAcademicYear(): ?AcademicYear
    {
        return AcademicYear::active();
    }

    /**
     * department-staff always work within their own department. super_admin
     * has no fixed department, so they pick one via the table filter (kept
     * in session across visits, defaulting to the first department).
     */
    private function resolveDepartmentId(): ?int
    {
        $user = Auth::user();

        if (! $user->hasRole('super_admin')) {
            return $user->department_id;
        }

        return $this->tableFilters['department_id']['value']
            ?? Department::query()->orderBy('name')->value('id');
    }

    public function table(Table $table): Table
    {
        $isSuperAdmin = Auth::user()->hasRole('super_admin');

        return $table
            ->query(function (): Builder {
                $departmentId = $this->resolveDepartmentId();
                $academicYear = $this->currentAcademicYear();

                /** @var Builder $query */
                $query = BasisMain::query()
                    ->when(
                        $academicYear,
                        fn (Builder $query) => $query->whereHas(
                            'indicator.standard',
                            fn (Builder $query) => $query->where('framework_id', $academicYear->framework_id)
                        ),
                        fn (Builder $query): Builder => $query->whereRaw('1 = 0'),
                    )
                    ->with(['indicator.standard', 'documents' => fn ($query) => $query
                        ->where('academic_year_id', $academicYear?->id)
                        ->whereHas('user', fn (Builder $query) => $query->where('department_id', $departmentId))
                        ->with('files')])
                    ->orderBy('indicator_id')
                    ->orderBy('order');

                return $query;
            })
            ->groups([
                Group::make('indicator.name')
                    ->label('ຕົວຊີ້ວັດ')
                    ->getTitleFromRecordUsing(fn (BasisMain $record): HtmlString => new HtmlString(
                        '<span class="text-lg font-semibold">'.e($record->indicator->name).'</span>'
                    ))
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('indicator_id', $direction)),
            ])
            ->defaultGroup('indicator.name')
            ->filters([
                SelectFilter::make('department_id')
                    ->label('ພະແນກ/ພາກວິຊາ')
                    ->options(fn () => Department::orderBy('name')->pluck('name', 'id'))
                    ->visible($isSuperAdmin)
                    ->query(fn (Builder $query) => $query),
            ])
            ->persistFiltersInSession()
            ->columns([
                TextColumn::make('title')
                    ->label('ຫຼັກຖານ')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('ສະຖານະ')
                    ->badge()
                    ->state(fn (BasisMain $record): string => $record->documents->first()?->files->isNotEmpty()
                        ? 'ອັບໂຫຼດແລ້ວ'
                        : 'ຍັງບໍ່ໄດ້ອັບໂຫຼດ')
                    ->color(fn (BasisMain $record): string => $record->documents->first()?->files->isNotEmpty()
                        ? 'success'
                        : 'danger'),
                TextColumn::make('files_count')
                    ->label('ຈຳນວນໄຟລ໌')
                    ->state(fn (BasisMain $record): int => $record->documents->first()?->files->count() ?? 0)
                    ->badge()
                    ->color('gray'),
                TextColumn::make('reference_numbers')
                    ->label('ເລກທີ່')
                    ->state(fn (BasisMain $record): string => $record->documents->first()?->files
                        ->pluck('reference_no')
                        ->implode(', ') ?? '')
                    ->wrap(),
            ])
            ->recordActions([
                Action::make('upload')
                    ->label('ອັບໂຫຼດ')
                    ->icon(Heroicon::OutlinedArrowUpTray)
                    ->schema([
                        ...$this->evidenceReferenceFields(),
                        $this->evidenceFileUploadComponent('documents'),
                        Hidden::make('disk')->default('local'),
                        Hidden::make('original_name')->required(),
                        Hidden::make('mime_type')->required(),
                        Hidden::make('size')->required(),
                    ])
                    ->action(function (BasisMain $record, array $data): void {
                        $academicYear = $this->currentAcademicYear();
                        $departmentId = $this->resolveDepartmentId();

                        $document = Document::query()
                            ->where('basis_main_id', $record->id)
                            ->where('academic_year_id', $academicYear->id)
                            ->whereHas('user', fn (Builder $query) => $query->where('department_id', $departmentId))
                            ->first();

                        if (! $document) {
                            $submitterId = Auth::user()->hasRole('super_admin')
                                ? User::where('department_id', $departmentId)->value('id')
                                : Auth::id();

                            if (! $submitterId) {
                                Notification::make()
                                    ->title('ພະແນກນີ້ຍັງບໍ່ມີຜູ້ໃຊ້ງານ, ບໍ່ສາມາດສ້າງເອກະສານໄດ້')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $document = Document::create([
                                'user_id' => $submitterId,
                                'basis_main_id' => $record->id,
                                'academic_year_id' => $academicYear->id,
                            ]);
                        }

                        DocumentFile::create([
                            'document_id' => $document->id,
                            'reference_no' => $data['reference_no'] ?? null,
                            'issued_date' => $data['issued_date'] ?? null,
                            'disk' => $data['disk'],
                            'path' => $data['path'],
                            'original_name' => $data['original_name'],
                            'mime_type' => $data['mime_type'],
                            'size' => $data['size'],
                        ]);
                    }),
                Action::make('view')
                    ->label('ເບິ່ງເອກະສານ')
                    ->color('gray')
                    ->icon(Heroicon::OutlinedEye)
                    ->visible(fn (BasisMain $record): bool => $record->documents->isNotEmpty())
                    ->url(fn (BasisMain $record): string => route('filament.admin.resources.documents.edit', $record->documents->first())),
            ])
            ->emptyStateHeading('ຍັງບໍ່ໄດ້ກຳນົດປີການສຶກສາປັດຈຸບັນ');
    }
}
