<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Models\AcademicYear;
use App\Models\BasisMain;
use App\Models\Document;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('academic_year_id')
                    ->label('ປີການສຶກສາ')
                    ->relationship('academicYear', 'name')
                    ->live()
                    ->afterStateUpdated(function (callable $set): void {
                        $set('standard_id', null);
                        $set('indicator_id', null);
                        $set('basis_main_id', null);
                    })
                    ->required(),
                Select::make('user_id')
                    ->label('ຜູ້ສົ່ງ')
                    ->relationship(
                        'user',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->when(
                            ! auth()->user()?->hasRole('super_admin'),
                            fn ($query) => $query->where('department_id', auth()->user()?->department_id)
                        )
                    )
                    ->default(fn () => auth()->id())
                    ->required(),
                Select::make('standard_id')
                    ->label('ມາດຕະຖານ')
                    ->options(function (Get $get): array {
                        $academicYear = AcademicYear::find($get('academic_year_id'));

                        if (! $academicYear) {
                            return [];
                        }

                        return Standard::where('framework_id', $academicYear->framework_id)
                            ->orderBy('order')
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->live()
                    ->dehydrated(false)
                    ->disabled(fn (Get $get): bool => blank($get('academic_year_id')))
                    ->afterStateHydrated(function (Component $component, ?Document $record): void {
                        if ($record?->basisMain) {
                            $component->state($record->basisMain->indicator->standard_id);
                        }
                    })
                    ->afterStateUpdated(function (callable $set): void {
                        $set('indicator_id', null);
                        $set('basis_main_id', null);
                    })
                    ->required()
                    ->columnSpanFull(),
                Select::make('indicator_id')
                    ->label('ຕົວຊີ້ວັດ')
                    ->options(function (Get $get): array {
                        $standardId = $get('standard_id');

                        if (! $standardId) {
                            return [];
                        }

                        return Indicator::where('standard_id', $standardId)
                            ->orderBy('order')
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->live()
                    ->dehydrated(false)
                    ->disabled(fn (Get $get): bool => blank($get('standard_id')))
                    ->afterStateHydrated(function (Component $component, ?Document $record): void {
                        if ($record?->basisMain) {
                            $component->state($record->basisMain->indicator_id);
                        }
                    })
                    ->afterStateUpdated(fn (callable $set) => $set('basis_main_id', null))
                    ->required()
                    ->columnSpanFull(),
                Select::make('basis_main_id')
                    ->label('ຫຼັກຖານ')
                    ->options(function (Get $get): array {
                        $indicatorId = $get('indicator_id');

                        if (! $indicatorId) {
                            return [];
                        }

                        return BasisMain::where('indicator_id', $indicatorId)
                            ->orderBy('order')
                            ->pluck('title', 'id')
                            ->all();
                    })
                    ->disabled(fn (Get $get): bool => blank($get('indicator_id')))
                    ->helperText('ຕ້ອງເລືອກປີການສຶກສາ, ມາດຕະຖານ ແລະ ຕົວຊີ້ວັດກ່ອນ, ຈຶ່ງຈະສະແດງລາຍການຫຼັກຖານ')
                    ->required()
                    ->columnSpanFull()
                    ->searchable()
                    ->rule(function (Get $get, ?Document $record): Closure {
                        return function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                            if ($record && $value === $record->basis_main_id) {
                                return;
                            }

                            $academicYear = AcademicYear::find($get('academic_year_id'));

                            if (! $academicYear || ! $value) {
                                return;
                            }

                            $matches = BasisMain::whereKey($value)
                                ->whereHas('indicator.standard', fn ($query) => $query->where('framework_id', $academicYear->framework_id))
                                ->exists();

                            if (! $matches) {
                                $fail('ຫຼັກຖານນີ້ບໍ່ໄດ້ຢູ່ໃນຊຸດມາດຕະຖານຂອງປີການສຶກສາທີ່ເລືອກ.');
                            }
                        };
                    })
                    ->rule(function (Get $get, ?Document $record): Closure {
                        return function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                            $academicYearId = $get('academic_year_id');
                            $departmentId = User::find($get('user_id'))?->department_id;

                            if (! $value || ! $academicYearId || ! $departmentId) {
                                return;
                            }

                            $duplicateExists = Document::query()
                                ->where('basis_main_id', $value)
                                ->where('academic_year_id', $academicYearId)
                                ->whereHas('user', fn ($query) => $query->where('department_id', $departmentId))
                                ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($duplicateExists) {
                                $fail('ພະແນກນີ້ໄດ້ສົ່ງຫຼັກຖານນີ້ສຳລັບປີການສຶກສານີ້ໄປແລ້ວ. ກະລຸນາເປີດເອກະສານເກົ່າເພື່ອເພີ່ມໄຟລ໌ແທນ.');
                            }
                        };
                    }),
            ]);
    }
}
