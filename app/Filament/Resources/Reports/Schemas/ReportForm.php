<?php

namespace App\Filament\Resources\Reports\Schemas;

use App\Models\AcademicYear;
use App\Models\Indicator;
use App\Models\Report;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        $isSuperAdmin = fn (): bool => auth()->user()?->hasRole('super_admin') ?? false;
        $canEvaluate = fn (): bool => auth()->user()?->hasAnyRole(['super_admin', 'assessor']) ?? false;

        return $schema
            ->components([
                Select::make('academic_year_id')
                    ->label('ປີການສຶກສາ')
                    ->relationship('academicYear', 'name')
                    ->live()
                    ->afterStateUpdated(fn (Select $component, callable $set) => $set('indicator_id', null))
                    ->disabled(fn (string $operation) => $operation === 'edit' && ! $isSuperAdmin())
                    ->required(),
                Select::make('department_id')
                    ->label('ພະແນກ/ພາກວິຊາ')
                    ->relationship('department', 'name')
                    ->default(fn () => auth()->user()?->department_id)
                    ->disabled(fn (): bool => ! $isSuperAdmin())
                    ->required(),
                Select::make('indicator_id')
                    ->label('ຕົວຊີ້ວັດ')
                    ->options(function (Get $get): array {
                        $academicYear = AcademicYear::find($get('academic_year_id'));

                        if (! $academicYear) {
                            return [];
                        }

                        return Indicator::whereHas(
                            'standard',
                            fn ($query) => $query->where('framework_id', $academicYear->framework_id)
                        )->pluck('name', 'id')->all();
                    })
                    ->disabled(fn (Get $get, string $operation): bool => blank($get('academic_year_id'))
                        || ($operation === 'edit' && ! $isSuperAdmin()))
                    ->helperText('ຕ້ອງເລືອກປີການສຶກສາກ່ອນ, ຈະສະແດງສະເພາະຕົວຊີ້ວັດຂອງຊຸດມາດຕະຖານທີ່ໃຊ້ໃນປີນັ້ນ')
                    ->required()
                    ->searchable()
                    ->rule(function (Get $get, ?Report $record): Closure {
                        return function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                            // Unchanged from the saved record (e.g. the field is locked for
                            // this role) — don't re-validate data that predates this rule.
                            if ($record && $value === $record->indicator_id) {
                                return;
                            }

                            $academicYear = AcademicYear::find($get('academic_year_id'));

                            if (! $academicYear || ! $value) {
                                return;
                            }

                            $matches = Indicator::whereKey($value)
                                ->whereHas('standard', fn ($query) => $query->where('framework_id', $academicYear->framework_id))
                                ->exists();

                            if (! $matches) {
                                $fail('ຕົວຊີ້ວັດນີ້ບໍ່ໄດ້ຢູ່ໃນຊຸດມາດຕະຖານຂອງປີການສຶກສາທີ່ເລືອກ.');
                            }
                        };
                    })
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn ($rule, Get $get) => $rule
                            ->where('department_id', $get('department_id'))
                            ->where('academic_year_id', $get('academic_year_id'))
                    )
                    ->validationMessages([
                        'unique' => 'ບົດລາຍງານຂອງຕົວຊີ້ວັດ, ພະແນກ ແລະ ປີການສຶກສານີ້ ມີຢູ່ແລ້ວ.',
                    ]),
                Select::make('assessor_id')
                    ->label('ຜູ້ປະເມີນ')
                    ->relationship(
                        'assessor',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->whereHas(
                            'roles',
                            fn ($query) => $query->where('name', 'assessor')
                        ),
                    )
                    ->default(fn () => auth()->user()?->hasRole('assessor') ? auth()->id() : null)
                    ->disabled(fn (): bool => ! $canEvaluate())
                    ->searchable()
                    ->preload(),
                Select::make('status')
                    ->label('ສະຖານະ')
                    ->options([
                        'draft' => 'ຮ່າງ',
                        'submitted' => 'ສົ່ງແລ້ວ',
                        'approved' => 'ອະນຸມັດ',
                    ])
                    ->default('draft')
                    ->disabled(fn (): bool => ! $canEvaluate())
                    ->required(),
                TextInput::make('score')
                    ->label('ຄະແນນ')
                    ->numeric()
                    ->maxValue(100)
                    ->disabled(fn (): bool => ! $canEvaluate()),
                Textarea::make('good_point')
                    ->label('ຈຸດດີ')
                    ->disabled(fn (): bool => ! $canEvaluate())
                    ->columnSpanFull(),
                Textarea::make('remain_point')
                    ->label('ຂໍ້ຄົງຄ້າງ')
                    ->disabled(fn (): bool => ! $canEvaluate())
                    ->columnSpanFull(),
                Textarea::make('proposal')
                    ->label('ຂໍ້ສະເໜີ')
                    ->disabled(fn (): bool => ! $canEvaluate())
                    ->columnSpanFull(),
            ]);
    }
}
