<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountSwitchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileInvitationController;
use App\Http\Controllers\ProviderDashboardController;
use App\Http\Controllers\ServiceWizardController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\SetLocaleController;
use App\Http\Controllers\DemoContactFormController;
use App\Http\Controllers\TenantSite\HomeController;

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

    Route::get('account/profile', [ProfileController::class, 'edit'])->name('account.profile.edit');
    Route::put('account/profile', [ProfileController::class, 'update'])->name('account.profile.update');
    Route::put('account/profile/password', [ProfileController::class, 'updatePassword'])->name('account.profile.password');
    Route::post('account/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('account.profile.avatar');
    Route::delete('account/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('account.profile.avatar.destroy');

    Route::get('account/invitations', [ProfileInvitationController::class, 'index'])->name('account.invitations.index');
    Route::post('account/invitations', [ProfileInvitationController::class, 'store'])->name('account.invitations.store');
    Route::post('account/invitations/{invitation}/revoke', [ProfileInvitationController::class, 'revoke'])
        ->name('account.invitations.revoke');
});

// Auth-protected account routes (must be before catch-alls so they take precedence).
// 'verified' ensures users who registered via /register must confirm email before accessing.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('account/dashboard', [RoutingController::class, 'secondLevel'])->name('account.dashboard')->defaults('first', 'account')->defaults('second', 'dashboard');
    Route::get('account/settings', [RoutingController::class, 'secondLevel'])->name('account.settings')->defaults('first', 'account')->defaults('second', 'settings');

    // Account dashboards by account category (must be protected against public catch-alls).
    Route::get('provider/dashboard', [ProviderDashboardController::class, 'show'])
        ->name('provider.dashboard');

    Route::get('operator/dashboard', [RoutingController::class, 'secondLevel'])
        ->name('operator.dashboard')
        ->defaults('first', 'operator')
        ->defaults('second', 'dashboard');

    Route::get('agency/dashboard', [RoutingController::class, 'secondLevel'])
        ->name('agency.dashboard')
        ->defaults('first', 'agency')
        ->defaults('second', 'dashboard');

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
});

// Website language switcher (must be before catch-all routes)
Route::get('locale/{language}', SetLocaleController::class)->name('locale');

// Theme demo: contact / helpcenter forms POST via JS to /contactus (see resources/site/public/assets/js/app.js)
Route::post('contactus', DemoContactFormController::class)->name('site.demo.contact');

// Pricing page (dynamic plans from database; must be before catch-all)
Route::get('pages/pricing', App\Http\Controllers\PricingController::class)->name('pages.pricing');

// Digitalizar operador turístico comparison page
Route::get('pages/digitalizar-operador-turistico', App\Http\Controllers\DigitalizarOperadorController::class)->name('pages.digitalizar-operador-turistico');

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

