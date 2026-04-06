<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceGastronomyMenuTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_gastronomy_menu_translations';

    protected $fillable = [
        'service_gastronomy_menu_id',
        'language_id',
        'name',
        'description',
    ];

    public function serviceGastronomyMenu(): BelongsTo
    {
        return $this->belongsTo(ServiceGastronomyMenu::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}

