<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountSwitchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileInvitationController;
use App\Http\Controllers\ProviderDashboardController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\AccountSelectionController;
use App\Http\Controllers\OperatorDashboardController;
use App\Http\Controllers\AgencyDashboardController;
use App\Http\Controllers\ServiceWizardController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\SelectDashboardLaneController;
use App\Http\Controllers\SetLocaleController;
use App\Http\Controllers\DemoContactFormController;
use App\Http\Controllers\AccountCompanyController;
use App\Http\Controllers\AccountNotificationsController;
use App\Http\Controllers\AccountContactsController;
use App\Http\Controllers\AccountRelationshipController;
use App\Http\Controllers\AccountProviderPriceListController;
use App\Http\Controllers\AccountOperatorPriceListController;
use App\Http\Controllers\AccountOperatorPackageController;
use App\Http\Controllers\AccountExchangeRateController;
use App\Http\Controllers\AccountTransferVehicleTypesController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\AccountOperatorServiceOfferController;
use App\Http\Controllers\AccountProviderServiceOfferController;
use App\Http\Controllers\AccountServiceOfferHubController;
use App\Http\Controllers\AccountPackageOfferHubController;
use App\Http\Controllers\AccountOperatorPackageOfferController;
use App\Http\Controllers\AccountAgencyPackageOfferController;
use App\Http\Controllers\AccountAgencyReservationController;
use App\Http\Controllers\AccountOperatorReservationController;
use App\Http\Controllers\AccountProviderAllocationController;
use App\Http\Controllers\AccountAgencyClientsController;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\WelcomeCompanyController;
use App\Http\Controllers\AccountTasksController;
use App\Http\Controllers\TenantSite\HomeController;
use App\Http\Controllers\WebsiteImpersonationController;
use App\Support\AccountTypeCategoryIds;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__ . '/auth.php';

// Public home: SaaS landing on main host; tenant welcome when Host matches tenant website pattern
Route::get('/', HomeController::class)->name('home');

