<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestDocuments extends TableWidget
{
    protected static ?string $heading = 'ເອກະສານລ່າສຸດ';

     protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Document::query()->limit(10))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.department.name')
                    ->label('ພະແນກ/ພາກວິຊາ')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('basisMain.title')
                    ->label('ຫຼັກຖານ')
                    ->wrap(),
                TextColumn::make('academicYear.name')
                    ->label('ປີການສຶກສາ')
                    ->badge(),
                TextColumn::make('files_count')
                    ->label('ຈຳນວນໄຟລ໌ແນບ')
                    ->counts('files')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('ສ້າງເມື່ອ')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('ເບິ່ງ')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn (Document $record): string => DocumentResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
