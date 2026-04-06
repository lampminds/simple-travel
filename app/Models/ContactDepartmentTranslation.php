<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ContactDepartmentTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_contact_department_translations';

    protected $fillable = [
        'contact_department_id',
        'language_id',
        'name',
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