// Profile: auth only (no verified) so users can fix email before re-verifying after a change.
Route::middleware(['auth'])->group(function () {
    Route::post('account/switch', AccountSwitchController::class)->name('account.switch');
    Route::get('account/select', AccountSelectionController::class)->name('account.select');

    Route::get('account/access', [ProfileController::class, 'editAccess'])->name('account.access.edit');
    Route::put('account/access', [ProfileController::class, 'updateAccess'])->name('account.access.update');
    Route::put('account/access/password', [ProfileController::class, 'updateAccessPassword'])->name('account.access.password');

    Route::get('account/profile', [ProfileController::class, 'editProfile'])->name('account.profile.edit');
    Route::put('account/profile', [ProfileController::class, 'updateProfile'])->name('account.profile.update');
    Route::get('account/contact', [ProfileController::class, 'editContact'])->name('account.contact.edit');
    Route::put('account/contact', [ProfileController::class, 'updateContact'])->name('account.contact.update');
    Route::post('account/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('account.profile.avatar');
    Route::delete('account/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('account.profile.avatar.destroy');

    Route::get('account/invitations', [ProfileInvitationController::class, 'index'])->name('account.invitations.index');
    Route::get('account/invitations/employee', [ProfileInvitationController::class, 'employee'])->name('account.invitations.employee');
    Route::get('account/invitations/company', [ProfileInvitationController::class, 'company'])->name('account.invitations.company');
    Route::post('account/invitations/employee', [ProfileInvitationController::class, 'storeEmployee'])->name('account.invitations.store_employee');
    Route::post('account/invitations/company', [ProfileInvitationController::class, 'storeCompany'])->name('account.invitations.store_company');
    Route::post('account/invitations/{invitation}/resend', [ProfileInvitationController::class, 'resend'])
        ->name('account.invitations.resend');
    Route::post('account/invitations/{invitation}/revoke', [ProfileInvitationController::class, 'revoke'])
        ->name('account.invitations.revoke');

    Route::get('account/exchange-rates', [AccountExchangeRateController::class, 'index'])->name('account.exchange-rates.index');
    Route::get('account/exchange-rates/edit', [AccountExchangeRateController::class, 'edit'])->name('account.exchange-rates.edit');
    Route::post('account/exchange-rates', [AccountExchangeRateController::class, 'store'])->name('account.exchange-rates.store');

});

// One-time support link: open in another browser; no prior session required to redeem.
Route::get('impersonate/enter/{token}', [WebsiteImpersonationController::class, 'enter'])
    ->middleware('throttle:20,1')
    ->where('token', '[A-Za-z0-9]+')
    ->name('impersonate.website.enter');

// Auth-protected account routes (must be before catch-alls so they take precedence).
// 'verified' ensures users who registered via /register must confirm email before accessing.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('website/menu-placeholder/{missingRoute}', function (string $missingRoute) {
        return view('website.menu-route-placeholder', ['missingRoute' => $missingRoute]);
    })->name('website.menu.placeholder');

    Route::get('welcome-company', WelcomeCompanyController::class)->name('welcome.company');

    Route::get('account/dashboard', [RoutingController::class, 'secondLevel'])->name('account.dashboard')->defaults('first', 'account')->defaults('second', 'dashboard');
    Route::get('account/dashboard/lane/{lane}', SelectDashboardLaneController::class)
        ->name('account.dashboard.lane')
        ->where('lane', 'provider|operator|agency');
    Route::get('account/settings', [AccountSettingsController::class, 'edit'])->name('account.settings');
    Route::put('account/settings', [AccountSettingsController::class, 'update'])->name('account.settings.update');
    Route::get('account/company', [AccountCompanyController::class, 'edit'])->name('account.company.edit');
    Route::put('account/company', [AccountCompanyController::class, 'update'])->name('account.company.update');
    Route::get('account/company/cities/{cityId}', [AccountCompanyController::class, 'cityDetails'])->name('account.company.city.details');
    Route::get('account/cities/search', [AccountCompanyController::class, 'searchCities'])->name('account.cities.search');
    Route::get('account/tasks', [AccountTasksController::class, 'index'])->name('account.tasks.index');
    Route::get('account/notifications', [AccountNotificationsController::class, 'index'])->name('account.notifications.index');
    Route::post('account/notifications', [AccountNotificationsController::class, 'store'])->name('account.notifications.store');
    Route::post('account/notifications/{notification}/read', [AccountNotificationsController::class, 'markAsRead'])
        ->name('account.notifications.read');

    Route::get('account/relationships', [AccountRelationshipController::class, 'index'])->name('account.relationships.index');

    Route::get('account/clients', [AccountAgencyClientsController::class, 'index'])->name('account.clients.index');
    Route::get('account/clients/persons/create', [AccountAgencyClientsController::class, 'createPerson'])->name('account.clients.persons.create');
    Route::post('account/clients/persons', [AccountAgencyClientsController::class, 'storePerson'])->name('account.clients.persons.store');
    Route::get('account/clients/persons/{person}/edit', [AccountAgencyClientsController::class, 'editPerson'])->name('account.clients.persons.edit');
    Route::put('account/clients/persons/{person}', [AccountAgencyClientsController::class, 'updatePerson'])->name('account.clients.persons.update');
    Route::delete('account/clients/persons/{person}', [AccountAgencyClientsController::class, 'destroyPerson'])->name('account.clients.persons.destroy');
    Route::get('account/clients/organizations/create', [AccountAgencyClientsController::class, 'createOrganization'])->name('account.clients.organizations.create');
    Route::post('account/clients/organizations', [AccountAgencyClientsController::class, 'storeOrganization'])->name('account.clients.organizations.store');
    Route::get('account/clients/organizations/{organization}/edit', [AccountAgencyClientsController::class, 'editOrganization'])->name('account.clients.organizations.edit');
    Route::put('account/clients/organizations/{organization}', [AccountAgencyClientsController::class, 'updateOrganization'])->name('account.clients.organizations.update');
    Route::delete('account/clients/organizations/{organization}', [AccountAgencyClientsController::class, 'destroyOrganization'])->name('account.clients.organizations.destroy');

    Route::post('account/relationships/incoming/{invitation}/accept', [AccountRelationshipController::class, 'acceptIncomingInvitation'])
        ->name('account.relationships.incoming.accept');
    Route::post('account/relationships/incoming/{invitation}/decline', [AccountRelationshipController::class, 'declineIncomingInvitation'])
        ->name('account.relationships.incoming.decline');

    Route::get('account/contacts', [AccountContactsController::class, 'index'])->name('account.contacts.index');
    Route::get('account/contacts/{accountPerson}', [AccountContactsController::class, 'show'])->name('account.contacts.show');
    Route::post('account/contacts/{accountPerson}/message', [AccountContactsController::class, 'storeMessage'])
        ->name('account.contacts.message');
    Route::get('account/service-offers', [AccountServiceOfferHubController::class, 'index'])
        ->name('account.service-offers.index');

    Route::get('account/service-offers/operators/{operator}', [AccountProviderServiceOfferController::class, 'edit'])
        ->name('account.service-offers.operators.edit');
    Route::put('account/service-offers/operators/{operator}', [AccountProviderServiceOfferController::class, 'update'])
        ->name('account.service-offers.operators.update');
    Route::post('account/service-offers/operators/{operator}/offers/{offer}/revoke', [AccountProviderServiceOfferController::class, 'revoke'])
        ->name('account.service-offers.operators.revoke');

    Route::get('account/service-offers/{offer}/preview.pdf', [AccountOperatorServiceOfferController::class, 'previewPdf'])
        ->name('account.service-offers.preview-pdf');
    Route::post('account/service-offers/{offer}/accept', [AccountOperatorServiceOfferController::class, 'accept'])
        ->name('account.service-offers.accept');
    Route::post('account/service-offers/{offer}/reject', [AccountOperatorServiceOfferController::class, 'reject'])
        ->name('account.service-offers.reject');
    Route::post('account/service-offers/{offer}/availability', [AccountOperatorServiceOfferController::class, 'updateLinkedAvailability'])
        ->name('account.service-offers.linked-availability');

    Route::get('account/provider/service-offers', function (Request $request) {
        return redirect()->route('account.service-offers.index', array_merge($request->query(), ['as' => 'provider']));
    });
    Route::get('account/provider/service-offers/{operator}', function (Request $request, Account $operator) {
        return redirect()->route('account.service-offers.operators.edit', array_merge($request->query(), ['operator' => $operator]));
    });

    Route::get('account/operator/service-offers', function (Request $request) {
        return redirect()->route('account.service-offers.index', array_merge($request->query(), ['as' => 'operator']));
    });
    Route::post('account/operator/service-offers/{offer}/accept', [AccountOperatorServiceOfferController::class, 'accept']);
    Route::post('account/operator/service-offers/{offer}/reject', [AccountOperatorServiceOfferController::class, 'reject']);
    Route::post('account/operator/service-offers/{offer}/availability', [AccountOperatorServiceOfferController::class, 'updateLinkedAvailability']);

    Route::get('account/package-offers', [AccountPackageOfferHubController::class, 'index'])
        ->name('account.package-offers.index');
    Route::get('account/package-offers/agencies/{agency}', [AccountOperatorPackageOfferController::class, 'edit'])
        ->name('account.package-offers.agencies.edit');
    Route::put('account/package-offers/agencies/{agency}', [AccountOperatorPackageOfferController::class, 'update'])
        ->name('account.package-offers.agencies.update');
    Route::post('account/package-offers/agencies/{agency}/offers/{offer}/revoke', [AccountOperatorPackageOfferController::class, 'revoke'])
        ->name('account.package-offers.agencies.revoke');
    Route::get('account/package-offers/{offer}/preview.pdf', [AccountAgencyPackageOfferController::class, 'previewPdf'])
        ->name('account.package-offers.preview-pdf');
    Route::post('account/package-offers/{offer}/accept', [AccountAgencyPackageOfferController::class, 'accept'])
        ->name('account.package-offers.accept');
    Route::post('account/package-offers/{offer}/reject', [AccountAgencyPackageOfferController::class, 'reject'])
        ->name('account.package-offers.reject');

    Route::get('account/reservations', [AccountAgencyReservationController::class, 'index'])
        ->name('account.reservations.index');
    Route::get('account/reservations/create/{packageOffer}', [AccountAgencyReservationController::class, 'create'])
        ->name('account.reservations.create');
    Route::post('account/reservations', [AccountAgencyReservationController::class, 'store'])
        ->name('account.reservations.store');
    Route::get('account/reservations/{booking}', [AccountAgencyReservationController::class, 'show'])
        ->name('account.reservations.show');

    Route::get('account/operator/reservations', [AccountOperatorReservationController::class, 'index'])
        ->name('account.operator.reservations.index');
    Route::get('account/operator/reservations/{booking}', [AccountOperatorReservationController::class, 'show'])
        ->name('account.operator.reservations.show');
    Route::post('account/operator/reservations/{booking}/confirm', [AccountOperatorReservationController::class, 'confirm'])
        ->name('account.operator.reservations.confirm');
    Route::post('account/operator/reservations/{booking}/reject', [AccountOperatorReservationController::class, 'reject'])
        ->name('account.operator.reservations.reject');

    Route::permanentRedirect('account/price-lists', '/account/provider-price-lists');
    Route::permanentRedirect('account/price-lists/{tail}', '/account/provider-price-lists/{tail}')->where('tail', '.*');

    Route::get('account/provider-price-lists', [AccountProviderPriceListController::class, 'index'])->name('account.provider-price-lists.index');
    Route::get('account/provider-price-lists/create', [AccountProviderPriceListController::class, 'create'])->name('account.provider-price-lists.create');
    Route::post('account/provider-price-lists', [AccountProviderPriceListController::class, 'store'])->name('account.provider-price-lists.store');
    Route::get('account/provider-price-lists/{priceList}/assignments', [AccountProviderPriceListController::class, 'editAssignments'])
        ->name('account.provider-price-lists.assignments.edit');
    Route::put('account/provider-price-lists/{priceList}/assignments', [AccountProviderPriceListController::class, 'updateAssignments'])
        ->name('account.provider-price-lists.assignments.update');
    Route::get('account/provider-price-lists/{priceList}/edit', [AccountProviderPriceListController::class, 'edit'])->name('account.provider-price-lists.edit');
    Route::put('account/provider-price-lists/{priceList}', [AccountProviderPriceListController::class, 'update'])->name('account.provider-price-lists.update');
    Route::delete('account/provider-price-lists/{priceList}', [AccountProviderPriceListController::class, 'destroy'])->name('account.provider-price-lists.destroy');

    Route::get('account/allocations', [AccountProviderAllocationController::class, 'operatorsIndex'])->name('account.allocations.index');
    Route::get('account/allocations/operators/{operator}', [AccountProviderAllocationController::class, 'index'])->name('account.allocations.operators.index');
    Route::get('account/allocations/operators/{operator}/create', [AccountProviderAllocationController::class, 'create'])->name('account.allocations.operators.create');
    Route::post('account/allocations/operators/{operator}', [AccountProviderAllocationController::class, 'store'])->name('account.allocations.operators.store');
    Route::get('account/allocations/{allocation}/edit', [AccountProviderAllocationController::class, 'edit'])->name('account.allocations.edit');
    Route::put('account/allocations/{allocation}', [AccountProviderAllocationController::class, 'update'])->name('account.allocations.update');
    Route::delete('account/allocations/{allocation}', [AccountProviderAllocationController::class, 'destroy'])->name('account.allocations.destroy');

    Route::get('account/operator-price-lists', [AccountOperatorPriceListController::class, 'index'])->name('account.operator-price-lists.index');
    Route::get('account/operator-price-lists/create', [AccountOperatorPriceListController::class, 'create'])->name('account.operator-price-lists.create');
    Route::post('account/operator-price-lists', [AccountOperatorPriceListController::class, 'store'])->name('account.operator-price-lists.store');
    Route::post('account/operator-price-lists/preview-item', [AccountOperatorPriceListController::class, 'previewItem'])
        ->name('account.operator-price-lists.preview-item');
    Route::get('account/operator-price-lists/{operatorPriceList}/assignments', [AccountOperatorPriceListController::class, 'editAssignments'])
        ->name('account.operator-price-lists.assignments.edit');
    Route::put('account/operator-price-lists/{operatorPriceList}/assignments', [AccountOperatorPriceListController::class, 'updateAssignments'])
        ->name('account.operator-price-lists.assignments.update');
    Route::get('account/operator-price-lists/{operatorPriceList}/edit', [AccountOperatorPriceListController::class, 'edit'])->name('account.operator-price-lists.edit');
    Route::put('account/operator-price-lists/{operatorPriceList}', [AccountOperatorPriceListController::class, 'update'])->name('account.operator-price-lists.update');
    Route::delete('account/operator-price-lists/{operatorPriceList}', [AccountOperatorPriceListController::class, 'destroy'])->name('account.operator-price-lists.destroy');

    Route::get('account/operator-packages', [AccountOperatorPackageController::class, 'index'])->name('account.operator-packages.index');
    Route::get('account/operator-packages/create', [AccountOperatorPackageController::class, 'create'])->name('account.operator-packages.create');
    Route::post('account/operator-packages', [AccountOperatorPackageController::class, 'store'])->name('account.operator-packages.store');
    Route::post('account/operator-packages/translate-translations', [AccountOperatorPackageController::class, 'translateTranslations'])
        ->name('account.operator-packages.translate-translations');
    Route::get('account/operator-packages/offers', [AccountOperatorPackageController::class, 'offers'])->name('account.operator-packages.offers');
    Route::get('account/operator-packages/item-conditions', [AccountOperatorPackageController::class, 'itemConditions'])
        ->name('account.operator-packages.item-conditions');
    Route::get('account/operator-packages/{operatorPackage}/edit', [AccountOperatorPackageController::class, 'edit'])->name('account.operator-packages.edit');
    Route::put('account/operator-packages/{operatorPackage}', [AccountOperatorPackageController::class, 'update'])->name('account.operator-packages.update');
    Route::delete('account/operator-packages/{operatorPackage}', [AccountOperatorPackageController::class, 'destroy'])->name('account.operator-packages.destroy');

    Route::get('account/transfer-vehicle-types', [AccountTransferVehicleTypesController::class, 'index'])
        ->name('account.transfer-vehicle-types.index');
    Route::get('account/transfer-vehicle-types/create', [AccountTransferVehicleTypesController::class, 'create'])
        ->name('account.transfer-vehicle-types.create');
    Route::post('account/transfer-vehicle-types', [AccountTransferVehicleTypesController::class, 'store'])
        ->name('account.transfer-vehicle-types.store');
    Route::post('account/transfer-vehicle-types/import-from-template', [AccountTransferVehicleTypesController::class, 'import'])
        ->name('account.transfer-vehicle-types.import');
    Route::get('account/transfer-vehicle-types/{transfer_vehicle_type}/edit', [AccountTransferVehicleTypesController::class, 'edit'])
        ->name('account.transfer-vehicle-types.edit');
    Route::put('account/transfer-vehicle-types/{transfer_vehicle_type}', [AccountTransferVehicleTypesController::class, 'update'])
        ->name('account.transfer-vehicle-types.update');
    Route::patch('account/transfer-vehicle-types/{transfer_vehicle_type}/move/{direction}', [AccountTransferVehicleTypesController::class, 'move'])
        ->where('direction', 'up|down')
        ->name('account.transfer-vehicle-types.move');
    Route::delete('account/transfer-vehicle-types/{transfer_vehicle_type}', [AccountTransferVehicleTypesController::class, 'destroy'])
        ->name('account.transfer-vehicle-types.destroy');

    Route::get('relationships', fn () => redirect()->route('account.relationships.index'))->name('relationships');
    Route::get('clients', fn () => redirect()->route('account.clients.index'))->name('clients');
    Route::get('reservations', fn () => redirect()->route('account.reservations.index'))->name('reservations');
    Route::get('catalog', [CatalogController::class, 'index'])->name('catalog');

    // Account dashboards by account category (must be protected against public catch-alls).
    Route::prefix('provider')
        ->name('provider.')
        ->group(function () {
            Route::get('dashboard', [ProviderDashboardController::class, 'show'])
                ->defaults('menu_type_id', AccountTypeCategoryIds::PROVIDER)
                ->name('dashboard');
            Route::get('relationships', fn () => redirect()->route('account.relationships.index', ['as' => 'provider']))
                ->defaults('menu_type_id', AccountTypeCategoryIds::PROVIDER)
                ->name('relationships');
        });

    Route::prefix('operator')
        ->name('operator.')
        ->group(function () {
            Route::get('dashboard', [OperatorDashboardController::class, 'show'])
                ->defaults('menu_type_id', AccountTypeCategoryIds::OPERATOR)
                ->name('dashboard');
            Route::get('relationships', fn () => redirect()->route('account.relationships.index', ['as' => 'operator']))
                ->defaults('menu_type_id', AccountTypeCategoryIds::OPERATOR)
                ->name('relationships');
        });

    Route::prefix('agency')
        ->name('agency.')
        ->group(function () {
            Route::get('dashboard', [AgencyDashboardController::class, 'show'])
                ->defaults('menu_type_id', AccountTypeCategoryIds::AGENCY)
                ->name('dashboard');
            Route::get('clients', fn () => redirect()->route('account.clients.index'))
                ->defaults('menu_type_id', AccountTypeCategoryIds::AGENCY)
                ->name('clients');
            Route::get('relationships', fn () => redirect()->route('account.relationships.index', ['as' => 'agency']))
                ->defaults('menu_type_id', AccountTypeCategoryIds::AGENCY)
                ->name('relationships');
        });

    // Service wizard step 1 (create or edit).
    Route::get('services/wizard/{serviceType:code}/step-1', [ServiceWizardController::class, 'createStepOne'])
        ->name('services.wizard.step1');
    Route::get('services/wizard/{serviceType:code}/step-1/{service}', [ServiceWizardController::class, 'editStepOne'])
        ->name('services.wizard.step1.edit');
    Route::post('services/wizard/{serviceType:code}/step-1', [ServiceWizardController::class, 'storeStepOne'])
        ->name('services.wizard.step1.store');
    Route::put('services/wizard/{serviceType:code}/step-1/{service}', [ServiceWizardController::class, 'updateStepOne'])
        ->name('services.wizard.step1.update');
    Route::post('services/wizard/translate-descriptions', [ServiceWizardController::class, 'translateDescriptions'])
        ->name('services.wizard.translate-descriptions');
    Route::get('services/cities/search', [ServiceWizardController::class, 'searchCities'])
        ->name('services.cities.search');

    Route::get('services/wizard/{serviceType:code}/step-2/{service}', [ServiceWizardController::class, 'createStepTwo'])
        ->name('services.wizard.step2');

    Route::get('services/wizard/{serviceType:code}/step-3/{service}', [ServiceWizardController::class, 'createStepThree'])
        ->name('services.wizard.step3');

    Route::get('services/wizard/{serviceType:code}/step-4/{service}', [ServiceWizardController::class, 'createStepFour'])
        ->name('services.wizard.step4');

    Route::get('services/wizard/{serviceType:code}/step-5/{service}', [ServiceWizardController::class, 'createStepFive'])
        ->name('services.wizard.step5');

    Route::get('services/wizard/{serviceType:code}/step-6/{service}', [ServiceWizardController::class, 'createStepSix'])
        ->name('services.wizard.step6');

    Route::get('services/wizard/{serviceType:code}/step-7/{service}', [ServiceWizardController::class, 'createStepSeven'])
        ->name('services.wizard.step7');

    Route::get('services/wizard/{serviceType:code}/step-8/{service}', [ServiceWizardController::class, 'createStepEight'])
        ->name('services.wizard.step8');
});

