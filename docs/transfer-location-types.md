# Transfer location types catalog

Location rows (`service_transfer_locations`) reference a **type** from the global catalog seeded by `ServiceTransferLocationTypesCatalogSeeder` (`service_transfer_location_types` + translations).

City-specific location seeders (Foz, San Martín de los Andes, …) must use an existing type `code`. Do not invent codes only in a location seeder.

## Adding a new type

1. Open `database/seeders/ServiceTransferLocationTypesCatalogSeeder.php`.
2. Add the type under the best-fitting category in `locationTypeDefinitions()` with `en`, `es`, and `pt` labels.
3. Re-run the catalog seeder:

   ```bash
   php artisan db:seed --class=ServiceTransferLocationTypesCatalogSeeder
   ```

4. Use the new `code` in the city location seeder and re-run that seeder.

`updateOrInsert` on types is keyed by `code`, so re-running the catalog seeder is safe and refreshes translations.

## Categories (high level)

| Category code | Examples of types |
|---------------|-------------------|
| `public_transport` | `airport`, `bus_station`, `international_airport` |
| `hospitality` | `hotel`, `resort`, `lodge` |
| `tourism_attractions` | `beach`, `viewpoint`, `waterfall`, `national_park`, `park`, `scenic_route`, `geological_site` |
| `urban` | `downtown`, `neighborhood` |
| `meeting_points` | `meeting_point`, `checkpoint` |
| `outdoor_adventure` | `mountain`, `volcano`, `ski_resort`, `refuge` |
| `private_generic` | `private_address`, `custom_location` |

## Recently added types (San Martín catalog work)

| Code | Use for |
|------|---------|
| `mountain` | Peaks and massifs that are not modeled as `volcano` |
| `park` | Municipal, theme, or religious parks (not `national_park`) |
| `scenic_route` | Named scenic drives (e.g. Route of the Seven Lakes) |
| `geological_site` | Lava fields, rock formations, similar sites |

Prefer specific types (`beach`, `viewpoint`, `waterfall`) over generic `tourist_attraction` when the catalog already has a match.

## Related seeders

- `ServiceTransferLocationsFozDoIguacuAccount1Seeder` — `city_id` 11691
- `ServiceTransferLocationsSanMartinDeLosAndesSeeder` — `city_id` 1425

## Filament: generate catalog for a city

In **Administration → Cities** (`LmpCityResource`), the row action **Generate transfer locations** calls:

- `App\Services\CityTransferLocationsAiPlanner` — OpenAI (`OPENAI_API_KEY`, optional `OPENAI_CHAT_MODEL`)
- `App\Services\CitySystemTransferLocationsGeneratorService` — persists rows with `account_id` null and optional MyMemory/Google translations

Requires active location types in the catalog. Default source language is Spanish (`es` locale); you can disable translation to insert only the source language.

**OpenAI usage per run:** 1 call to generate the location list; if translation is enabled, 1 more call (or 2 if there are more than 25 names) to translate all labels in batch. MyMemory/Google are not used in this flow.

See also [account-scoping.md](account-scoping.md) for `account_id` null system catalog rows.
