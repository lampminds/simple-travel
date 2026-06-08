<?php

namespace Database\Seeders;

use Database\Seeders\Support\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatServiceDetailConditionKeysTableSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    /**
     * Catalog keys for service detail conditions (category.code + English description).
     * Must run before CatServiceDetailTopicsTableSeeder.
     */
    public function run(): void
    {
        $this->withoutForeignKeyChecks(function (): void {
            DB::table('cat_service_detail_condition_keys')->delete();

            $rows = [];
            $id = 1;

            foreach ($this->sourceLines() as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                [$qualifiedCode, $description] = array_pad(explode("\t", $line, 2), 2, null);
                if ($qualifiedCode === null || $description === null) {
                    continue;
                }

                [$category, $code] = explode('.', $qualifiedCode, 2);

                $rows[] = [
                    'id' => $id++,
                    'code' => $code,
                    'category' => $category,
                    'description' => $description,
                ];
            }

            DB::table('cat_service_detail_condition_keys')->insert($rows);
        });
    }

    /**
     * @return list<string>
     */
    private function sourceLines(): array
    {
        return explode("\n", trim(<<<'TXT'
payment.cancellation_policy	Cancellation rules and deadlines.
payment.refund_policy	Refund eligibility and refund processing conditions.
payment.payment_terms	Payment schedules, installments, or due dates.
payment.deposit_required	Indicates that an advance payment or deposit is required.
payment.non_refundable	Service or rate cannot be refunded.
payment.no_show_policy	Rules applied when the passenger does not show up.
payment.modification_policy	Conditions for modifying dates, passengers, or services.
payment.rate_subject_to_change	Rate may change before confirmation.
payment.dynamic_pricing	Price may vary depending on demand or availability.
payment.fuel_surcharge	Additional fuel surcharge may apply.
payment.mandatory_fee	Mandatory additional fee payable locally.
payment.taxes_and_fees	Taxes, fees, and surcharges applicable to the rate.
operation.weather_dependent	Service operation depends on weather conditions.
operation.schedule_subject_to_change	Schedule may change without prior notice.
operation.itinerary_subject_to_change	Itinerary may change depending on operational conditions.
operation.operator_confirmation_required	Requires manual confirmation from operator.
operation.minimum_passengers	Minimum passenger requirement to operate the service.
operation.maximum_passengers	Maximum passenger capacity limitations.
operation.reconfirmation_required	Passenger must reconfirm before service date.
operation.time_slot_assignment	Final schedule assigned after confirmation.
operation.operational_delay	Delays may occur due to operational factors.
operation.arrival_recommendation	Recommended arrival or presentation timing.
operation.meeting_point	Meeting point instructions.
operation.boarding_time	Required boarding or presentation time before departure.
operation.duration	Service duration information.
operation.seasonal_operation	Service operates only during specific seasons.
operation.blackout_dates	Dates when the service is unavailable.
operation.subject_to_availability	Availability must be confirmed before booking.
operation.operating_hours	Days and times when the service operates.
transport.pickup_information	Pickup location, timing, or logistics.
transport.dropoff_information	Drop-off location or operational details.
transport.driver_waiting_time	Maximum waiting time for transfers or pickups.
transport.airport_transfer_conditions	Conditions related to airport transfers.
transport.flight_information_required	Passenger flight information is required.
transport.child_seat_required	Child seat requirements for transportation services.
transport.luggage_policy	Luggage size, weight, or quantity restrictions.
transport.transfer_schedule_coordination	Transfer schedules coordinated with external services.
transport.vehicle_information	Vehicle type, capacity, or fleet details.
transport.pickup_area	Geographic area or zones covered for pickup.
accommodation.check_in_time	Official check-in time.
accommodation.check_out_time	Official check-out time.
accommodation.early_check_in	Early check-in conditions or availability.
accommodation.late_check_out	Late check-out conditions or availability.
accommodation.room_configuration	Bed setup or room configuration conditions.
accommodation.room_assignment_subject_to_availability	Room allocation depends on availability at check-in.
accommodation.shared_bathroom	Accommodation includes shared bathroom facilities.
accommodation.bedding_policy	Extra bed or bedding conditions.
accommodation.hotel_policy	Accommodation-specific operational policies.
accommodation.room_cleaning	Room cleaning schedule or housekeeping policies.
safety.medical_restriction	Medical or health-related restrictions.
safety.pregnant_restriction	Restrictions for pregnant travelers.
safety.physical_requirement	Physical fitness or mobility requirements.
safety.life_jacket_required	Life jacket usage is mandatory.
safety.helmet_required	Helmet usage is mandatory.
safety.risk_activity	Indicates that the service involves inherent risks.
safety.altitude_warning	High altitude conditions warning.
safety.terrain_warning	Terrain or route difficulty warning.
safety.wildlife_warning	Possible wildlife encounters or related precautions.
safety.waiver_required	Liability waiver or consent form required.
safety.safety_information	General safety instructions or warnings.
legal.liability_limitation	Operator or provider liability limitations.
legal.force_majeure	Conditions related to force majeure events.
legal.local_regulation	Conditions imposed by local authorities or regulations.
legal.documentation_required	Required documents or identification.
legal.passport_required	Passport requirement information.
legal.visa_required	Visa requirement information.
legal.insurance_required	Mandatory insurance requirements.
legal.vaccination_required	Mandatory vaccination requirements.
legal.customs_requirements	Customs or import/export restrictions.
legal.border_crossing_requirements	Cross-border travel requirements.
legal.insurance_information	Insurance coverage or recommendation information.
legal.terms_and_conditions	General terms and conditions of the service.
legal.environmental_policy	Environmental or sustainability policies.
legal.park_rules	Rules and regulations of parks or protected areas.
inclusions.included_services	Services or items included in the rate.
inclusions.excluded_services	Services or items not included in the rate.
inclusions.meal_included	Meal inclusion details.
inclusions.drinks_included	Beverage inclusion details.
inclusions.transfer_included	Transfer service is included.
inclusions.transfer_not_included	Transfer service is not included.
inclusions.park_fee_not_included	Park entrance fee not included.
inclusions.tax_not_included	Taxes are excluded from the displayed price.
inclusions.gratuity_not_included	Tips or gratuities are not included.
inclusions.equipment_included	Included equipment or gear.
inclusions.equipment_required	Required equipment that passengers must bring.
traveler.age_restriction	Minimum or maximum allowed age.
traveler.child_policy	Rules related to children or minors.
traveler.infant_policy	Rules related to infants or babies.
traveler.senior_policy	Special conditions for senior passengers.
traveler.pet_policy	Pet-related conditions or restrictions.
traveler.smoking_policy	Smoking restrictions or permissions.
traveler.alcohol_policy	Alcohol consumption rules.
traveler.accessibility_information	Accessibility or reduced mobility information.
traveler.mobility_restriction	Restrictions related to limited mobility.
traveler.student_policy	Rules related to students or student discounts.
service.private_service	Service is private or exclusive.
service.shared_service	Service is shared with other passengers.
service.multilingual_service	Service is offered in multiple languages.
service.guide_language	Languages spoken by guides or staff.
service.optional_activity	Optional add-on activity information.
service.digital_voucher_accepted	Digital vouchers are accepted.
service.printed_voucher_required	Printed voucher is required.
service.ticket_delivery	Ticket or voucher delivery instructions.
service.internet_availability	Internet or Wi-Fi availability information.
service.electricity_information	Power outlets or voltage information.
service.photography_restriction	Photography or filming restrictions.
service.clothing_requirement	Recommended or mandatory clothing.
service.climate_conditions	Service affected by seasonal or climate conditions.
service.operator_discretion	Final operation subject to operator discretion.
service.emergency_contact	Emergency contact information.
service.what_to_bring	Items passengers should bring for the service.
service.operator_contact	Operator contact details for coordination.
service.support_information	Customer support or assistance information.
TXT
        ));
    }
}
