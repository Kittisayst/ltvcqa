<?php

namespace App\Filament\Resources\QaFrameworks;

use App\Filament\Resources\QaFrameworks\Pages\ManageQaFrameworks;
use App\Models\BasisMain;
use App\Models\Indicator;
use App\Models\QaFramework;
use App\Models\Standard;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class QaFrameworkResource extends Resource
{
    protected static ?string $model = QaFramework::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'ຊຸດມາດຕະຖານ';

    protected static string|UnitEnum|null $navigationGroup = 'ມາດຕະຖານການປະກັນຄຸນນະພາບ';

    protected static ?string $modelLabel = 'ຊຸດມາດຕະຖານ';

    protected static ?string $pluralModelLabel = 'ຊຸດມາດຕະຖານ';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
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
                    ->rule(fn (?Model $record) => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                        if ($value !== 'published') {
                            return;
                        }

                        $alreadyPublished = QaFramework::query()
                            ->where('status', 'published')
                            ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                            ->exists();

                        if ($alreadyPublished) {
                            $fail('ມີຊຸດມາດຕະຖານອື່ນທີ່ເຜີຍແຜ່ຢູ່ແລ້ວ. ກະລຸນາປ່ຽນອັນນັ້ນເປັນຮ່າງກ່ອນ.');
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
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
            ])
            ->filters([
                //
            ])
            ->recordActions([
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
                                            'description' => $basisMain->description,
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

    public static function getPages(): array
    {
        return [
            'index' => ManageQaFrameworks::route('/'),
        ];
    }
}
