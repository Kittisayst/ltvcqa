<?php

namespace App\Filament\Resources\QaFrameworks\Pages;

use App\Filament\Resources\QaFrameworks\QaFrameworkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQaFramework extends EditRecord
{
    protected static string $resource = QaFrameworkResource::class;

    protected static ?string $navigationLabel = 'ແກ້ໄຂ';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
