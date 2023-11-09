<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NoteTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_id',
        'note_id',
        'created_at',
        'updated_at'
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo( Tag::class);
    }

    public function note(): BelongsTo
    {
        return $this->belongsTo( Note::class);
    }
}
