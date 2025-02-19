<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected function casts(): array {
        return [
            'date' => 'datetime'
        ];
    }
}
