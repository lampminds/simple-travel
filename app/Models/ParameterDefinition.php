<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ParameterDefinition extends Model
{
    use AuditTrait;

    protected $table = 'cat_parameter_definitions';

    /** @var list<string> */
    protected $fillable = [
        'category',
        'code',
        'type',
        'scope',
        'has_default',
        'ui_component',
        'ui_options',
        'help',
        'comments',
    ];

    protected $casts = [
        'has_default' => 'boolean',
        'ui_options' => 'array',
    ];

    /** @var list<string> */
    public const TYPES = [
        'string',
        'integer',
        'decimal',
        'boolean',
        'json',
        'array',
        'object',
        'date',
        'datetime',
        'time',
    ];

    /** @var list<string> */
    public const SCOPES = [
        'system',
        'tenant',
    ];

    /** Must match `cat_parameter_definitions.ui_component` enum in migrations. */
    public const UI_COMPONENTS = [
        'input',
        'select',
        'checkbox',
        'radio',
        'switch',
        'textarea',
        'editor',
        'date',
        'datetime',
        'time',
    ];

    public function parameterValues(): HasMany
    {
        return $this->hasMany(ParameterValue::class, 'parameter_definition_id');
    }
}


