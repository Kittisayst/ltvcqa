<?php

namespace App\Filament\Resources\QaFrameworks\Schemas;

use App\Models\QaFramework;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class QaFrameworkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('ໂຄງຮ່າງຫຼັກຖານປະກັນຄຸນນະພາບ')
                    ->schema([
                        TextInput::make('name')
                            ->label('ຊື່')
                            ->required()
                            ->maxLength(255),
                        Select::make('status')
                            ->label('ສະຖານະ')
                            ->options([
                                'draft' => 'ຮ່າງ',
                                'published' => 'ເຜີຍແຜ່',
                            ])
                            ->default('draft')
                            ->required()
                            ->rule(fn(?Model $record) => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                                if ($value !== 'published') {
                                    return;
                                }

                                $alreadyPublished = QaFramework::query()
                                    ->where('status', 'published')
                                    ->when($record, fn($query) => $query->whereKeyNot($record->getKey()))
                                    ->exists();

                                if ($alreadyPublished) {
                                    $fail('ມີຊຸດມາດຕະຖານອື່ນທີ່ເຜີຍແຜ່ຢູ່ແລ້ວ. ກະລຸນາປ່ຽນອັນນັ້ນເປັນຮ່າງກ່ອນ.');
                                }
                            }),
                    ])->columns(2)
            ])->columns(1);
    }
}
