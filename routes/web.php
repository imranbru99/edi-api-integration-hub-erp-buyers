<?php

use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\ApiIntegrationController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\DemoController;
use App\Http\Controllers\Web\DevelopersController;
use App\Http\Controllers\Web\DocsController;
use App\Http\Controllers\Web\EdiGatewayController;
use App\Http\Controllers\Web\EdiStandardsController;
use App\Http\Controllers\Web\ErpIntegrationsController;
use App\Http\Controllers\Web\FaqController;
use App\Http\Controllers\Web\FeaturesController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ModulesController;
use App\Http\Controllers\Web\PricingController;
use App\Http\Controllers\Web\SecurityController;
use App\Http\Controllers\Web\SolutionsController;
use App\Models\EdiTransaction;
use Illuminate\Support\Facades\Route;

Route::bind('transaction', fn (string $value) => EdiTransaction::withoutGlobalScopes()->findOrFail($value));

Route::get('/', HomeController::class)->name('home');
Route::get('/features', FeaturesController::class)->name('features');
Route::get('/edi-gateway', EdiGatewayController::class)->name('edi-gateway');
Route::get('/edi-standards', [EdiStandardsController::class, 'index'])->name('edi-standards');
Route::get('/edi-standards/{code}', [EdiStandardsController::class, 'show'])->name('edi-standards.show');
Route::get('/erp-integrations', [ErpIntegrationsController::class, 'index'])->name('erp-integrations');
Route::get('/erp-integrations/{slug}', [ErpIntegrationsController::class, 'show'])->name('erp-integrations.show');
Route::get('/api-integration', ApiIntegrationController::class)->name('api-integration');
Route::get('/modules', [ModulesController::class, 'index'])->name('modules');
Route::get('/modules/{code}', [ModulesController::class, 'show'])->name('modules.show');
Route::get('/solutions/{industry}', [SolutionsController::class, 'show'])->name('solutions.show');
Route::get('/pricing', PricingController::class)->name('pricing');
Route::get('/security', SecurityController::class)->name('security');
Route::get('/developers', DevelopersController::class)->name('developers');
Route::get('/faq', FaqController::class)->name('faq');
Route::get('/about', AboutController::class)->name('about');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/docs', [DocsController::class, 'index'])->name('docs');
Route::get('/docs/{page}', [DocsController::class, 'show'])->name('docs.show');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('demo')->name('demo.')->group(function () {
    Route::get('/', [DemoController::class, 'index'])->name('index');
    Route::get('/login', [DemoController::class, 'showLogin'])->name('login');
    Route::post('/login', [DemoController::class, 'login'])->name('login.attempt');

    Route::middleware('auth')->group(function () {
        Route::get('/transactions', [DemoController::class, 'transactions'])->name('transactions');
        Route::get('/transactions/{transaction}', [DemoController::class, 'showTransaction'])->name('transactions.show');
        Route::get('/transactions/{transaction}/inspect/{layer}', [DemoController::class, 'inspect'])->name('transactions.inspect');
        Route::post('/transactions/{transaction}/simulate-delivery', [DemoController::class, 'simulateDelivery'])->name('transactions.simulate-delivery');
        Route::get('/transactions/{transaction}/timeline', [DemoController::class, 'timeline'])->name('transactions.timeline');
        Route::post('/reset', [DemoController::class, 'reset'])->name('reset');
    });
});
