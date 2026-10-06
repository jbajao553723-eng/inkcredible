<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoredFile extends Model
{
    protected $fillable = [
        'disk',
        'path',
        'contents_base64',
        'mime_type',
        'size',
        'visibility',
    ];

    protected $hidden = [
        'contents_base64',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }
}
