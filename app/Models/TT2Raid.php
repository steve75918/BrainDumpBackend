<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TT2Raid extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tt2_raids';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['batch_name'];
}
