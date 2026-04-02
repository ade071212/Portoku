<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactInfo extends Model
{
    protected $table = 'contact_info';

    protected $fillable = [
        'user_id',
        'cta_title',
        'cta_description',
        'email',
        'linkedin',
        'instagram',
        'whatsapp',
        'tiktok',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
