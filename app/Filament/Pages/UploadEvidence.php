<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\ConfiguresEvidenceFileUpload;
use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
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
use Illuminate\Support\Str;
use UnitEnum;

class UploadEvidence extends Page implements HasTable
{
    use ConfiguresEvidenceFileUpload;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCloudArrowUp;

    protected static ?string $navigationLabel = 'ອັບໂຫຼດຫຼັກຖານ';

    protected static string|UnitEnum|null $navigationGroup = 'ຫຼັກຖານ ແລະ ເອກະສານ';

    protected string $view = 'filament.pages.upload-evidence';

    public function getTitle(): string
    {
        $framework = $this->currentAcademicYear()?->framework;

        return $framework?->status === 'published'
            ? "ອັບໂຫຼດຫຼັກຖານ - {$framework->name}"
            : 'ອັບໂຫຼດຫຼັກຖານ';
    }

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
                $isFrameworkPublished = $academicYear?->framework?->status === 'published';

                /** @var Builder $query */
                $query = BasisMain::query()
                    ->when(
                        $academicYear && $isFrameworkPublished,
                        fn (Builder $query) => $query->whereHas(
                            'indicator.standard',
                            fn (Builder $query) => $query->where('framework_id', $academicYear->framework_id)
                        ),
                        fn (Builder $query): Builder => $query->whereRaw('1 = 0'),
                    )
                    ->with([
                        'indicator.standard',
                        'documents' => fn ($query) => $query
                            ->where('academic_year_id', $academicYear?->id)
                            ->whereHas('user', fn (Builder $query) => $query->where('department_id', $departmentId))
                            ->with('files'),
                    ])
                    ->orderBy('indicator_id')
                    ->orderBy('order');

                return $query;
            })
            ->groups([
                Group::make('indicator.name')
                    ->label('ຕົວຊີ້ວັດ')
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(fn (BasisMain $record): HtmlString => new HtmlString(
                        '<div>'
                        .'<div style="display: block; font-size: 1.25rem; font-weight: 500; color: var(--amber-600);">ມາດຕະຖານທີ '.$record->indicator->standard->order.': '.e($record->indicator->standard->name).'</div>'
                        .'<div title="'.e($record->indicator->name).'" style="display: block; font-size: 1.125rem; font-weight: 600; color: var(--teal-600);">'.e(Str::limit($record->indicator->name, 100)).'</div>'
                        .'</div>'
                    ))
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('indicator_id', $direction)),
            ])
            ->defaultGroup('indicator.name')
            ->defaultPaginationPageOption(50)
            ->filters([
                SelectFilter::make('department_id')
                    ->label('ພະແນກ/ພາກວິຊາ')
                    ->options(fn () => Department::orderBy('name')->pluck('name', 'id'))
                    ->visible($isSuperAdmin)
                    ->query(fn (Builder $query) => $query),
                SelectFilter::make('standard_id')
                    ->label('ມາດຕະຖານ')
                    ->options(fn (): array => Standard::query()
                        ->when($this->currentAcademicYear(), fn (Builder $query, AcademicYear $year) => $query->where('framework_id', $year->framework_id))
                        ->orderBy('order')
                        ->get()
                        ->mapWithKeys(fn (Standard $standard): array => [$standard->id => "ມາດຕະຖານທີ {$standard->order}: {$standard->name}"])
                        ->all())
                    ->modifyFormFieldUsing(fn (Select $field): Select => $field
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('../indicator_id.value', null)))
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $query, string $value) => $query->whereHas(
                            'indicator.standard',
                            fn (Builder $query) => $query->where('standards.id', $value)
                        ),
                    )),
                SelectFilter::make('indicator_id')
                    ->label('ຕົວຊີ້ວັດ')
                    ->options(fn (): array => Indicator::query()
                        ->when(
                            $this->tableFilters['standard_id']['value'] ?? null,
                            fn (Builder $query, string $standardId) => $query->where('standard_id', $standardId),
                            fn (Builder $query) => $query->when(
                                $this->currentAcademicYear(),
                                fn (Builder $query, AcademicYear $year) => $query->whereHas(
                                    'standard',
                                    fn (Builder $query) => $query->where('framework_id', $year->framework_id)
                                ),
                            ),
                        )
                        ->orderBy('order')
                        ->get()
                        ->mapWithKeys(fn (Indicator $indicator): array => [$indicator->id => $indicator->name])
                        ->all()),
            ])
            ->deferFilters(false)
            ->persistFiltersInSession()
            ->columns([
                TextColumn::make('title')
                    ->label('ຫຼັກຖານ')
                    ->state(fn (BasisMain $record): string => "{$record->order}. {$record->title}")
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
                    ->state(fn (BasisMain $record): array => $record->documents->first()?->files
                        ->pluck('reference_no')
                        ->filter()
                        ->values()
                        ->all() ?? [])
                    ->badge()
                    ->wrap(3)
                    ->color('gray'),
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
                    ->url(fn (BasisMain $record): string => route('filament.admin.resources.documents.view', $record->documents->first())),
            ])
            ->emptyStateHeading(fn (): string => $this->currentAcademicYear()
                ? 'ຊຸດມາດຕະຖານຂອງປີການສຶກສານີ້ຍັງບໍ່ໄດ້ເຜີຍແຜ່'
                : 'ຍັງບໍ່ໄດ້ກຳນົດປີການສຶກສາປັດຈຸບັນ');
    }
}
