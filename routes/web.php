<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\SetLocaleController;

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

// Public landing / home page (no auth required) — uses the SaaS landing content
Route::get('/', function () {
    return view('landings.saas');
})->name('home');

// Auth-protected account routes (must be before catch-alls so they take precedence)
Route::middleware('auth')->group(function () {
    Route::get('account/dashboard', [RoutingController::class, 'secondLevel'])->name('account.dashboard')->defaults('first', 'account')->defaults('second', 'dashboard');
    Route::get('account/settings', [RoutingController::class, 'secondLevel'])->name('account.settings')->defaults('first', 'account')->defaults('second', 'settings');
});

// Website language switcher (must be before catch-all routes)
Route::get('locale/{language}', SetLocaleController::class)->name('locale');

// Pricing page (dynamic plans from database; must be before catch-all)
Route::get('pages/pricing', App\Http\Controllers\PricingController::class)->name('pages.pricing');

// Digitalizar operador turístico comparison page
Route::get('pages/digitalizar-operador-turistico', App\Http\Controllers\DigitalizarOperadorController::class)->name('pages.digitalizar-operador-turistico');

// Providers (prestadores) landing page
Route::get('providers', fn () => view('pages.providers', ['title' => 'Prestadores - Simple Travel']))->name('pages.providers');

// Public content routes (landings, pages, index, ui-kit, etc.)
Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
Route::get('{any}', [RoutingController::class, 'root'])->name('any');

