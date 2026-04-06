<?php

namespace App\Models;

/**
 * Reference model for master currency data on addons DB (lmp_currencies).
 * Used for dropdowns and display; no FK across databases.
 */
class LmpCurrency extends \Illuminate\Database\Eloquent\Model
{
    protected $connection = 'addons';

    protected $table = 'lmp_currencies';

    public $timestamps = false;

    protected $fillable = ['code', 'symbol', 'name'];
}
