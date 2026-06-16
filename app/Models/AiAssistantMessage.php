<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class AiAssistantMessage extends Model
{
    use AuditTrait;

    public const USAGE_ASSISTANT = 'assistant';

    public const USAGE_TRANSLATION = 'translation';

    public const USAGE_OPENAI_TRANSLATION = 'openai_translation';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    public const STATUS_RATE_LIMITED = 'rate_limited';

    protected $table = 'ai_assistant_messages';

    protected $fillable = [
        'usage_type',
        'source',
        'user_id',
        'account_id',
        'account_type_id',
        'language_id',
        'question',
        'answer',
        'status',
        'error_message',
        'chat_model',
        'embedding_model',
        'embedding_prompt_tokens',
        'chat_prompt_tokens',
        'chat_completion_tokens',
        'chat_total_tokens',
        'total_tokens',
        'estimated_usd',
        'source_keys',
    ];

    protected function casts(): array
    {
        return [
            'source_keys' => 'array',
            'embedding_prompt_tokens' => 'integer',
            'chat_prompt_tokens' => 'integer',
            'chat_completion_tokens' => 'integer',
            'chat_total_tokens' => 'integer',
            'total_tokens' => 'integer',
            'estimated_usd' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
