<?php

namespace App\Filament\Pages;

use App\Models\Menu;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class WebsiteMenuEditorPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'filament.resources.nav_parameters';

    protected static ?int $navigationSort = 18;

    protected static ?string $slug = 'website-menu-editor';

    protected string $view = 'filament.pages.website-menu-editor';

    /**
     * Eager-load a few levels of children for the tree (typical menus stay shallow).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Menu>
     */
    #[Computed]
    public function rootMenus()
    {
        return Menu::query()
            ->whereNull('parent_id')
            ->with([
                'children' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')->with([
                    'children' => fn ($q2) => $q2->orderBy('sort_order')->orderBy('id')->with([
                        'children' => fn ($q3) => $q3->orderBy('sort_order')->orderBy('id'),
                    ]),
                ]),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function moveMenu(int $menuId, string $direction): void
    {
        $menu = Menu::query()->findOrFail($menuId);

        $siblings = Menu::query()
            ->where('parent_id', $menu->parent_id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $idx = $siblings->search(fn (Menu $m): bool => (int) $m->id === (int) $menu->id);
        if ($idx === false) {
            return;
        }

        if ($direction === 'up' && $idx > 0) {
            $this->swapSortOrder($menu, $siblings[$idx - 1]);
        } elseif ($direction === 'down' && $idx < $siblings->count() - 1) {
            $this->swapSortOrder($menu, $siblings[$idx + 1]);
        }
    }

    private function swapSortOrder(Menu $a, Menu $b): void
    {
        DB::transaction(function () use ($a, $b): void {
            $ta = (int) $a->sort_order;
            $tb = (int) $b->sort_order;
            $a->update(['sort_order' => $tb]);
            $b->update(['sort_order' => $ta]);
        });
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.pages.website_menu_editor.nav_label');
    }

    public function getTitle(): string
    {
        return __('filament.pages.website_menu_editor.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        $group = static::$navigationGroup;

        return $group instanceof \UnitEnum
            ? $group->value
            : ($group !== null ? (string) __($group) : null);
    }
}