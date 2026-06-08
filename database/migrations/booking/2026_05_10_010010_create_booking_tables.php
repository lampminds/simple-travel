<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('booking_code')->unique()->comment('BK-YY-AAnnA: BK is fixed, YY is year, A is random ABCDEFGHJKMNPQRTWXYZ, nn is random 2346789');

            $table->foreignId('operator_id')->constrained('accounts');
            $table->foreignId('agency_id')->constrained('accounts');
            $table->foreignId('organization_id')->nullable()->comment('optional, if it is an organization')->constrained();

            $table->string('invitation_token')->nullable()->comment('token used to invite passengers to complete the booking');

            $table->foreignId('status_id')->constrained('cat_booking_statuses', 'id', 'bs_fk');
            $table->enum('booking_source', ['web', 'agency', 'operator', 'api', 'ota', 'admin'])->default('web');
            $table->date('travel_start_date');
            $table->date('travel_end_date');
            $table->json('passengers_snapshot')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->unsignedTinyInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('cat_currencies');

            $table->enum('billing_type', ['person', 'organization', 'agency'])->default('person')->comment('Billed to person_id (the holder), organization_id or agency_id');
            $table->foreignId('billing_person_id')->nullable()->constrained('persons');
            $table->foreignId('billing_organization_id')->nullable()->constrained('organizations');

            $table->json('remarks_internal')->nullable();
            $table->json('remarks_customer')->nullable();

            lmpStamps($table);

        });

        Schema::create('booking_passengers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('persons');

            // the below section is a snapshot of the person's data
            $table->string('full_name');
            $table->string('given_name')->nullable();
            $table->string('family_name')->nullable();
            $table->unsignedBigInteger('nationality_id')->nullable(); // refers to lmp_countries
            $table->date('date_of_birth')->nullable();
            $table->foreignId('gender_id')->nullable()->constrained('cat_genders');

            // IDs, email, phone, emergency_contact can be retrieved from booking_passenger_documents

            $table->enum('passenger_type', ['adult', 'child', 'infant', 'senior'])->default('adult');
            $table->enum('role', ['holder', 'passenger', 'leader'])->default('passenger');
            $table->enum('status', ['pending', 'completed', 'checked_in', 'cancelled'])->default('pending');
            $table->text('special_requirements')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('waiver_signed_at')->nullable();

            lmpStamps($table);

            $table->unique(['booking_id', 'person_id'], 'booking_passengers_unique');
        });

        Schema::create('booking_passenger_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_passenger_id')->constrained('booking_passengers', 'id', 'bp_documents_fk')->cascadeOnDelete();
            $table->foreignId('document_id')->constrained('cat_documents', 'id', 'bp_documents_doc_fk');
            $table->string('value')->comment('e.g. passport number');
            $table->unsignedBigInteger('nationality_id')->nullable(); // refers to lmp_countries

            lmpStamps($table);

            $table->unique(['booking_passenger_id', 'document_id'], 'booking_passenger_documents_unique');
        });

        Schema::create('booking_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('persons');
            $table->enum('role', ['secretary', 'parent', 'coordinator', 'other']);
            $table->text('notes')->nullable();

            lmpStamps($table);

            $table->unique(['booking_id', 'person_id', 'role'], 'booking_contacts_unique');
        }
        );

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('operator_package_item_id')->constrained('operator_package_items');
            $table->foreignId('status_id')->constrained('cat_booking_statuses', 'id', 'bsi_fk');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->unsignedTinyInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('cat_currencies');
            $table->string('confirmation_code')->nullable();
            $table->string('provider_reference')->nullable();
            $table->json('package_snapshot'); // mandatory

            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->json('remarks')->nullable();

            lmpStamps($table);

            $table->unique(
                ['booking_id', 'operator_package_item_id', 'start_date'],
                'booking_items_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_passenger_documents');
        Schema::dropIfExists('booking_passengers');
        Schema::dropIfExists('booking_contacts');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
    }
};
