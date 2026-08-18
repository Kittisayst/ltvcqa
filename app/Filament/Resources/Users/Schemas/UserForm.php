<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('ຊື່')
                    ->required(),
                TextInput::make('username')
                    ->label('ຊື່ຜູ້ໃຊ້')
                    ->required(),
                Select::make('department_id')
                    ->label('ພະແນກ/ພາກວິຊາ')
                    ->relationship('department', 'name'),
                Select::make('roles')
                    ->label('ບົດບາດ')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn (Role $record): string => match ($record->name) {
                        'super_admin' => 'ຜູ້ບໍລິຫານລະບົບ',
                        'assessor' => 'ຜູ້ປະເມີນ',
                        'department-staff' => 'ພະນັກງານພະແນກ',
                        default => $record->name,
                    }),
                TextInput::make('password')
                    ->label('ລະຫັດຜ່ານ')
                    ->password()
                    ->revealable()
                    ->confirmed()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                TextInput::make('password_confirmation')
                    ->label('ຢືນຢັນລະຫັດຜ່ານ')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(false),
            ]);
    }
}
