<?php

namespace App\Filament\Resources\QaFrameworks\Pages;

use App\Filament\Resources\QaFrameworks\QaFrameworkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQaFrameworks extends ListRecords
{
    protected static string $resource = QaFrameworkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
