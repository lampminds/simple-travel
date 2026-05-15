# Website: service “features”, “details”, and vertical catalogs — usage survey

**Scope:** authenticated **website** (Blade + `app/Http/Controllers` + `app/Livewire` + `routes/web.php`). **Excluded:** Filament admin, Artisan-only commands, seeders (except as data prerequisites).

**Method:** code search for reads/writes tied to public/account flows; vertical modules (hotel / entertainment / gastronomy / transfer) were checked for any reference under `app/Http`, `app/Livewire`, and `resources/views` outside Filament.

---

## 1. Where the website actually reads/writes service data today

| Flow | Route / entry | What persists |
|------|----------------|---------------|
| **Service wizard — step 1** | `services/wizard/{serviceType}/step-1` | `services`, `service_translations`, city on `services` (`ServiceWizardController`) |
| **Step 2** | `…/step-2/{service}` | Livewire `ServiceStatusStep` → columns on `services` (status, duration, flags, booking, etc.) |
| **Step 3** | `…/step-3/{service}` | Livewire `ServiceVariantsStep` → `service_variants` (+ variant media via Spatie) |
| **Step 4 — “features”** | `…/step-4/{service}` | Livewire `ServiceFeaturesStep` → pivot `service_featurables`; catalog driven by `cat_service_feature_scopes`, `cat_service_features`, `cat_service_feature_categories` (`ServiceFeatureSelectionService`) |
| **Step 5 — media** | `…/step-5/{service}` | Livewire `ServiceMediaStep` → `sys_media` (via `App\Models\Media`) on `Service` |
| **Step 6 — “details”** | `…/step-6/{service}` | Livewire `ServiceDetailsStep` → `cat_service_details`; topics/categories from `cat_service_detail_topics`, `cat_service_detail_topic_categories` |
| **Catalog (account)** | `GET catalog` | Lists `services` (+ type, translations, media, variant counts). **Does not** render feature tags, detail paragraphs, hotel rows, etc. (`CatalogController` + `catalog/*` views) |

So on the **website**, “**features**” and “**details**” are **only** maintained through the wizard (steps 4 and 6). They are **not** shown again on the catalog list as structured data.

---

## 2. “Characteristics” stack — `cat_service_features` (wizard step 4)

These tables **are in use on the website** for editing; admin also maintains the catalog.

| Table | Role | Website |
|-------|------|--------|
| `cat_service_feature_categories` | Grouping of features | **Yes** — options in step 4 (`ServiceFeatureSelectionService::categoryCheckboxOptions`) |
| `cat_service_feature_category_translations` | Labels | **Yes** (via Eloquent `translations` on categories) |
| `cat_service_features` | Individual selectable features | **Yes** — loaded in step 4 when in scope + category filter |
| `cat_service_feature_translations` | Feature labels | **Yes** (via model) |
| `cat_service_feature_scopes` | Which feature IDs apply to which `cat_service_types` | **Yes** — `scopedFeatureIdsForServiceType()` |
| `service_featurables` | Pivot: service ↔ feature (polymorphic; service side is `Service`) | **Yes** — synced in `ServiceFeaturesStep` |

**Gap:** There is **no** consumer-facing page in this repo that **displays** the chosen features to visitors (only Filament + wizard persistence). The catalog table shows name/type/status/variants only.

---

## 3. “Details” stack — structured topics (wizard step 6)

| Table | Role | Website |
|-------|------|--------|
| `cat_service_detail_topic_categories` | Group detail topics | **Yes** — `ServiceDetailsStep` uses categories to filter topics |
| `cat_service_detail_topic_category_translations` | Labels | **Yes** (via model) |
| `cat_service_detail_topics` | Topics (per category) | **Yes** — chosen per line in step 6 |
| `cat_service_detail_topic_translations` | Topic labels | **Yes** (via model) |
| `cat_service_details` | Per-service, per-language paragraphs | **Yes** — CRUD in `ServiceDetailsStep` |

**Gap:** Same as features — **no** public or account “service detail” view composes these rows for display (only wizard + Filament).

---

## 4. Activities (related catalog, not “features” UI)

| Table | Role | Website |
|-------|------|--------|
| `cat_service_activity_categories` (+ translations) | Categories for activities | **No** in website code — **Filament / seeders** |
| `cat_service_activities` (+ translations) | Activity catalog | **No** wizard step — **Filament / seeders** |
| `service_activity_assignments` | M:N service ↔ activity | **No** — assigned only in **Filament** (`ServiceResource` activities tab) |

**Gap:** If the product intent is “activities on the website wizard”, that step **does not exist** yet; data is admin-only.

---

## 5. Vertical specializations (hotel / entertainment / gastronomy / transfer)

