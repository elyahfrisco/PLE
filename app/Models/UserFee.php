<?php

namespace App\Models;

use App\Models\Season;
use App\Models\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserFee extends AppModel
{
    use HasFactory;
    protected $fillable = [
        'amount',
        'type',
        'bill_id',
        'user_id',
        'season_id'
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function bill()
    {
        return $this->hasOne(Bill::class, 'id', 'bill_id');
    }
}
