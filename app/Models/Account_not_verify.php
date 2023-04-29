<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account_not_verify extends Model
{
    use HasFactory;
    protected $table = 'account_not_verify';
    protected $fillable = [
        'user_id',
        'lname',
        'fname',
        'username',
        'email',
        'password'
    ];
}
