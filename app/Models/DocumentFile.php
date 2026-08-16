<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['document_id', 'reference_no', 'issued_date', 'disk', 'path', 'original_name', 'mime_type', 'size'])]
class DocumentFile extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (DocumentFile $file): void {
            Storage::disk($file->disk)->delete($file->path);
        });
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
