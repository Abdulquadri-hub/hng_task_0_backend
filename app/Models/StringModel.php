<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StringModel extends Model
{
    public $primaryKey = "id";

    public $incrementing = false;

    public $keyType = "string";

    const UPDATED_AT = null;

    protected $fillable = [
        'id','value','length','is_palindrome','unique_characters','word_count','sha256_hash','character_frequency_map',
    ];

    protected $casts = [
        'is_palindrome' => 'boolean',
        'length' => 'integer',
        'unique_characters' => 'integer',
        'word_count' => 'integer',
        'character_frequency_map' => 'array',
        'created_at' => 'datetime',
    ];
}
