<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Models\Document;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class DocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('ຂໍ້ມູນທົ່ວໄປ')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('academicYear.name')
                            ->label('ປີການສຶກສາ')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->badge(),
                        TextEntry::make('user.name')
                            ->label('ຜູ້ສົ່ງ')
                            ->icon(Heroicon::OutlinedUser),
                        TextEntry::make('user.department.name')
                            ->label('ພະແນກ/ພາກວິຊາ')
                            ->icon(Heroicon::OutlinedBuildingOffice2)
                            ->badge()
                            ->color('gray'),
                    ]),
                Section::make('ຫຼັກຖານທີ່ສົ່ງ')
                    ->schema([
                        TextEntry::make('basisMain.indicator.standard.name')
                            ->label('ມາດຕະຖານ')
                            ->formatStateUsing(fn (string $state, Document $record): string => 'ມາດຕະຖານທີ '.$record->basisMain->indicator->standard->order.': '.$state)
                            ->weight(FontWeight::Medium)
                            ->color('amber'),
                        TextEntry::make('basisMain.indicator.name')
                            ->label('ຕົວຊີ້ວັດ')
                            ->color('teal')
                            ->formatStateUsing(fn (string $state, Document $record): string => 'ຕົວຊີ້ວັດທີ '.$record->basisMain->indicator->order.': '.$state)
                            ->weight(FontWeight::Medium),
                        TextEntry::make('basisMain.title')
                            ->label('ຫຼັກຖານ')
                            ->formatStateUsing(fn (string $state, Document $record): string => 'ຫຼັກຖານທີ '.$record->basisMain->order.': '.$state)
                            ->color('primary')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large),
                    ]),
            ])->columns(1);
    }
}
