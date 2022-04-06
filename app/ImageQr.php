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
        'contenttype',
        'submitted',
        'processed',
        'ttl',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