// Website language switcher (must be before catch-all routes)
Route::get('locale/{language}', SetLocaleController::class)->name('locale');

// Theme demo: contact / helpcenter forms POST via JS to /contactus (see resources/site/public/assets/js/app.js)
Route::post('contactus', DemoContactFormController::class)->name('site.demo.contact');

// Pricing page (dynamic plans from database; must be before catch-all)
Route::get('pages/pricing', App\Http\Controllers\PricingController::class)->name('pages.pricing');
Route::get('pages/faq', App\Http\Controllers\FaqPageController::class)->name('pages.faq');

Route::get('api/faqs', [App\Http\Controllers\CatFaqController::class, 'index'])->name('api.faqs.index');

// Digitalizar operador turístico comparison page
Route::get('pages/digitalizar-operador-turistico', App\Http\Controllers\DigitalizarOperadorController::class)->name('pages.digitalizar-operador-turistico');

// Quick access page for original purchased template demos.
Route::view('template-demos', 'pages.template-demos')->name('template.demos');

Route::view('pages/about', 'pages.about')->name('pages.about');
Route::view('pages/privacy', 'pages.privacy')->name('pages.privacy');
Route::view('pages/terms', 'pages.terms')->name('pages.terms');

// Redirect old Filament resource URL (contact_roles renamed to contact_positions)
Route::get('smpl_adm/contact-roles', fn () => redirect('/smpl_adm/contact-positions', 301))
    ->name('smpl_adm.contact-roles.redirect');

// Local only: preview custom error pages (must be before catch-all routes).
if (app()->isLocal()) {
    Route::get('_errors/{code}', function (string $code) {
        $allowed = ['403', '404', '500', '503'];
        if (! in_array($code, $allowed, true)) {
            abort(404);
        }

        return response()->view("errors.{$code}", [], (int) $code);
    })
        ->where('code', '403|404|500|503')
        ->name('dev.error-preview');
}

// Public content routes (landings, pages, index, ui-kit, etc.)
Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
Route::get('{any}', [RoutingController::class, 'root'])->name('any');

