<?php

namespace App\Models;

class LmpState extends \Illuminate\Database\Eloquent\Model
{
    protected $connection = 'addons';

	protected $table = 'lmp_states';
	public $timestamps = false;

	protected $fillable = [
		'name', 'country_id', 'state_type_id', 'level', 'parent_id', 'latitude', 'longitude', 'timezone_id'
	];

	public function country() { return $this->belongsTo(LmpCountry::class, 'country_id'); }
	public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
	public function children() { return $this->hasMany(self::class, 'parent_id'); }
}


