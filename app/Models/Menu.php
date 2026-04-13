<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Menu extends Model
{
    use AuditTrait;

    protected $table = 'cat_menus';

    protected $fillable = [
        'slug',
        'icon',
        'route',
        'parent_id',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(MenuTranslation::class, 'menu_id');
    }

    /**
     * Account types (cat_account_categories.group = type) that may see this menu.
     * Empty pivot means the item is not shown for any account type (explicit assignment required).
     */
    public function accountTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            AccountCategory::class,
            'cat_menu_account_type_assignments',
            'menu_id',
            'type_id'
        )->where((new AccountCategory)->getTable().'.group', 'type');
    }

    /**
     * Label for admin lists (translation for current locale, else slug).
     */
    public function getAdminLabelAttribute(): string
    {
        $t = $this->getTranslationForDisplay();

        return ($t && filled($t->name)) ? (string) $t->name : (string) $this->slug;
    }

    protected function getTranslationForDisplay(): ?MenuTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations.language.locale');
        }

        if ($this->translations->isEmpty()) {
            return null;
        }

        $locale = app()->getLocale();
        foreach ($this->translations as $translation) {
            $lang = $translation->language;
            if (! $lang) {
                continue;
            }
            $lang->loadMissing('locale');
            if ($lang->locale && Locale::primaryTagMatches($lang->locale, $locale)) {
                return $translation;
            }
        }

        return $this->translations->first();
    }

    /**
     * Whether assigning $ancestorId as parent would create a cycle.
     */
    public function wouldCreateParentCycle(?int $ancestorId): bool
    {
        if ($ancestorId === null) {
            return false;
        }

        if ((int) $ancestorId === (int) $this->id) {
            return true;
        }

        $current = Menu::query()->find($ancestorId);
        while ($current !== null) {
            if ((int) $current->id === (int) $this->id) {
                return true;
            }
            $current = $current->parent;
        }

        return false;
    }

    /**
     * Depth for flat table indent (0 = root). Loads parent chain up to 20 hops.
     */
    public function getDepthAttribute(): int
    {
        $d = 0;
        $p = $this->parent;
        $guard = 0;
        while ($p !== null && $guard < 20) {
            $d++;
            $p->loadMissing('parent');
            $p = $p->parent;
            $guard++;
        }

        return $d;
    }

    /**
     * @return list<int>
     */
    public static function descendantIdsOf(int $menuId): array
    {
        $ids = [];
        $stack = static::query()->where('parent_id', $menuId)->pluck('id')->all();
        while ($stack !== []) {
            $id = (int) array_pop($stack);
            $ids[] = $id;
            foreach (static::query()->where('parent_id', $id)->pluck('id')->all() as $cid) {
                $stack[] = (int) $cid;
            }
        }

        return $ids;
    }
}

