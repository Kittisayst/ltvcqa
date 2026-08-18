<?php

namespace App\Filament\Resources\Documents\RelationManagers;

use App\Filament\Concerns\ConfiguresEvidenceFileUpload;
use App\Models\DocumentFile;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
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

    private static function mimeTypeIcon(string $mimeType): Heroicon
    {
        return match (true) {
            $mimeType === 'application/pdf' => Heroicon::OutlinedDocumentText,
            str_starts_with($mimeType, 'image/') => Heroicon::OutlinedPhoto,
            in_array($mimeType, [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ], true) => Heroicon::OutlinedTableCells,
            in_array($mimeType, ['application/zip', 'application/x-zip-compressed'], true) => Heroicon::OutlinedArchiveBox,
            default => Heroicon::OutlinedDocument,
        };
    }

    private static function mimeTypeLabel(string $mimeType): string
    {
        return match (true) {
            $mimeType === 'application/pdf' => 'PDF',
            str_starts_with($mimeType, 'image/') => 'ຮູບພາບ',
            in_array($mimeType, [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ], true) => 'Word',
            in_array($mimeType, [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ], true) => 'Excel',
            in_array($mimeType, ['application/zip', 'application/x-zip-compressed'], true) => 'ZIP',
            default => $mimeType,
        };
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->evidenceFileUploadComponent('documents/'.$this->getOwnerRecord()->getKey()),
                ...$this->evidenceReferenceFields(),
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
                IconColumn::make('mime_type')
                    ->label('ປະເພດ')
                    ->icon(fn (string $state): Heroicon => self::mimeTypeIcon($state))
                    ->tooltip(fn (string $state): string => self::mimeTypeLabel($state)),
                TextColumn::make('size')
                    ->label('ຂະໜາດ')
                    ->formatStateUsing(fn (int $state): string => Number::fileSize($state)),
                TextColumn::make('created_at')
                    ->label('ອັບໂຫຼດເມື່ອ')
                    ->dateTime('d/m/Y H:i')
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
