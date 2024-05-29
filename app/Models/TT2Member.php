<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TT2Member extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tt2_members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'member_code'];

    /**
     * Get the raidStatistics for the member.
     */
    public function raidStatistics(): HasMany
    {
        return $this->hasMany(TT2RaidStatistic::class, 'member_id');
    }
}
