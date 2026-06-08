<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class OrganizationDocument extends Model
{
    use AuditTrait;

    protected $fillable = [
        'organization_id',
        'document_id',
        'value',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(CatDocument::class, 'document_id');
    }
}
