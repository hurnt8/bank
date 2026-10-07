<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycAnswer extends Model
{
    protected $fillable = ['user_id', 'field_key', 'value', 'file_path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
