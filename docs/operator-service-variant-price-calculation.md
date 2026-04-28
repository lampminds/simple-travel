# Operator price calculation (service variant)

This document describes how an **operator-facing price** for a **selected service variant** is derived from the current database schema, and how that maps to the pricing tables introduced in `database/migrations/services/pricing/2026_04_05_001010_create_price_lists_table.php`.

> **Terminology:** In the admin UI, a price list is assigned to an **account** (`assigned_to` morph). In product language, that account is often the **operator** (or agency) that “takes” the service from the provider. The schema does not use a separate `operators` table for this link.

---

## 1. Starting point: variant base price

**Table:** `service_variants`  
**Migration:** `database/migrations/services/variants/2026_02_20_005030_create_service_variants_table.php`

For a chosen `service_variant_id`, the starting monetary amount is:

| Column        | Role |
|---------------|------|
| `base_price`  | Starting price (decimal `12,2`) |
| `currency_id` | Currency of the variant (FK to `cat_currencies`) |

```text
base_amount = service_variants.base_price   (for the selected variant)
```

---

## 2. Resolve the operator’s price list (assignment + date windows)

**Table:** `price_list_assignments`  
**Same pricing migration as above.**

An operator (as an `Account`) is linked to a **price list** through a row in `price_list_assignments`:

| Column              | Role |
|---------------------|------|
| `price_list_id`     | Which list applies |
| `assigned_to_type`  | Morph type (in Filament this is set to `Account::class`) |
| `assigned_to_id`    | **Operator account** id |
| `valid_from`        | Optional start of assignment validity (timestamp, nullable) |
| `valid_to`          | Optional end of assignment validity (timestamp, nullable) |
| `is_active`         | Must be truthy for the assignment to be considered |

**Date logic (assignment):** For a given **pricing date** (e.g. travel date, booking date, or “today” — product decision), the assignment is eligible if:

- `is_active` is true, and  
- `valid_from` is null or `pricing_date >= valid_from`, and  
- `valid_to` is null or `pricing_date <= valid_to`.

**Table:** `price_lists` (same migration)

The **list itself** also carries validity and status:

| Column        | Role |
|---------------|------|
| `valid_from`  | Optional list-wide start |
| `valid_to`    | Optional list-wide end |
| `is_active`   | List must be active |

**Date logic (list):** The same `pricing_date` should fall within the list’s `valid_from` / `valid_to` when those are set, and `is_active` should be true.

**Schema note:** Both `price_lists` and `price_list_assignments` support independent date ranges. A correct resolver should enforce **both** the list window and the assignment window (and any future business rules for conflicts if several rows match).

---

## 3. List line for the variant: `price_list_items` adjustment

**Table:** `price_list_items`  
**Same pricing migration.**

Once a single effective `price_list_id` is known for the operator and date, the row for the variant is:

| Column               | Role |
|----------------------|------|
| `price_list_id`      | FK to the list |
| `service_variant_id` | FK to the variant |
| `price`              | Decimal `10,2` — meaning depends on `pricing_mode` / `application_mode` |
| `pricing_mode`       | `fixed` or `percentage` |
| `application_mode`   | `compose` or `final` |

**Migration comment on `application_mode` (verbatim):**  
*“Compose: price is composed of the base price + the percentage”*

Recommended interpretation (to be implemented consistently in code):

- Let `B` = `service_variants.base_price` for the variant.
- Let `L` = `price_list_items.price` (the list line value).
- **`application_mode = final`:** the list line is the full sale amount for that step (i.e. ignore composing with base for that line — `L` is the line result before global assignment adjustment).
- **`application_mode = compose` with `pricing_mode = fixed`:** treat `L` as a **fixed delta** on the base, e.g. `B + L` (sign of `L` is data-defined).
- **`application_mode = compose` with `pricing_mode = percentage`:** treat `L` as a **percentage of the base**, e.g. `B + (B * L / 100)` (exact formula should match product rules).

If **no** `price_list_items` row exists for `(price_list_id, service_variant_id)`, the schema does not define a fallback; the application may default to `B` or treat the price as unknown — **that is a product decision, not encoded in the migration.**

**Currency:** `price_lists.currency_id` should be aligned with the variant’s `currency_id` for a coherent amount; the schema does not enforce equality.

---

## 4. Global assignment adjustment (operator-specific)

**Table:** `price_list_assignments` (same row as in section 2)

After the amount from section 3 is obtained, apply the **assignment-level** adjustment:

| Column             | Role |
|--------------------|------|
| `adjustment_type`  | `none`, `percentage`, or `fixed` |
| `adjustment_value` | Nullable decimal `12,2`; comment: *“-10 = discount, +15 = markup”* |

Suggested interpretation:

- **`none`:** no change.
- **`percentage`:** `result = previous_result * (1 + adjustment_value / 100)` (sign per stored value).
- **`fixed`:** `result = previous_result + adjustment_value`.

---

## 5. End-to-end flow (summary)

```text
1. B = service_variants.base_price
2. Find price_list_assignments for operator Account + active + date range
   AND the linked price_lists row active + date range
3. Find price_list_items for (that price_list_id, service_variant_id)
4. Map list line (pricing_mode + application_mode) to an intermediate price from B and/or L
5. Apply price_list_assignments.adjustment_type / adjustment_value
```

---

## 6. Migration support checklist (confirmed)

| Requirement | Supported by schema? |
|-------------|----------------------|
| Base price on the variant | Yes — `service_variants.base_price` |
| Operator-specific list link with date range | Yes — `price_list_assignments` on morph `assigned_to` + `valid_from` / `valid_to` / `is_active` |
| List-level date range | Yes — `price_lists.valid_from` / `valid_to` / `is_active` |
| Per-variant line on a list (adjustment vs base) | Yes — `price_list_items` with `price`, `pricing_mode`, `application_mode` |
| Global operator discount/markup on top | Yes — `adjustment_type`, `adjustment_value` on the **assignment** row |

**Implementation note:** The `PriceListItem` Eloquent model may not expose every column that exists in the database (e.g. `application_mode` in `$fillable`). The **physical table** still supports the full model described above; keep forms and mass-assignment in sync when implementing the calculator.

---

## 7. Out of scope (not in these migrations)

- How to pick one list when **multiple** assignments match (priority rules).
- Rounding, taxes, and FX conversion.
- “Operator takes” **catalog** tables (`operator_catalog_items`, etc.) if pricing is also driven by catalog rows — that would be a separate document.
