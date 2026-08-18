<?php

namespace App\Filament\Resources\QaFrameworks\Tables;

use App\Filament\Resources\QaFrameworks\QaFrameworkResource;
use App\Models\BasisMain;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class QaFrameworksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('ຊື່')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('ສະຖານະ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'ເຜີຍແຜ່',
                        default => 'ຮ່າງ',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('standards_count')
                    ->label('ຈຳນວນມາດຕະຖານ')
                    ->counts('standards')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('ສ້າງເມື່ອ')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('ແກ້ໄຂເມື່ອ')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('manageStructure')
                    ->label('ຈັດການໂຄງສ້າງ')
                    ->icon(Heroicon::OutlinedListBullet)
                    ->color('gray')
                    ->url(fn (QaFramework $record): string => QaFrameworkResource::getUrl('structure', ['record' => $record])),
                Action::make('copyFrom')
                    ->label('ຄັດລອກຈາກ...')
                    ->icon(Heroicon::OutlinedDocumentDuplicate)
                    ->color('gray')
                    ->visible(fn (QaFramework $record): bool => $record->status === 'draft')
                    ->schema([
                        Select::make('source_framework_id')
                            ->label('ຈາກຊຸດມາດຕະຖານ')
                            ->options(fn (QaFramework $record) => QaFramework::query()
                                ->whereKeyNot($record->getKey())
                                ->pluck('name', 'id'))
                            ->live()
                            ->required(),
                        CheckboxList::make('standard_ids')
                            ->label('ເລືອກມາດຕະຖານ')
                            ->options(function (Get $get, QaFramework $record): array {
                                $sourceFrameworkId = $get('source_framework_id');

                                if (! $sourceFrameworkId) {
                                    return [];
                                }

                                $existingNames = Standard::query()
                                    ->where('framework_id', $record->id)
                                    ->pluck('name');

                                return Standard::query()
                                    ->where('framework_id', $sourceFrameworkId)
                                    ->whereNotIn('name', $existingNames)
                                    ->orderBy('order')
                                    ->pluck('name', 'id')
                                    ->all();
                            })
                            ->columns(1)
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (QaFramework $record, array $data): void {
                        DB::transaction(function () use ($record, $data): void {
                            $standards = Standard::query()
                                ->with('indicators.basisMains')
                                ->whereIn('id', $data['standard_ids'])
                                ->orderBy('order')
                                ->get();

                            $order = Standard::query()->where('framework_id', $record->id)->max('order') ?? 0;

                            foreach ($standards as $standard) {
                                $newStandard = Standard::create([
                                    'framework_id' => $record->id,
                                    'name' => $standard->name,
                                    'order' => ++$order,
                                ]);

                                foreach ($standard->indicators as $indicator) {
                                    $newIndicator = Indicator::create([
                                        'standard_id' => $newStandard->id,
                                        'name' => $indicator->name,
                                        'order' => $indicator->order,
                                    ]);

                                    foreach ($indicator->basisMains as $basisMain) {
                                        BasisMain::create([
                                            'indicator_id' => $newIndicator->id,
                                            'title' => $basisMain->title,
                                            'order' => $basisMain->order,
                                        ]);
                                    }
                                }
                            }
                        });

                        Notification::make()
                            ->title('ຄັດລອກມາດຕະຖານສຳເລັດແລ້ວ')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
