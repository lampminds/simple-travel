<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountSwitchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileInvitationController;
use App\Http\Controllers\ProviderDashboardController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\AccountSelectionController;
use App\Http\Controllers\OperatorDashboardController;
use App\Http\Controllers\RelationshipsDemoController;
use App\Http\Controllers\ServiceWizardController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\SelectDashboardLaneController;
use App\Http\Controllers\SetLocaleController;
use App\Http\Controllers\DemoContactFormController;
use App\Http\Controllers\AccountCompanyController;
use App\Http\Controllers\WelcomeCompanyController;
use App\Http\Controllers\AccountTasksController;
use App\Http\Controllers\TenantSite\HomeController;
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

    Route::get('account/profile', [ProfileController::class, 'edit'])->name('account.profile.edit');
    Route::put('account/profile', [ProfileController::class, 'update'])->name('account.profile.update');
    Route::put('account/profile/password', [ProfileController::class, 'updatePassword'])->name('account.profile.password');
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
});

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
    Route::get('account/settings', [RoutingController::class, 'secondLevel'])->name('account.settings')->defaults('first', 'account')->defaults('second', 'settings');
    Route::get('account/company', [AccountCompanyController::class, 'edit'])->name('account.company.edit');
    Route::put('account/company', [AccountCompanyController::class, 'update'])->name('account.company.update');
    Route::get('account/company/cities/{cityId}', [AccountCompanyController::class, 'cityDetails'])->name('account.company.city.details');
    Route::get('account/tasks', [AccountTasksController::class, 'index'])->name('account.tasks.index');
    Route::get('relationships', [RelationshipsDemoController::class, 'index'])->name('relationships');
    Route::get('catalog', [CatalogController::class, 'index'])->name('catalog');

    // Account dashboards by account category (must be protected against public catch-alls).
    Route::prefix('provider')
        ->name('provider.')
        ->group(function () {
            Route::get('dashboard', [ProviderDashboardController::class, 'show'])
                ->defaults('menu_type_id', AccountTypeCategoryIds::PROVIDER)
                ->name('dashboard');
            Route::get('relationships', [RelationshipsDemoController::class, 'provider'])
                ->defaults('menu_type_id', AccountTypeCategoryIds::PROVIDER)
                ->name('relationships');
        });

    Route::prefix('operator')
        ->name('operator.')
        ->group(function () {
            Route::get('dashboard', [OperatorDashboardController::class, 'show'])
                ->defaults('menu_type_id', AccountTypeCategoryIds::OPERATOR)
                ->name('dashboard');
            Route::get('relationships', [RelationshipsDemoController::class, 'operator'])
                ->defaults('menu_type_id', AccountTypeCategoryIds::OPERATOR)
                ->name('relationships');
        });

    Route::prefix('agency')
        ->name('agency.')
        ->group(function () {
            Route::get('dashboard', [RoutingController::class, 'secondLevel'])
                ->defaults('menu_type_id', AccountTypeCategoryIds::AGENCY)
                ->name('dashboard')
                ->defaults('first', 'agency')
                ->defaults('second', 'dashboard');
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
});

// Website language switcher (must be before catch-all routes)
Route::get('locale/{language}', SetLocaleController::class)->name('locale');

// Theme demo: contact / helpcenter forms POST via JS to /contactus (see resources/site/public/assets/js/app.js)
Route::post('contactus', DemoContactFormController::class)->name('site.demo.contact');

// Pricing page (dynamic plans from database; must be before catch-all)
Route::get('pages/pricing', App\Http\Controllers\PricingController::class)->name('pages.pricing');

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

