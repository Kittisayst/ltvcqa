<?php

namespace App\Filament\Resources\AcademicYears;

use App\Filament\Resources\AcademicYears\Pages\ManageAcademicYears;
use App\Models\AcademicYear;
use App\Models\QaFramework;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'ປີການສຶກສາ';

    protected static string|UnitEnum|null $navigationGroup = 'ຂໍ້ມູນພື້ນຖານ';

    protected static ?string $modelLabel = 'ປີການສຶກສາ';

    protected static ?string $pluralModelLabel = 'ປີການສຶກສາ';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('framework_id')
                    ->label('ຊຸດມາດຕະຖານ')
                    ->relationship('framework', 'name', fn ($query) => $query->where('status', 'published'))
                    ->disabledOn('edit')
                    ->required(),
                TextInput::make('name')
                    ->label('ຊື່')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('framework.name')
                    ->label('ຊຸດມາດຕະຖານ')
                    ->badge(),
                IconColumn::make('is_active')
                    ->label('ປັດຈຸບັນ')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('activate')
                    ->label('ຕັ້ງເປັນປັດຈຸບັນ')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('ຊຸດມາດຕະຖານຂອງປີນີ້ຈະຖືກເຜີຍແຜ່ອັດຕະໂນມັດ (ຖ້າຍັງເປັນຮ່າງ), ຊຸດອື່ນທີ່ເຜີຍແຜ່ຢູ່ຈະປ່ຽນເປັນຮ່າງ, ແລະ ປີການສຶກສາອື່ນຈະປິດການເປັນປັດຈຸບັນ.')
                    ->visible(fn (AcademicYear $record): bool => ! $record->is_active)
                    ->action(function (AcademicYear $record): void {
                        DB::transaction(function () use ($record): void {
                            QaFramework::query()
                                ->where('status', 'published')
                                ->whereKeyNot($record->framework_id)
                                ->update(['status' => 'draft']);

                            $record->framework->update(['status' => 'published']);

                            AcademicYear::query()
                                ->whereKeyNot($record->getKey())
                                ->update(['is_active' => false]);

                            $record->update(['is_active' => true]);
                        });

                        AcademicYear::forgetActiveCache();

                        Notification::make()
                            ->title('ຕັ້ງເປັນປີການສຶກສາປັດຈຸບັນແລ້ວ')
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

    public static function getPages(): array
    {
        return [
            'index' => ManageAcademicYears::route('/'),
        ];
    }
}
