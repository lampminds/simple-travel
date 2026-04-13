<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ParameterOptionTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_parameter_option_translations';

    /** @var list<string> */
    protected $fillable = [
        'parameter_option_id',
        'language_id',
        'name',
    ];

    public function parameterOption(): BelongsTo
    {
        return $this->belongsTo(ParameterOption::class, 'parameter_option_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
