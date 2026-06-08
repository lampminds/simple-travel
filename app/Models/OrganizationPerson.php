<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class OrganizationPerson extends Model
{
    use AuditTrait, HasUuid;

    protected $table = 'organization_person';

    protected $fillable = [
        'organization_id',
        'person_id',
        'contact_department_id',
        'contact_position_id',
        'is_primary',
        'is_active',
        'is_public_contact',
        'is_preferred_contact_mode',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'is_public_contact' => 'boolean',
        'is_preferred_contact_mode' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(ContactDepartment::class, 'contact_department_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(ContactPosition::class, 'contact_position_id');
    }
}
