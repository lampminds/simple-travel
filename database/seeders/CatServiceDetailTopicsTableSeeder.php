<?php

namespace Database\Seeders;

use Database\Seeders\Support\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatServiceDetailTopicsTableSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    /**
     * Seeds service detail topics with default condition keys and scope.
     * Requires CatServiceDetailConditionKeysTableSeeder to run first.
     *
     * Re-runs CatServiceDetailTopicTranslationsTableSeeder when finished.
     */
    public function run(): void
    {
        $this->withoutForeignKeyChecks(function (): void {
            $this->truncateTables([
                'cat_service_detail_topic_translations',
                'cat_service_detail_topics',
            ]);

            $conditionKeyIds = DB::table('cat_service_detail_condition_keys')
                ->get()
                ->mapWithKeys(fn (object $row): array => [
                    $row->category.'.'.$row->code => (int) $row->id,
                ])
                ->all();

            $rows = [];
            $id = 1;

            foreach ($this->topicDefinitions() as $definition) {
                $conditionKeyId = null;
                if (isset($definition['condition_key']) && $definition['condition_key'] !== null) {
                    $qualified = $definition['condition_key'];
                    if (! isset($conditionKeyIds[$qualified])) {
                        throw new \RuntimeException("Missing condition key [{$qualified}] for topic [{$definition['code']}].");
                    }
                    $conditionKeyId = $conditionKeyIds[$qualified];
                }

                $rows[] = [
                    'id' => $id++,
                    'code' => $definition['code'],
                    'service_detail_topic_category_id' => $definition['category_id'],
                    'visibility' => $definition['visibility'],
                    'scope' => $definition['scope'],
                    'sort_order' => 9999,
                    'active' => 1,
                    'condition_key_id' => $conditionKeyId,
                    'operator_override_mode' => 'none',
                ];
            }

            DB::table('cat_service_detail_topics')->insert($rows);

            $this->call(CatServiceDetailTopicTranslationsTableSeeder::class);
        });
    }

    /**
     * @return list<array{code: string, category_id: int, visibility: string, scope: string, condition_key: string|null}>
     */
    private function topicDefinitions(): array
    {
        return [
            // general_information (1)
            ['code' => 'service_description', 'category_id' => 1, 'visibility' => 'public', 'scope' => 'informational', 'condition_key' => null],
            ['code' => 'service_highlights', 'category_id' => 1, 'visibility' => 'public', 'scope' => 'informational', 'condition_key' => null],
            ['code' => 'service_included', 'category_id' => 1, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'inclusions.included_services'],
            ['code' => 'service_not_included', 'category_id' => 1, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'inclusions.excluded_services'],
            ['code' => 'service_recommendations', 'category_id' => 1, 'visibility' => 'public', 'scope' => 'informational', 'condition_key' => null],
            ['code' => 'important_information', 'category_id' => 1, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'service.operator_discretion'],
            ['code' => 'accessibility', 'category_id' => 1, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'traveler.accessibility_information'],

            // trade_policies (2)
            ['code' => 'sales_conditions', 'category_id' => 2, 'visibility' => 'operator', 'scope' => 'commercial', 'condition_key' => 'payment.payment_terms'],
            ['code' => 'payment_conditions', 'category_id' => 2, 'visibility' => 'operator', 'scope' => 'commercial', 'condition_key' => 'payment.payment_terms'],
            ['code' => 'deposit_policy', 'category_id' => 2, 'visibility' => 'operator', 'scope' => 'commercial', 'condition_key' => 'payment.deposit_required'],
            ['code' => 'rate_conditions', 'category_id' => 2, 'visibility' => 'operator', 'scope' => 'commercial', 'condition_key' => 'payment.rate_subject_to_change'],
            ['code' => 'taxes_and_fees', 'category_id' => 2, 'visibility' => 'public', 'scope' => 'commercial', 'condition_key' => 'payment.taxes_and_fees'],

            // policies (3)
            ['code' => 'cancellation_policy', 'category_id' => 3, 'visibility' => 'public', 'scope' => 'commercial', 'condition_key' => 'payment.cancellation_policy'],
            ['code' => 'modification_policy', 'category_id' => 3, 'visibility' => 'operator', 'scope' => 'commercial', 'condition_key' => 'payment.modification_policy'],
            ['code' => 'no_show_policy', 'category_id' => 3, 'visibility' => 'operator', 'scope' => 'commercial', 'condition_key' => 'payment.no_show_policy'],
            ['code' => 'refund_policy', 'category_id' => 3, 'visibility' => 'operator', 'scope' => 'commercial', 'condition_key' => 'payment.refund_policy'],

            // service_details (4)
            ['code' => 'operating_hours', 'category_id' => 4, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'operation.operating_hours'],
            ['code' => 'duration', 'category_id' => 4, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'operation.duration'],
            ['code' => 'availability_period', 'category_id' => 4, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'operation.subject_to_availability'],
            ['code' => 'seasonality', 'category_id' => 4, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'operation.seasonal_operation'],
            ['code' => 'meeting_point', 'category_id' => 4, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'operation.meeting_point'],
            ['code' => 'pickup_information', 'category_id' => 4, 'visibility' => 'operator', 'scope' => 'service', 'condition_key' => 'transport.pickup_information'],
            ['code' => 'dropoff_information', 'category_id' => 4, 'visibility' => 'operator', 'scope' => 'service', 'condition_key' => 'transport.dropoff_information'],

            // passenger_policies (5)
            ['code' => 'children_policy', 'category_id' => 5, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'traveler.child_policy'],
            ['code' => 'infant_policy', 'category_id' => 5, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'traveler.infant_policy'],
            ['code' => 'student_policy', 'category_id' => 5, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'traveler.student_policy'],
            ['code' => 'senior_policy', 'category_id' => 5, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'traveler.senior_policy'],
            ['code' => 'group_policy', 'category_id' => 5, 'visibility' => 'operator', 'scope' => 'service', 'condition_key' => 'operation.minimum_passengers'],
            ['code' => 'minimum_age', 'category_id' => 5, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'traveler.age_restriction'],
            ['code' => 'maximum_age', 'category_id' => 5, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'traveler.age_restriction'],

            // passenger_requirements (6)
            ['code' => 'physical_requirements', 'category_id' => 6, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'safety.physical_requirement'],
            ['code' => 'health_requirements', 'category_id' => 6, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'safety.medical_restriction'],
            ['code' => 'equipment_required', 'category_id' => 6, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'inclusions.equipment_required'],
            ['code' => 'documentation_required', 'category_id' => 6, 'visibility' => 'public', 'scope' => 'legal', 'condition_key' => 'legal.documentation_required'],

            // preparation (7)
            ['code' => 'what_to_bring', 'category_id' => 7, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'service.what_to_bring'],
            ['code' => 'what_to_wear', 'category_id' => 7, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'service.clothing_requirement'],
            ['code' => 'weather_conditions', 'category_id' => 7, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'operation.weather_dependent'],
            ['code' => 'safety_information', 'category_id' => 7, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'safety.safety_information'],

            // transport (8)
            ['code' => 'transport_included', 'category_id' => 8, 'visibility' => 'operator', 'scope' => 'service', 'condition_key' => 'inclusions.transfer_included'],
            ['code' => 'vehicle_information', 'category_id' => 8, 'visibility' => 'operator', 'scope' => 'service', 'condition_key' => 'transport.vehicle_information'],
            ['code' => 'pickup_area', 'category_id' => 8, 'visibility' => 'operator', 'scope' => 'service', 'condition_key' => 'transport.pickup_area'],
            ['code' => 'transfer_conditions', 'category_id' => 8, 'visibility' => 'operator', 'scope' => 'service', 'condition_key' => 'transport.airport_transfer_conditions'],

            // hotel_details (9)
            ['code' => 'checkin_time', 'category_id' => 9, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'accommodation.check_in_time'],
            ['code' => 'checkout_time', 'category_id' => 9, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'accommodation.check_out_time'],
            ['code' => 'late_checkout_policy', 'category_id' => 9, 'visibility' => 'operator', 'scope' => 'service', 'condition_key' => 'accommodation.late_check_out'],
            ['code' => 'early_checkin_policy', 'category_id' => 9, 'visibility' => 'operator', 'scope' => 'service', 'condition_key' => 'accommodation.early_check_in'],
            ['code' => 'room_cleaning', 'category_id' => 9, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'accommodation.room_cleaning'],
            ['code' => 'meal_plans', 'category_id' => 9, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'inclusions.meal_included'],
            ['code' => 'pets_policy', 'category_id' => 9, 'visibility' => 'public', 'scope' => 'service', 'condition_key' => 'traveler.pet_policy'],

            // legals (10)
            ['code' => 'liability_information', 'category_id' => 10, 'visibility' => 'public', 'scope' => 'legal', 'condition_key' => 'legal.liability_limitation'],
            ['code' => 'insurance_information', 'category_id' => 10, 'visibility' => 'operator', 'scope' => 'legal', 'condition_key' => 'legal.insurance_information'],
            ['code' => 'terms_and_conditions', 'category_id' => 10, 'visibility' => 'public', 'scope' => 'legal', 'condition_key' => 'legal.terms_and_conditions'],

            // assistance (11)
            ['code' => 'emergency_contact', 'category_id' => 11, 'visibility' => 'operator', 'scope' => 'service', 'condition_key' => 'service.emergency_contact'],
            ['code' => 'operator_contact', 'category_id' => 11, 'visibility' => 'operator', 'scope' => 'service', 'condition_key' => 'service.operator_contact'],
            ['code' => 'support_information', 'category_id' => 11, 'visibility' => 'operator', 'scope' => 'service', 'condition_key' => 'service.support_information'],

            // local_regulations (12)
            ['code' => 'environmental_policy', 'category_id' => 12, 'visibility' => 'public', 'scope' => 'legal', 'condition_key' => 'legal.environmental_policy'],
            ['code' => 'local_regulations', 'category_id' => 12, 'visibility' => 'public', 'scope' => 'legal', 'condition_key' => 'legal.local_regulation'],
            ['code' => 'park_rules', 'category_id' => 12, 'visibility' => 'public', 'scope' => 'legal', 'condition_key' => 'legal.park_rules'],

            // internal_operations (13) — visible only on the "internal" wizard tab
            ['code' => 'internal_general_notes', 'category_id' => 13, 'visibility' => 'internal', 'scope' => 'informational', 'condition_key' => null],
            ['code' => 'provider_field_instructions', 'category_id' => 13, 'visibility' => 'internal', 'scope' => 'service', 'condition_key' => null],
            ['code' => 'booking_confirmation_workflow', 'category_id' => 13, 'visibility' => 'internal', 'scope' => 'service', 'condition_key' => 'operation.operator_confirmation_required'],
            ['code' => 'reconfirmation_workflow', 'category_id' => 13, 'visibility' => 'internal', 'scope' => 'service', 'condition_key' => 'operation.reconfirmation_required'],
            ['code' => 'schedule_assignment_notes', 'category_id' => 13, 'visibility' => 'internal', 'scope' => 'service', 'condition_key' => 'operation.time_slot_assignment'],
            ['code' => 'itinerary_change_notes', 'category_id' => 13, 'visibility' => 'internal', 'scope' => 'service', 'condition_key' => 'operation.itinerary_subject_to_change'],
            ['code' => 'schedule_change_notes', 'category_id' => 13, 'visibility' => 'internal', 'scope' => 'service', 'condition_key' => 'operation.schedule_subject_to_change'],
            ['code' => 'supplier_reference', 'category_id' => 13, 'visibility' => 'internal', 'scope' => 'commercial', 'condition_key' => null],
            ['code' => 'internal_pricing_notes', 'category_id' => 13, 'visibility' => 'internal', 'scope' => 'commercial', 'condition_key' => null],
            ['code' => 'resource_allocation_notes', 'category_id' => 13, 'visibility' => 'internal', 'scope' => 'service', 'condition_key' => null],
        ];
    }
}
