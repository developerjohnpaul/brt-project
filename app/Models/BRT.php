<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BRT extends Model
{
    use HasFactory;

    protected $table = 'brts';  // Ensure this matches the table name

    protected $fillable = [
        'user_id', 
        'brt_code', 
        'reserved_amount', 
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
