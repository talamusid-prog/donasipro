<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Admin\BankAccountController as AdminBankAccountController;
use App\Http\Controllers\Admin\AppSettingController as AdminAppSettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TripayChannelController;

use App\Http\Controllers\Admin\WhatsAppTemplateController;
use App\Http\Controllers\Admin\WABlastController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\AminController;
use App\Models\BankAccount;
use App\Services\TripayService;

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

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns');
Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
Route::get('/campaigns/{campaign}/donate', [CampaignController::class, 'donate'])->name('campaigns.donate');

// Donation routes
Route::post('/campaigns/{campaign}/donate', [DonationController::class, 'store'])->name('campaigns.donate.store');
Route::get('/donations/{donation}/payment', [DonationController::class, 'payment'])->name('donations.payment');
Route::get('/donations/{donation}/success', [DonationController::class, 'success'])->name('donations.success');
Route::get('/donations/{donation}/payment-detail', function($donationId) {
    $donation = App\Models\Donation::findOrFail($donationId);
    $campaign = $donation->campaign;
    return view('donations.payment-detail', compact('donation', 'campaign'));
})->name('donations.payment-detail');

Route::post('/donations/{donation}/upload-proof', [DonationController::class, 'uploadProof'])->name('donations.upload-proof');

Route::get('/donations/{donation}/verification', function($donationId) {
    $donation = App\Models\Donation::findOrFail($donationId);
    $campaign = $donation->campaign;
    return view('donations.verification', compact('donation', 'campaign'));
})->name('donations.verification');

// Amin routes
Route::post('/amin/toggle', [AminController::class, 'toggleAmin'])->name('amin.toggle');
Route::get('/amin/status', [AminController::class, 'getAminStatus'])->name('amin.status');

// Static pages
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/help', function () {
    return view('help');
})->name('help');

Route::get('/zakat-calculator', function () {
    return view('zakat-calculator');
})->name('zakat-calculator');

Route::get('/tunaikan-zakat-mal', function () {
    return view('tunaikan-zakat-mal');
})->name('tunaikan-zakat-mal');

Route::get('/tunaikan-zakat-penghasilan', function () {
    return view('tunaikan-zakat-penghasilan');
})->name('tunaikan-zakat-penghasilan');

Route::get('/donasi-zakat-mal/{amount?}', function ($amount = null) {
    $bankAccounts = \App\Models\BankAccount::active()->orderBy('bank_name')->get();
    $tripayService = new \App\Services\TripayService();
    $tripayChannels = $tripayService->getEnabledPaymentMethods();
    return view('donasi-zakat-mal', compact('amount', 'bankAccounts', 'tripayChannels'));
})->name('donasi-zakat-mal');

Route::get('/donasi-zakat-penghasilan/{amount?}', function ($amount = null) {
    $bankAccounts = \App\Models\BankAccount::active()->orderBy('bank_name')->get();
    $tripayService = new \App\Services\TripayService();
    $tripayChannels = $tripayService->getEnabledPaymentMethods();
    return view('donasi-zakat-penghasilan', compact('amount', 'bankAccounts', 'tripayChannels'));
})->name('donasi-zakat-penghasilan');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    // Tambahkan route update profil
    Route::put('/profile', function (Illuminate\Http\Request $request) {
        $user = auth()->user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();
        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    })->name('profile.update');

    // Tambahkan route update password
    Route::put('/profile/password', function (Illuminate\Http\Request $request) {
        $user = auth()->user();
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user->password = bcrypt($request->input('password'));
        $user->save();
        return redirect()->route('profile')->with('success', 'Password berhasil diubah.');
    })->name('password.update');
    
    Route::get('/my-donations', function () {
        return view('my-donations');
    })->name('my-donations');
});

// Admin Login
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);

