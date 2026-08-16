<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\BasisMain;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class MissingEvidenceTable extends TableWidget
{
    protected static ?string $heading = 'ຫຼັກຖານທີ່ຍັງບໍ່ໄດ້ສົ່ງ';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user() && ! auth()->user()->hasAnyRole(['super_admin', 'assessor']);
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $activeYear = AcademicYear::where('is_active', true)->first();

        return $table
            ->query(
                BasisMain::query()
                    ->when(
                        $activeYear,
                        fn (Builder $query) => $query->whereHas(
                            'indicator.standard',
                            fn (Builder $query) => $query->where('framework_id', $activeYear->framework_id)
                        )->whereDoesntHave(
                            'documents',
                            fn (Builder $query) => $query->where('academic_year_id', $activeYear->id)
                                ->whereHas('user', fn (Builder $query) => $query->where('department_id', $user->department_id))
                        ),
                        fn (Builder $query) => $query->whereRaw('1 = 0'),
                    )
            )
            ->columns([
                TextColumn::make('indicator.standard.name')
                    ->label('ມາດຕະຖານ'),
                TextColumn::make('indicator.name')
                    ->label('ຕົວຊີ້ວັດ')
                    ->wrap(),
                TextColumn::make('title')
                    ->label('ຫຼັກຖານ')
                    ->wrap()
                    ->badge()
                    ->color('danger'),
            ])
            ->emptyStateHeading($activeYear ? 'ສົ່ງຫຼັກຖານຄົບຖ້ວນແລ້ວ' : 'ຍັງບໍ່ໄດ້ກຳນົດປີການສຶກສາປັດຈຸບັນ')
            ->paginated(false);
    }
}
