<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class AccountDocument extends Model
{
    use HasFactory, AuditTrait, HasUuid;

    protected $table = 'account_documents';

    protected $fillable = [
        'account_id',
        'document_id',
        'value',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(CatDocument::class, 'document_id');
    }
}
