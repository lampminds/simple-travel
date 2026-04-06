<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceGastronomyMenuCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'cat_service_gastronomy_menu_category_translations';

    protected $fillable = [
        'service_gastronomy_menu_category_id',
        'language_id',
        'name',
    ];

    public function serviceGastronomyMenuCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceGastronomyMenuCategory::class, 'service_gastronomy_menu_category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}

