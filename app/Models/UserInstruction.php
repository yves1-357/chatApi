<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInstruction extends Model
{
    protected $fillable = ['name','email','user_id','content'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
