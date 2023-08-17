<?php

namespace App\Models;

use App\Models\Season;
use App\Models\Closing;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Trimester extends AppModel
{
    use HasFactory;

    protected $fillable = [
        'num_trimester',
        'week_count',
        'date_start',
        'date_end',
        'season_id',
    ];

    protected $dates = [
        'date_start',
        'date_end',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function closings()
    {
        return $this->join('closings', function ($query) {
            $query->on('trimesters.date_start')
                ->on('trimesters.date_start');
        });
    }

    public function activities_price()
    {
        // return $this->has;
    }

    public function scopeUnfinished($query)
    {
        $query->where('date_end', '>', now());
    }

    public function scopeGroupByNumTrimester($query)
    {
        $query->select(DB::raw('*, MIN(date_start) date_start, MAX(date_end) date_end'))
            ->groupBy('num_trimester');
    }
}
