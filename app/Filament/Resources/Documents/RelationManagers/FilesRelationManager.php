<?php

namespace App\Filament\Resources\Documents\RelationManagers;

use App\Filament\Concerns\ConfiguresEvidenceFileUpload;
use App\Models\DocumentFile;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Hugomyb\FilamentMediaAction\Actions\MediaAction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

class FilesRelationManager extends RelationManager
{
    use ConfiguresEvidenceFileUpload;

    protected static string $relationship = 'files';

    protected static ?string $title = 'ໄຟລ໌ແນບ';

    /**
     * hugomyb/filament-media-action only knows how to preview these — anything
     * else (Word, Excel, zip) falls back to the plain download link.
     */
    private const PREVIEWABLE_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png'];

    private function isPreviewable(DocumentFile $file): bool
    {
        return in_array(strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION)), self::PREVIEWABLE_EXTENSIONS, true);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reference_no')
                    ->label('ເລກທີ່')
                    ->required(),
                DatePicker::make('issued_date')
                    ->label('ວັນທີອອກ')
                    ->required(),
                $this->evidenceFileUploadComponent('documents/'.$this->getOwnerRecord()->getKey()),
                Hidden::make('disk')->default('local'),
                Hidden::make('original_name')->required(),
                Hidden::make('mime_type')->required(),
                Hidden::make('size')->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('original_name')
            ->columns([
                TextColumn::make('reference_no')
                    ->label('ເລກທີ່')
                    ->searchable(),
                TextColumn::make('issued_date')
                    ->label('ວັນທີອອກ')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('original_name')
                    ->label('ຊື່ໄຟລ໌')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('mime_type')
                    ->label('ປະເພດ'),
                TextColumn::make('size')
                    ->label('ຂະໜາດ')
                    ->formatStateUsing(fn (int $state): string => Number::fileSize($state)),
                TextColumn::make('created_at')
                    ->label('ອັບໂຫຼດເມື່ອ')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('ອັບໂຫຼດໄຟລ໌')
                    ->visible(fn (): bool => auth()->user()?->can('update', $this->getOwnerRecord()) ?? false),
            ])
            ->recordActions([
                MediaAction::make('preview')
                    ->label('ເບິ່ງ')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (DocumentFile $record): bool => $this->isPreviewable($record))
                    ->media(fn (DocumentFile $record): string => Storage::disk($record->disk)->temporaryUrl($record->path, now()->addMinutes(5))),
                Action::make('download')
                    ->label('ດາວໂຫຼດ')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record): string => Storage::disk($record->disk)->temporaryUrl($record->path, now()->addMinutes(5)))
                    ->openUrlInNewTab(),
                DeleteAction::make()
                    ->visible(fn (): bool => auth()->user()?->can('update', $this->getOwnerRecord()) ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->can('update', $this->getOwnerRecord()) ?? false),
                ]),
            ]);
    }
}
