<?php

namespace App\Filament\Resources\QaFrameworks\Pages;

use App\Filament\Resources\QaFrameworks\QaFrameworkResource;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\QueryException;

class ManageStructure extends EditRecord
{
    protected static string $resource = QaFrameworkResource::class;

    protected static ?string $title = 'ຈັດການໂຄງສ້າງ';

    protected static ?string $navigationLabel = 'ຈັດການໂຄງສ້າງ';

    // protected Width|string|null $maxContentWidth = Width::Full;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('')
                    ->schema([
                        Repeater::make('standards')
                            ->label('ມາດຕະຖານ')
                            ->relationship()
                            ->orderColumn('order')
                            ->extraAttributes(['style' => 'border-inline-start: 4px solid rgb(251 191 36); padding-inline-start: 1rem;'])
                            ->schema([
                                TextInput::make('name')
                                    ->label('ຊື່ມາດຕະຖານ')
                                    ->required()
                                    ->columnSpanFull(),
                                Repeater::make('indicators')
                                    ->label('ຕົວຊີ້ວັດ')
                                    ->relationship()
                                    ->orderColumn('order')
                                    ->extraAttributes(['style' => 'border-inline-start: 4px solid rgb(56 189 248); padding-inline-start: 1rem;'])
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('ຊື່ຕົວຊີ້ວັດ')
                                            ->required()
                                            ->columnSpanFull(),
                                        Repeater::make('basisMains')
                                            ->label('ຫຼັກຖານ')
                                            ->relationship()
                                            ->orderColumn('order')
                                            ->extraAttributes(['style' => 'border-inline-start: 4px solid rgb(52 211 153); padding-inline-start: 1rem;'])
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('ຫຼັກຖານ')
                                                    ->required()
                                                    ->columnSpanFull(),
                                            ])
                                            ->itemLabel('ຫຼັກຖານ')
                                            ->itemNumbers()
                                            ->addActionLabel('ເພີ່ມຫຼັກຖານ')
                                            ->collapsible()
                                            ->collapsed()
                                            ->defaultItems(0)
                                            ->columnSpanFull(),
                                    ])
                                    ->itemLabel('ຕົວຊີ້ວັດ')
                                    ->itemNumbers()
                                    ->addActionLabel('ເພີ່ມຕົວຊີ້ວັດ')
                                    ->collapsible()
                                    ->collapsed()
                                    ->defaultItems(0)
                                    ->columnSpanFull(),

                            ])
                            ->itemLabel('ມາດຕະຖານ')
                            ->itemNumbers()
                            ->addActionLabel('ເພີ່ມມາດຕະຖານ')
                            ->collapsible()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ])->columnSpanFull()
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Standards/indicators/basis mains are restrict-on-delete once a
     * Document or Report references them, so removing a repeater row that's
     * already in use throws a DB constraint violation (parent::save()
     * already rolls back its transaction on any Throwable) — surface it as
     * a notification instead of letting it bubble up as a fatal 500.
     */
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        try {
            parent::save($shouldRedirect, $shouldSendSavedNotification);
        } catch (QueryException) {
            Notification::make()
                ->title('ບໍ່ສາມາດບັນທຶກໄດ້')
                ->body('ອາດຈະເປັນຍ້ອນລຶບຫົວຂໍ້ທີ່ມີເອກະສານ ຫຼື ບົດລາຍງານອ້າງອີງຢູ່ແລ້ວ. ກະລຸນາໂຫຼດໜ້ານີ້ໃໝ່.')
                ->danger()
                ->send();
        }
    }
}
