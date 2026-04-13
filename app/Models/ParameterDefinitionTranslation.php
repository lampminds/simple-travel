<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ParameterDefinitionTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_parameter_definition_translations';

    /** @var list<string> */
    protected $fillable = [
        'parameter_definition_id',
        'language_id',
        'name',
        'description',
        'help',
    ];

    public function parameterDefinition(): BelongsTo
    {
        return $this->belongsTo(ParameterDefinition::class, 'parameter_definition_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
