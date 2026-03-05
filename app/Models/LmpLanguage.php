<?php

namespace App\Models;

// Empty class that extends the vendor model
class LmpLanguage extends \Illuminate\Database\Eloquent\Model
{
    protected $connection = 'addons';

    protected $table = 'lmp_languages';
    public $timestamps = false;
    protected $fillable = ['name', 'code', 'code3'];
}
