<?php

namespace App\Models;

// Empty class that extends the vendor model
class LmpCity extends \Illuminate\Database\Eloquent\Model
{
    protected $connection = 'addons';

    protected $table = 'lmp_cities';
    public $timestamps = false;
    protected $fillable = ['name', 'state_id', 'latitude', 'longitude', 'timezone_id'];
    public function state() { return $this->belongsTo(LmpState::class); }
    public function timezone() { return $this->belongsTo(LmpTimezone::class); }
}
