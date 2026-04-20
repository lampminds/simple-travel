<?php

namespace App\Models;

use App\Observers\AiKnowledgeTranslationObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class AiKnowledgeTranslation extends Model
{
    use AuditTrait;

    protected $table = 'ai_knowledge_translations';

    protected $fillable = [
        'ai_knowledge_item_id',
        'language_id',
        'title',
        'content',
        'content_short',
        'url',
        'tags',
        'embedding',
        'embedding_model',
        'embedding_version',
    ];

    protected static function booted(): void
    {
        static::observe(AiKnowledgeTranslationObserver::class);
    }

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'embedding' => 'array',
        ];
    }

    public function knowledgeItem(): BelongsTo
    {
        return $this->belongsTo(AiKnowledgeItem::class, 'ai_knowledge_item_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
