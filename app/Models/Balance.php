<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property int $amount
 */
class Balance extends Model
{
    use HasFactory;

    protected $table = 'balances';

    protected $primaryKey = 'user_id';
}
