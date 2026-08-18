<?php

namespace App\Filament\Concerns;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait ConfiguresEvidenceFileUpload
{
    /**
     * Matches the file types actually seen in the legacy evidence archive
     * (PDF, Office docs, images, zip) — the largest legacy file was ~23MB.
     */
    private const MAX_FILE_SIZE_KB = 25600;

    private const ACCEPTED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/zip',
        'application/x-zip-compressed',
    ];

    /**
     * A photo doesn't carry a filing reference number the way a numbered
     * paper does — evidence photos are auto-labeled instead of prompting
     * for one.
     */
    private const IMAGE_REFERENCE_NO_LABEL = 'ຮູບພາບ';

    protected function evidenceFileUploadComponent(string $directory): FileUpload
    {
        return FileUpload::make('path')
            ->label('ໄຟລ໌')
            ->disk('local')
            ->visibility('private')
            ->directory($directory)
            ->acceptedFileTypes(self::ACCEPTED_MIME_TYPES)
            ->maxSize(self::MAX_FILE_SIZE_KB)
            ->helperText('ຮອງຮັບ PDF, Word, Excel, ຮູບພາບ (JPG/PNG) ແລະ ZIP, ຂະໜາດບໍ່ເກີນ 25MB')
            ->afterStateUpdated(function ($state, callable $set): void {
                if (!$state instanceof TemporaryUploadedFile) {
                    return;
                }

                // Content-sniffed, not getClientMimeType(): Livewire's temp
                // upload has no real extension, so the client-reported mime
                // is unreliable (and browsers can misreport it anyway).
                $mimeType = $state->getMimeType();

                $set('original_name', $state->getClientOriginalName());
                $set('mime_type', $mimeType);
                $set('size', $state->getSize());

                if (str_starts_with($mimeType, 'image/')) {
                    $set('reference_no', self::IMAGE_REFERENCE_NO_LABEL);
                    $set('issued_date', null);
                }
            })
            ->required()
            ->columnSpanFull();
    }

    /**
     * Reference-number entry has 3 shapes: a photo (auto-labeled, no input
     * needed), a numbered paper (reference_no + issued_date), or a
     * non-numbered paper (issued_date only) — toggled by the uploader since
     * the file type alone can't say whether a document was numbered.
     *
     * @return array<int, Component>
     */
    protected function evidenceReferenceFields(): array
    {
        $isImage = fn(Get $get): bool => str_starts_with((string) $get('mime_type'), 'image/');

        return [
            Group::make([
                Toggle::make('has_reference_no')
                    ->label('ມີເລກທີ່')
                    ->default(true)
                    ->live()
                    ->dehydrated(false)
                    ->visible(fn(Get $get): bool => !$isImage($get))
                    ->afterStateUpdated(fn(bool $state, callable $set) => $state ? null : $set('reference_no', null)),
                TextInput::make('reference_no')
                    ->label('ເລກທີ່')
                    // Hiding-while-null (toggle off) is fine, but hiding a field
                    // silently drops its dehydrated value even with
                    // ->dehydrated(true) — so the image case, which needs its
                    // auto-filled value to actually persist, stays visible
                    // (read-only) instead of hidden.
                    ->visible(fn(Get $get): bool => $isImage($get) || $get('has_reference_no'))
                    ->disabled(fn(Get $get): bool => $isImage($get))
                    ->dehydrated(true)
                    ->required(fn(Get $get): bool => !$isImage($get) && $get('has_reference_no'))
                    ->columnSpan(2),
                DatePicker::make('issued_date')
                    ->label('ວັນທີອອກ')
                    ->visible(fn(Get $get): bool => !$isImage($get))
                    ->required(fn(Get $get): bool => !$isImage($get))
                    ->columnSpan(2),
            ])->columns(5)
        ];
    }
}
