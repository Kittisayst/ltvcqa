<?php

namespace App\Filament\Resources\BasisMains\Pages;

use App\Filament\Resources\BasisMains\BasisMainResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBasisMain extends EditRecord
{
    protected static string $resource = BasisMainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
