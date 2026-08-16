<?php

namespace App\Filament\Concerns;

use Filament\Forms\Components\FileUpload;
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
                if (! $state instanceof TemporaryUploadedFile) {
                    return;
                }

                $set('original_name', $state->getClientOriginalName());
                $set('mime_type', $state->getClientMimeType());
                $set('size', $state->getSize());
            })
            ->required()
            ->columnSpanFull();
    }
}
