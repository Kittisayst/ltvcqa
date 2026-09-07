<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Resources\Reports\ReportResource;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Indicator;
use App\Models\Report;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Assessment console: the framework's Standard -> Indicator checklist for a
 * chosen AcademicYear + Department, one row per Indicator, whether or not a
 * Report exists yet. Replaces the stock "list of Report rows" screen.
 */
class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    public function getTitle(): string
    {
        return 'ການປະເມີນຕົວຊີ້ວັດ';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    private function isDepartmentStaff(): bool
    {
        return Auth::user()?->hasRole('department-staff') ?? false;
    }

    private function canEvaluate(): bool
    {
        return Auth::user()?->hasAnyRole(['assessor', 'super_admin']) ?? false;
    }

    /**
     * Resolves (creating on first use) the Report for one Indicator in the
     * currently scoped department + academic year. The three ids are the
     * console's scope, so the Report is always framework-consistent and
     * unique by construction.
     */
    private function reportForIndicator(Indicator $indicator): Report
    {
        return Report::firstOrCreate([
            'indicator_id' => $indicator->id,
            'department_id' => $this->resolveDepartmentId(),
            'academic_year_id' => $this->resolveAcademicYearId(),
        ]);
    }

    private function resolveAcademicYearId(): ?int
    {
        return $this->tableFilters['academic_year_id']['value']
            ?? AcademicYear::active()?->id;
    }

    /**
     * department-staff are pinned to their own department; assessors and
     * super_admin choose one via the (visible) filter.
     */
    private function resolveDepartmentId(): ?int
    {
        if ($this->isDepartmentStaff()) {
            return Auth::user()->department_id;
        }

        return $this->tableFilters['department_id']['value'] ?? null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $yearId = $this->resolveAcademicYearId();
                $departmentId = $this->resolveDepartmentId();
                $year = $yearId ? AcademicYear::find($yearId) : null;

                $query = Indicator::query()
                    ->join('standards', 'standards.id', '=', 'indicators.standard_id')
                    ->select('indicators.*')
                    ->orderBy('standards.order')
                    ->orderBy('indicators.order')
                    ->with([
                        'standard',
                        'basisMains' => fn ($relation) => $relation->with([
                            'documents' => fn ($documents) => $documents
                                ->where('academic_year_id', $yearId)
                                ->whereHas('user', fn ($user) => $user->where('department_id', $departmentId)),
                        ]),
                        'reports' => fn ($relation) => $relation
                            ->where('department_id', $departmentId)
                            ->where('academic_year_id', $yearId)
                            ->with('assessor'),
                    ]);

                if (! $year || ! $departmentId) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->where('standards.framework_id', $year->framework_id);
            })
            ->groups([
                Group::make('standard.name')
                    ->label('ມາດຕະຖານ')
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(fn (Indicator $record): HtmlString => new HtmlString(
                        '<span style="font-size: 1.05rem; font-weight: 600; color: var(--amber-600);">'
                        .'ມາດຕະຖານທີ '.$record->standard->order.': '.e(Str::limit($record->standard->name, 120))
                        .'</span>'
                    ))
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('standards.order', $direction)),
            ])
            ->defaultGroup('standard.name')
            ->deferFilters(false)
            ->defaultPaginationPageOption(50)
            ->filters([
                SelectFilter::make('academic_year_id')
                    ->label('ປີການສຶກສາ')
                    ->options(fn (): array => AcademicYear::query()->orderByDesc('name')->pluck('name', 'id')->all())
                    ->default(fn (): ?int => AcademicYear::active()?->id)
                    ->selectablePlaceholder(false)
                    ->query(fn (Builder $query): Builder => $query),
                SelectFilter::make('department_id')
                    ->label('ພະແນກ/ພາກວິຊາ')
                    ->options(fn (): array => Department::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->visible(fn (): bool => ! $this->isDepartmentStaff())
                    ->searchable()
                    ->query(fn (Builder $query): Builder => $query),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('ຕົວຊີ້ວັດ')
                    ->state(fn (Indicator $record): string => "{$record->order}. {$record->name}")
                    ->wrap()
                    ->searchable(),
                TextColumn::make('evidence_progress')
                    ->label('ຄວາມຄືບໜ້າຫຼັກຖານ')
                    ->badge()
                    ->color('gray')
                    ->state(function (Indicator $record): string {
                        $total = $record->basisMains->count();
                        $withEvidence = $record->basisMains
                            ->filter(fn ($basisMain) => $basisMain->documents->isNotEmpty())
                            ->count();

                        return "{$withEvidence} / {$total} ຫຼັກຖານ";
                    }),
                TextColumn::make('assessment_status')
                    ->label('ສະຖານະ')
                    ->badge()
                    ->state(fn (Indicator $record): string => match ($record->reports->first()?->status) {
                        'submitted' => 'ສົ່ງແລ້ວ',
                        'approved' => 'ອະນຸມັດ',
                        'draft' => 'ຮ່າງ',
                        default => 'ຍັງບໍ່ໄດ້ປະເມີນ',
                    })
                    ->color(fn (Indicator $record): string => match ($record->reports->first()?->status) {
                        'submitted' => 'warning',
                        'approved' => 'success',
                        'draft' => 'gray',
                        default => 'danger',
                    }),
            ])
            ->recordActions([
                $this->evaluateAction(),
            ])
            ->emptyStateHeading(fn (): string => $this->emptyStateHeading());
    }

    private function evaluateAction(): Action
    {
        return Action::make('evaluate')
            ->label('ປະເມີນ')
            ->icon(Heroicon::OutlinedClipboardDocumentCheck)
            ->visible(fn (): bool => $this->canEvaluate())
            ->modalHeading('ປະເມີນຕົວຊີ້ວັດ')
            ->modalSubmitActionLabel('ບັນທຶກ')
            ->fillForm(function (Indicator $record): array {
                $report = $this->reportForIndicator($record);

                return [
                    'score' => $report->score,
                    'good_point' => $report->good_point,
                    'remain_point' => $report->remain_point,
                    'proposal' => $report->proposal,
                    'status' => in_array($report->status, ['draft', 'submitted'], true) ? $report->status : 'draft',
                ];
            })
            ->schema([
                Placeholder::make('context')
                    ->label('')
                    ->content(fn (Indicator $record): HtmlString => new HtmlString(implode('<br>', [
                        'ມາດຕະຖານ: '.e($record->standard->name),
                        'ຕົວຊີ້ວັດ: '.e($record->name),
                        'ພະແນກ/ພາກວິຊາ: '.e(Department::find($this->resolveDepartmentId())?->name ?? '-'),
                        'ປີການສຶກສາ: '.e(AcademicYear::find($this->resolveAcademicYearId())?->name ?? '-'),
                    ])))
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('ສະຖານະ')
                    ->options(['draft' => 'ຮ່າງ', 'submitted' => 'ສົ່ງແລ້ວ'])
                    ->default('draft')
                    ->required()
                    ->live(),
                TextInput::make('score')
                    ->label('ຄະແນນ')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->helperText('0–100')
                    ->required(fn (Get $get): bool => $get('status') !== 'draft'),
                Textarea::make('good_point')->label('ຈຸດດີ')->columnSpanFull(),
                Textarea::make('remain_point')->label('ຂໍ້ຄົງຄ້າງ')->columnSpanFull(),
                Textarea::make('proposal')->label('ຂໍ້ສະເໜີ')->columnSpanFull(),
            ])
            ->action(function (Indicator $record, array $data): void {
                $report = $this->reportForIndicator($record);

                if (Auth::user()->hasRole('assessor')) {
                    $data['assessor_id'] = Auth::id();
                }

                $report->update($data);
            });
    }

    private function emptyStateHeading(): string
    {
        $yearId = $this->resolveAcademicYearId();

        if (! $yearId) {
            return 'ເລືອກປີການສຶກສາ';
        }

        if (! $this->resolveDepartmentId()) {
            return 'ເລືອກພະແນກ/ພາກວິຊາ';
        }

        $year = AcademicYear::find($yearId);

        if ($year && ! $year->framework?->standards()->exists()) {
            return 'ຊຸດມາດຕະຖານຍັງບໍ່ມີໂຄງສ້າງ';
        }

        return 'ບໍ່ພົບຕົວຊີ້ວັດ';
    }
}
