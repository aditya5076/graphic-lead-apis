<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageQr extends Model
{
    protected $table = 'imagequeue';
    protected $primaryKey = 'imageid';
    public $incrementing = false;
    public $timestamps = \false;

    protected $fillable = [
        'imageid',
        'userid',
        'content_type',
        'submitted',
        'processed',
        'ttl',
        'status',
    ];

    protected $hidden = [
        'userid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