// Admin Dashboard (hanya untuk admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin Campaign Management
    Route::resource('campaigns', AdminCampaignController::class);
    Route::post('/campaigns/upload-image', [AdminCampaignController::class, 'uploadImage'])->name('campaigns.upload-image');
    
    // Admin Category Management
    Route::resource('categories', AdminCategoryController::class);
    
    // Admin Donation Management
    Route::get('/donations', [AdminDonationController::class, 'index'])->name('donations.index');
    Route::get('/donations/{donation}', [AdminDonationController::class, 'show'])->name('donations.show');
    Route::patch('/donations/{donation}/status', [AdminDonationController::class, 'updateStatus'])->name('donations.update-status');
    Route::post('/donations/{donation}/confirm', [AdminDonationController::class, 'confirm'])->name('donations.confirm');
    Route::post('/donations/{donation}/reject', [AdminDonationController::class, 'reject'])->name('donations.reject');
    Route::get('/donations/export', [AdminDonationController::class, 'export'])->name('donations.export');
    
    // Admin User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.update-role');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Admin Slider Management
    Route::resource('sliders', AdminSliderController::class);

    // Admin Bank Account Management
    Route::resource('bank-accounts', AdminBankAccountController::class);

    // Admin App Settings Management
    Route::get('/settings', [AdminAppSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminAppSettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/reset', [AdminAppSettingController::class, 'reset'])->name('settings.reset');
    Route::post('/wa-blast/test-connection', [WABlastController::class, 'testConnection'])->name('wa-blast.test-connection');

    // Tripay Channels Management
    Route::get('/tripay-channels', [TripayChannelController::class, 'index'])->name('tripay-channels.index');
    Route::post('/tripay-channels/sync', [TripayChannelController::class, 'sync'])->name('tripay-channels.sync');
    Route::post('/tripay-channels/{channel}/toggle', [TripayChannelController::class, 'toggle'])->name('tripay-channels.toggle');
    Route::post('/tripay-channels/bulk-toggle', [TripayChannelController::class, 'bulkToggle'])->name('tripay-channels.bulk-toggle');
    Route::put('/tripay-channels/{channel}', [TripayChannelController::class, 'update'])->name('tripay-channels.update');
    
    // Tripay Settings
    Route::put('/tripay-settings', [TripayChannelController::class, 'updateSettings'])->name('tripay-settings.update');
    Route::post('/tripay-channels/test-connection', [TripayChannelController::class, 'testConnection'])->name('tripay-channels.test-connection');
    


    // WhatsApp Template Management
    Route::prefix('whatsapp-templates')->name('whatsapp-templates.')->group(function () {
        Route::get('/', [WhatsAppTemplateController::class, 'index'])->name('index');
        Route::get('/create', [WhatsAppTemplateController::class, 'create'])->name('create');
        Route::post('/', [WhatsAppTemplateController::class, 'store'])->name('store');
        Route::get('/{id}', [WhatsAppTemplateController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [WhatsAppTemplateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [WhatsAppTemplateController::class, 'update'])->name('update');
        Route::delete('/{id}', [WhatsAppTemplateController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-active', [WhatsAppTemplateController::class, 'toggleActive'])->name('toggle-active');
    });

    // WA Blast API Management
    Route::prefix('wa-blast')->name('wa-blast.')->group(function () {
        Route::get('/', [WABlastController::class, 'index'])->name('index');
        Route::post('/test-connection', [WABlastController::class, 'testConnection'])->name('test-connection');
        Route::post('/send-test', [WABlastController::class, 'sendTestMessage'])->name('send-test');
        Route::post('/send-template-test', [WABlastController::class, 'sendTemplateTest'])->name('send-template-test');
        Route::get('/templates', [WABlastController::class, 'templates'])->name('templates');
        Route::get('/settings', [WABlastController::class, 'settings'])->name('settings');
        Route::put('/update-settings', [WABlastController::class, 'updateSettings'])->name('update-settings');
        
        // Template management routes
        Route::post('/test-template', [WABlastController::class, 'testTemplate'])->name('test-template');
        Route::get('/templates/{id}', [WABlastController::class, 'getTemplate'])->name('get-template');
        Route::post('/templates', [WABlastController::class, 'storeTemplate'])->name('store-template');
        Route::put('/templates/{id}', [WABlastController::class, 'updateTemplate'])->name('update-template');
        Route::delete('/templates/{id}', [WABlastController::class, 'deleteTemplate'])->name('delete-template');
    });
});

// WhatsApp Webhook Routes (optional)
Route::get('/api/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify'])->name('whatsapp.webhook.verify');
Route::post('/api/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle'])->name('whatsapp.webhook.handle');
