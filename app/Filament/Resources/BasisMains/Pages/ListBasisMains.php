<?php

namespace App\Filament\Resources\BasisMains\Pages;

use App\Filament\Resources\BasisMains\BasisMainResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBasisMains extends ListRecords
{
    protected static string $resource = BasisMainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