These areas have **rich `cat_service_*` dictionaries** and **`service_*` profile tables** in migrations. For the **website**, there is **no** controller, Livewire component, or Blade under the account flows that **creates, edits, or displays** them.

Evidence: matches for `service_hotels`, `service_gastronom`, `service_entertainment`, `service_transfer`, etc. appear in **Filament**, **migrations**, **seeders**, **`ServiceCascadeDeletion`**, and **`routes/console.php`** — not under `ServiceWizardController`, `CatalogController`, or `app/Livewire`.

### 5.1 Hotels

| Table | Website |
|-------|--------|
| `cat_service_hotel_type_categories` (+ translations) | **Not used** |
| `cat_service_hotel_types` (+ translations) | **Not used** |
| `service_hotels` (1:1 extension row per service) | **Not used** |

### 5.2 Entertainment

| Table | Website |
|-------|--------|
| `cat_service_entertainment_type_categories` (+ translations) | **Not used** |
| `cat_service_entertainment_types` (+ translations) | **Not used** |
| `service_entertainment` | **Not used** |

### 5.3 Gastronomy

| Table | Website |
|-------|--------|
| `cat_service_gastronomy_types` (+ translations) | **Not used** |
| `cat_service_gastronomy_cuisines` (+ translations) | **Not used** |
| `cat_service_gastronomy_venues` (+ translations) | **Not used** |
| `cat_service_gastronomy_menu_categories` (+ translations) | **Not used** |
| `cat_service_gastronomy_menus` (+ translations) | **Not used** |
| `service_gastronomies` | **Not used** |
| `service_cuisine_gastronomy_assignments` | **Not used** |
| `service_gastronomy_venue_assignments` | **Not used** |
| `service_gastronomy_schedules` | **Not used** |
| `service_gastronomy_capacities` | **Not used** |
| `service_gastronomy_experiences` | **Not used** |

**Note:** Filament “gastronomy feature” resources (`ServiceGastronomyFeature*`) reuse the **same** `cat_service_feature_categories` / `cat_service_features` tables as the wizard step 4 taxonomy — they are not separate physical tables. Only the **service_gastronomy_*** extension graph above is gastronomy-specific schema.

### 5.4 Transfers

| Table | Website |
|-------|--------|
| `service_transfer_location_types` (+ translations) | **Not used** |
| `service_transfer_locations` (+ translations) | **Not used** |
| `service_transfer_vehicle_types` | **Not used** |
| `service_transfers` | **Not used** |
| `service_transfer_routes` | **Not used** |
| `service_transfer_vehicles` | **Not used** |
| `service_transfer_prices` | **Not used** |

**Interpretation:** the vertical model is **prepared in the schema** (and manageable in admin), but the **website onboarding/editing path does not branch** by `service_type` into these tables. Any rows would only come from **Filament** (or custom code not present in this survey).

---

## 6. Other service-related tables (context)

| Area | Website | Notes |
|------|--------|--------|
| `cat_service_types` | **Yes** | Wizard URL segment; catalog “create service” dropdown |
| `service_variants` (+ translations) | **Yes** | Wizard step 3 |
| `service_variant_availability_rules` / `overrides` / `service_variant_availability_time_slots` | **No** in Livewire wizard | Likely admin / future booking |
| `provider_price_lists` / `provider_price_list_items` / `provider_price_list_assignments`; `operator_price_lists` / `operator_price_list_items` / `operator_price_list_assignments` | **Yes** | Account: `account/provider-price-lists` (`AccountProviderPriceListController`) and `account/operator-price-lists` (`AccountOperatorPriceListController`); legacy `/account/price-lists` redirects to provider URLs — commercial, not the same as “feature checkboxes” |
| `allocations` | **No** obvious website UI in surveyed paths | |

---

## 7. Summary — what is missing on the website (product/tech)

1. **Display layer:** Persisted **features** (`service_featurables`) and **details** (`cat_service_details`) are **not surfaced** on catalog, dashboards, or a public service page in this codebase.
2. **Activities:** **No** wizard or account UI; **Filament-only**.
3. **Vertical profiles:** Hotel / entertainment / gastronomy / transfer **submodels are unused** on the website; filling them is **admin-only** today.
4. **If the goal is parity with Filament:** you would add wizard steps (or a single “specialization” step) per `service_type` code, or reuse Filament-only data read-only on a future “service public profile” view.

---

## 8. How to re-verify later

```bash
# Website touchpoints (adjust paths as needed)
rg -n "ServiceDetailsStep|ServiceFeaturesStep|ServiceWizardController|CatalogController" app resources/views routes/web.php
rg -n "service_hotel|service_gastronom|service_entertainment|service_transfer" app/Http app/Livewire resources/views --glob '!**/Filament/**'
```

*Document generated from repository structure and grep-based audit; no runtime DB inspection.*
