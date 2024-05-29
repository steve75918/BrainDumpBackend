<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TT2RaidStatistic extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tt2_raid_statistics';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'raid_id',
        'attendance',
    ];

    /**
     * Get the raid that owns the raidStatistic.
     */
    public function raid(): BelongsTo
    {
        return $this->belongsTo(TT2Raid::class);
    }
}
