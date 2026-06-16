# Service Variant Availability Model — Implementation Guide

## Overview

This document defines how availability must be interpreted and computed for catalog services and their `service_variants`.

The model is composed of:

* **Service layer** (`service_availability_rules`, `service_availability_overrides`) — master schedule and mass closures for all variants of a service
* Base configuration (`service_variants`)
* Recurring availability rules (`service_variant_availability_rules`)
* Optional time slots (`service_variant_availability_time_slots`)
* Overrides (`service_variant_availability_overrides`)

---

# 0. Service layer (all variants)

## 0.1 Purpose

Lets a provider close or restrict an entire service (e.g. hotel renovation) without editing each variant.

## 0.2 Tables

### `service_availability_rules`

Same fields as variant rules (no time slots): `service_id`, `start_date`, `end_date`, `weekday_mask`, `active`.

If **at least one active service rule exists**, a date must match a service rule **and** a variant rule to be bookable.

If **no service rules exist**, the service layer does not restrict dates (variant rules alone apply).

### `service_availability_overrides`

`service_id`, `date`, `end_date` (nullable, inclusive range end), `closed`, `reason`.

Service-level overrides support **closures only** (no capacity). A closed service override blocks **all variants** for every day in the range. Variant overrides cannot reopen a service closure.

## 0.3 Resolution order (full stack)

```
service closed override → service rules (if any) → variant rules → variant slots/overrides → capacity
```

---

# 1. Core Concepts (variant layer)

The system supports multiple inventory strategies:

* `unlimited`
* `per_day`
* `per_timeslot`
* `per_departure`

---

# 1. Core Concepts

## 1.1 Availability Layers (Priority Order)

Availability must always be resolved using this hierarchy:

```
override → time_slot → variant (default)
```

### Effective capacity:

```
effective_capacity =
    override.capacity ??
    time_slot.capacity ??
    variant.inventory_total
```

---

# 2. Table Definitions & Usage

---

## 2.1 `service_variants`

### Purpose

Defines the base behavior and default availability configuration.

### Key Fields

#### `inventory_type`

Determines how availability is interpreted:

* `unlimited`

    * No capacity limits
    * Availability always true (unless restricted by rules)

* `per_day`

    * Capacity applies per day
    * Time slots are ignored

* `per_timeslot`

    * Availability depends on defined time slots
    * Capacity is evaluated per slot

* `per_departure`

    * Same as `per_timeslot`, but semantically represents fixed departures

---

#### `inventory_total`

* Default capacity fallback
* Used when:

    * No slot capacity exists
    * No override exists

---

#### `capacity_min` / `capacity_max`

* Booking constraints
* Do NOT affect availability calculation
* Used only for validation

---

#### `start_time` / `end_time`

* Used ONLY when:

    * `inventory_type = per_day`
* Defines operating window
* Ignored if time slots are used

---

#### `cutoff_minutes`

* Default booking cutoff
* Used when slot-level cutoff is not defined

---

#### `min_advance_booking_hours`

* Minimum time before start required to book

#### `max_advance_booking_days`

* Maximum days in advance allowed

---

## 2.2 `service_variant_availability_rules`

### Purpose

Defines recurring availability patterns.

### Behavior

A rule is considered ACTIVE if:

* `active = true`
* `start_date <= query_date <= end_date` (if defined)
* `weekday_mask` matches query date

---

#### `weekday_mask`

Bitmask encoding:

| Day       | Value |
| --------- | ----- |
| Monday    | 1     |
| Tuesday   | 2     |
| Wednesday | 4     |
| Thursday  | 8     |
| Friday    | 16    |
| Saturday  | 32    |
| Sunday    | 64    |

Example:

```
Monday + Wednesday + Friday = 1 + 4 + 16 = 21
```

---

## 2.3 `service_variant_availability_time_slots`

### Purpose

Defines time-based availability within a rule.

### When used

ONLY when:

```
inventory_type IN (per_timeslot, per_departure)
```

---

### Key Fields

#### `start_time` / `end_time`

* Defines the time window of the slot

#### `capacity`

* Overrides `inventory_total` for this slot

#### `cutoff_minutes`

* Overrides variant-level cutoff

---

### Notes

* If no slots exist → variant is considered unavailable (for timeslot modes)
* Slots inherit rule constraints

---

## 2.4 `service_variant_availability_overrides`

### Purpose

Overrides availability for a specific date (and optionally time).

---

### Key Fields

#### `date`

* Target date

#### `start_time` (nullable)

* If NULL → applies to entire day
* If set → applies to specific slot

---

#### `capacity`

* Overrides capacity for that date/slot

#### `closed`

* If true → availability = 0

---

### Unique Constraint

```
(service_variant_id, date, start_time)
```

Ensures only one override per slot per day.

---

# 3. Availability Resolution Algorithm

## Step 1 — Check rules

* Find all active rules matching the date
* If none → NOT available

---

## Step 2 — Determine mode

### Case A: `unlimited`

```
return available = true
```

---

### Case B: `per_day`

1. Check override (date, NULL time)
2. If closed → unavailable
3. Capacity = override.capacity ?? inventory_total

---

### Case C: `per_timeslot` / `per_departure`

1. Find applicable rule
2. Get active slots
3. For each slot:

    * Check override (date + start_time)
    * If closed → skip slot
    * Compute capacity:

```
capacity =
    override.capacity ??
    slot.capacity ??
    inventory_total
```

---

# 4. Cutoff Resolution

```
effective_cutoff =
    slot.cutoff_minutes ??
    variant.cutoff_minutes
```

Booking must be rejected if:

```
now > (slot_start_time - cutoff)
```

---

# 5. Booking Constraints

A booking is valid only if:

* capacity is available
* `capacity_min <= quantity <= capacity_max`
* within advance booking window:

```
now >= start_time - min_advance_booking_hours
now <= start_time - max_advance_booking_days
```

---

# 6. Important Rules

## 6.1 No mixed modes

* If `per_day` → ignore time slots
* If `per_timeslot` → slots are mandatory

---

## 6.2 Overrides always win

* Overrides take precedence over ALL other configurations

---

## 6.3 Capacity is NOT cumulative

* Do NOT sum capacities from multiple sources
* Only use fallback chain

---

## 6.4 Availability != bookings

This model defines **maximum capacity**, not remaining capacity.

Remaining capacity requires:

```
remaining = capacity - booked_quantity
```

---

# 7. Recommended Validations

* Prevent slots when `inventory_type = per_day`
* Require slots when `inventory_type = per_timeslot`
* Ensure `capacity_min <= capacity_max`
* Ensure no overlapping slots within same rule

---

# 8. Future Extension Hooks

This model is compatible with:

* Allocation systems (supplier → reseller)
* Materialized availability tables
* Real-time booking engines

---

# 9. Summary

The system follows a strict layered resolution:

```
Rules → Slots → Overrides → Capacity Resolution
```

With deterministic fallback:

```
override → slot → variant
```

This ensures:

* Predictable behavior
* Extensibility
* Compatibility with complex distribution systems

```
```
