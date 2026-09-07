<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Resources\Reports\ReportResource;
use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Department;
use App\Models\DocumentFile;
use App\Models\Indicator;
use App\Models\Report;
use App\Models\Standard;
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
use Illuminate\Support\Facades\Storage;
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

    /** Report status => Lao badge label; a missing status reads as "not assessed". */
    private const STATUS_LABELS = [
        'draft' => 'ຮ່າງ',
        'submitted' => 'ສົ່ງແລ້ວ',
        'approved' => 'ອະນຸມັດ',
    ];

    private const NOT_ASSESSED_LABEL = 'ຍັງບໍ່ໄດ້ປະເມີນ';

    /** @var array<int, HtmlString> group-title cache, keyed by standard id */
    private array $standardGroupTitleCache = [];

    private function statusLabel(?string $status): string
    {
        return self::STATUS_LABELS[$status] ?? self::NOT_ASSESSED_LABEL;
    }

    private function statusColor(?string $status): string
    {
        return match ($status) {
            'submitted' => 'warning',
            'approved' => 'success',
            'draft' => 'gray',
            default => 'danger',
        };
    }

    /** Reports in the current department + academic-year scope. Never creates. */
    private function scopedReports(): Builder
    {
        return Report::query()
            ->where('department_id', $this->resolveDepartmentId())
            ->where('academic_year_id', $this->resolveAcademicYearId());
    }

    /**
     * Constrains a `reports` relation/query to the current scope. Works for
     * both eager-load closures (Relation) and whereHas closures (Builder).
     *
     * @template T of \Illuminate\Database\Eloquent\Relations\Relation|Builder
     *
     * @param  T  $relation
     * @return T
     */
    private function constrainReportsToScope($relation)
    {
        return $relation
            ->where('department_id', $this->resolveDepartmentId())
            ->where('academic_year_id', $this->resolveAcademicYearId());
    }

    /**
     * Constrains a `documents` relation/query to the scoped department + year.
     *
     * @template T of \Illuminate\Database\Eloquent\Relations\Relation|Builder
     *
     * @param  T  $relation
     * @return T
     */
    private function constrainDocumentsToScope($relation)
    {
        return $relation
            ->where('academic_year_id', $this->resolveAcademicYearId())
            ->whereHas('user', fn (Builder $user) => $user->where('department_id', $this->resolveDepartmentId()));
    }

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

    /**
     * The Report for one Indicator in the current scope, without creating
     * one — uses the eager-loaded relation on a table row where present.
     */
    private function existingReportForIndicator(Indicator $indicator): ?Report
    {
        if ($indicator->relationLoaded('reports')) {
            return $indicator->reports->first();
        }

        return $this->scopedReports()->where('indicator_id', $indicator->id)->first();
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
                            'documents' => fn ($documents) => $this->constrainDocumentsToScope($documents),
                        ]),
                        'reports' => fn ($relation) => $this->constrainReportsToScope($relation)->with('assessor'),
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
                    ->getTitleFromRecordUsing(fn (Indicator $record): HtmlString => $this->standardGroupTitle($record->standard))
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('standards.order', $direction)),
            ])
            ->defaultGroup('standard.name')
            ->deferFilters(false)
            ->defaultPaginationPageOption(50)
            ->description(fn (): ?string => $this->summaryLine())
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
                SelectFilter::make('assessment_state')
                    ->label('ສະຖານະການປະເມີນ')
                    ->options([
                        'not_assessed' => 'ຍັງບໍ່ໄດ້ປະເມີນ',
                        'draft' => 'ຮ່າງ',
                        'submitted' => 'ສົ່ງແລ້ວ',
                        'approved' => 'ອະນຸມັດ',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        if (blank($value)) {
                            return $query;
                        }

                        if ($value === 'not_assessed') {
                            return $query->whereDoesntHave('reports', fn (Builder $relation) => $this->constrainReportsToScope($relation));
                        }

                        return $query->whereHas('reports', fn (Builder $relation) => $this->constrainReportsToScope($relation)->where('status', $value));
                    }),
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
                    ->state(fn (Indicator $record): string => $this->statusLabel($record->reports->first()?->status))
                    ->color(fn (Indicator $record): string => $this->statusColor($record->reports->first()?->status)),
                TextColumn::make('score')
                    ->label('ຄະແນນ')
                    ->state(fn (Indicator $record): string => ($score = $record->reports->first()?->score) !== null
                        ? (string) $score
                        : '-')
                    ->alignEnd(),
                TextColumn::make('assessor')
                    ->label('ຜູ້ປະເມີນ')
                    ->state(fn (Indicator $record): string => $record->reports->first()?->assessor?->name ?? '-'),
                TextColumn::make('updated_at')
                    ->label('ອັບເດດຫຼ້າສຸດ')
                    ->state(fn (Indicator $record) => $record->reports->first()?->updated_at)
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                $this->evaluateAction(),
                $this->approveAction(),
            ])
            ->emptyStateHeading(fn (): string => $this->emptyStateHeading());
    }

    private function approveAction(): Action
    {
        return Action::make('approve')
            ->label('ອະນຸມັດ')
            ->icon(Heroicon::OutlinedCheckBadge)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('ອະນຸມັດບົດລາຍງານປະເມີນ')
            ->visible(fn (Indicator $record): bool => (Auth::user()?->hasRole('super_admin') ?? false)
                && $this->existingReportForIndicator($record)?->status === 'submitted')
            ->action(function (Indicator $record): void {
                $report = $this->existingReportForIndicator($record);

                if ($report?->status === 'submitted') {
                    $report->update(['status' => 'approved']);
                }
            });
    }

    private function evaluateAction(): Action
    {
        return Action::make('evaluate')
            ->label(fn (): string => $this->canEvaluate() ? 'ປະເມີນ' : 'ເບິ່ງການປະເມີນ')
            ->icon(Heroicon::OutlinedClipboardDocumentCheck)
            ->visible(fn (): bool => $this->canEvaluate() || $this->isDepartmentStaff())
            ->modalHeading('ປະເມີນຕົວຊີ້ວັດ')
            ->modalSubmitActionLabel('ບັນທຶກ')
            ->modalSubmitAction(fn () => $this->canEvaluate() ? null : false)
            ->disabledForm(fn (): bool => ! $this->canEvaluate())
            // Read-only: never create a Report just by opening the modal —
            // an existing one pre-fills, otherwise the fields start blank.
            // The Report is only created on save, in ->action().
            ->fillForm(function (Indicator $record): array {
                $report = $this->existingReportForIndicator($record);

                return [
                    'score' => $report?->score,
                    'good_point' => $report?->good_point,
                    'remain_point' => $report?->remain_point,
                    'proposal' => $report?->proposal,
                    'status' => in_array($report?->status, ['draft', 'submitted'], true) ? $report->status : 'draft',
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
                    ->rule('decimal:0,2')
                    ->helperText('0–100 (ທົດສະນິຍົມບໍ່ເກີນ 2 ຕຳແໜ່ງ)')
                    ->required(fn (Get $get): bool => $get('status') !== 'draft'),
                Textarea::make('good_point')->label('ຈຸດດີ')->columnSpanFull(),
                Textarea::make('remain_point')->label('ຂໍ້ຄົງຄ້າງ')->columnSpanFull(),
                Textarea::make('proposal')->label('ຂໍ້ສະເໜີ')->columnSpanFull(),
                Placeholder::make('evidence')
                    ->label('ຫຼັກຖານທີ່ພະແນກອັບໂຫຼດ')
                    ->content(fn (Indicator $record): HtmlString => $this->evidencePanelContent($record))
                    ->columnSpanFull(),
            ])
            ->action(function (Indicator $record, array $data): void {
                if (! $this->canEvaluate()) {
                    return;
                }

                $report = $this->reportForIndicator($record);

                if (Auth::user()->hasRole('assessor')) {
                    $data['assessor_id'] = Auth::id();
                }

                $report->update($data);
            });
    }

    private function standardGroupTitle(Standard $standard): HtmlString
    {
        return $this->standardGroupTitleCache[$standard->id] ??= $this->buildStandardGroupTitle($standard);
    }

    private function buildStandardGroupTitle(Standard $standard): HtmlString
    {
        $indicatorCount = $standard->indicators()->count();

        $average = $this->scopedReports()
            ->whereHas('indicator', fn (Builder $relation) => $relation->where('standard_id', $standard->id))
            ->whereNotNull('score')
            ->avg('score');

        return new HtmlString(
            '<span style="font-size: 1.05rem; font-weight: 600; color: var(--amber-600);">'
            .'ມາດຕະຖານທີ '.$standard->order.': '.e(Str::limit($standard->name, 120))
            .'</span> <span style="color: var(--gray-500); font-size: 0.85rem;">('
            .$indicatorCount.' ຕົວຊີ້ວັດ · ຄະແນນສະເລ່ຍ '.($average !== null ? number_format((float) $average, 1) : '-')
            .')</span>'
        );
    }

    /**
     * "assessed X / Y · approved Z · avg score" for the current scope, or
     * null while the scope is incomplete.
     */
    private function summaryLine(): ?string
    {
        $yearId = $this->resolveAcademicYearId();
        $departmentId = $this->resolveDepartmentId();
        $year = $yearId ? AcademicYear::find($yearId) : null;

        if (! $year || ! $departmentId) {
            return null;
        }

        $totalIndicators = Indicator::query()
            ->whereHas('standard', fn (Builder $relation) => $relation->where('framework_id', $year->framework_id))
            ->count();

        $reports = $this->scopedReports()->get(['status', 'score']);

        $averageScore = $reports->whereNotNull('score')->avg('score');

        return sprintf(
            'ປະເມີນແລ້ວ %d/%d ຕົວຊີ້ວັດ · ອະນຸມັດ %d · ຄະແນນສະເລ່ຍ %s',
            $reports->count(),
            $totalIndicators,
            $reports->where('status', 'approved')->count(),
            $averageScore !== null ? number_format((float) $averageScore, 1) : '-',
        );
    }

    /**
     * Read-only rundown of the scoped department's evidence for this
     * Indicator: every BasisMain, its uploaded files as new-tab links, and
     * a greyed marker where a BasisMain has no Document yet.
     */
    private function evidencePanelContent(Indicator $record): HtmlString
    {
        $basisMains = $record->basisMains()
            ->orderBy('order')
            ->with(['documents' => fn ($documents) => $this->constrainDocumentsToScope($documents)->with('files')])
            ->get();

        if ($basisMains->isEmpty()) {
            return new HtmlString('<p style="color: var(--gray-500);">ຕົວຊີ້ວັດນີ້ຍັງບໍ່ມີຫຼັກຖານໃນໂຄງສ້າງ</p>');
        }

        $blocks = $basisMains->map(function (BasisMain $basisMain): string {
            $files = $basisMain->documents->flatMap->files;

            $body = $files->isEmpty()
                ? '<span style="color: var(--gray-400);">ຍັງບໍ່ມີຫຼັກຖານ</span>'
                : $files->map(fn (DocumentFile $file): string => sprintf(
                    '<a href="%s" target="_blank" rel="noopener" style="color: var(--primary-600); text-decoration: underline;">%s</a>',
                    e($this->fileUrl($file)),
                    e($file->original_name ?: ($file->reference_no ?: 'ໄຟລ໌')),
                ))->implode(' &middot; ');

            return '<div style="margin-bottom: 0.5rem;">'
                .'<div style="font-weight: 500;">'.e($basisMain->order.'. '.$basisMain->title).'</div>'
                .'<div style="padding-inline-start: 1rem;">'.$body.'</div>'
                .'</div>';
        })->implode('');

        return new HtmlString($blocks);
    }

    private function fileUrl(DocumentFile $file): string
    {
        try {
            return Storage::disk($file->disk)->temporaryUrl($file->path, now()->addMinutes(5));
        } catch (\Throwable) {
            try {
                return Storage::disk($file->disk)->url($file->path);
            } catch (\Throwable) {
                return '#';
            }
        }
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
