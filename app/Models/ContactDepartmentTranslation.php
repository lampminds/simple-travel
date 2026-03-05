<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ContactDepartmentTranslation extends Model
{
    use AuditTrait;

    protected $fillable = [
        'contact_department_id',
        'language_id',
        'code',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function contactDepartment(): BelongsTo
    {
        return $this->belongsTo(ContactDepartment::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
