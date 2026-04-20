<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class AiKnowledgeItem extends Model
{
    use AuditTrait;

    protected $table = 'ai_knowledge_items';

    protected $fillable = [
        'key',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(AiKnowledgeTranslation::class, 'ai_knowledge_item_id');
    }
}
