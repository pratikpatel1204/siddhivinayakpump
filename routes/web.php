<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RedeemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\GiftController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "Cache cleared!";
});

Route::get('/get-path', function () {
    return base_path();
});

// Authentication routes
Route::get('/', [AuthController::class, 'showLoginForm']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/expired/reward', [AuthController::class, 'expired_reward'])->name('expired.reward');

// Routes that require authentication
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'showdashboard'])->name('show.dashboard');
    
    Route::get('/profile', [AuthController::class, 'showprofile'])->name('show.profile');
    Route::post('/password-update', [AuthController::class, 'updatePassword'])->name('password.update');

    Route::get('/redeem', [RedeemController::class, 'showredeem'])->name('show.redeem');
    Route::post('/redeem-check', [RedeemController::class, 'redeem_check'])->name('redeem.check');
    Route::post('/redeem-update', [RedeemController::class, 'redeem_update'])->name('redeem.update');

    Route::get('/redeem-reward-history', [RedeemController::class, 'redeem_reward_history'])->name('redeem.reward.history');

    Route::get('/gift', [GiftController::class, 'showgift'])->name('show.gift');
    Route::post('/redeem-gift-check', [GiftController::class, 'redeem_gift_check'])->name('redeem.gift.check');
    Route::post('/redeem-gift-update', [GiftController::class, 'redeem_gift_update'])->name('redeem.gift.update');
    
    Route::get('/redeem-gift-history', [GiftController::class, 'redeem_gift_history'])->name('redeem.gift.history');

    Route::get('/add-customer', [CustomerController::class, 'add_customer'])->name('add.customer');
    Route::post('/save-customer', [CustomerController::class, 'save_customer'])->name('save.customer');

    Route::get('/show-expired-reward-point-list', [ReportController::class, 'show_expired_reward_point_list'])->name('show.expired.reward.point.list');
    
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::post('/send-whatsapp', [DashboardController::class, 'sendWhatsApp'])->name('send.whatsapp');
        
        Route::get('/employee-list', [EmployeeController::class, 'employee_list'])->middleware('permission:view')->name('show.employee.list');
        Route::get('/add-employee', [EmployeeController::class, 'add_employee'])->middleware('permission:create')->name('add.employee');
        Route::post('/store-employee', [EmployeeController::class, 'store_employee'])->middleware('permission:create')->name('store.employee');
        Route::get('/edit-employee/{id}', [EmployeeController::class, 'edit_employee'])->middleware('permission:edit')->name('edit.employee');
        Route::post('/update-employee', [EmployeeController::class, 'update_employee'])->middleware('permission:edit')->name('update.employee');
        Route::post('/destroy-employee', [EmployeeController::class, 'destroy_employee'])->middleware('permission:delete')->name('destroy.employee');
               
        Route::get('/employee-permissions', [PermissionsController::class, 'showemployeePermissions'])->name('employee.permissions');
        Route::post('/employee/permissions', [PermissionsController::class, 'updateemployeePermissions'])->name('employee.update.permissions');
        
        Route::get('/customer-list', [CustomerController::class, 'show_customer_list'])->name('customer.list');
        Route::get('/create-customer', [CustomerController::class, 'create_customer'])->name('create.customer');
        Route::post('/store-customer', [CustomerController::class, 'store_customer'])->name('store.customer');
        Route::post('/destroy-customer', [CustomerController::class, 'destroy_customer'])->name('destroy.customer');
        Route::post('/customer/update-status', [CustomerController::class, 'updateStatus'])->name('customer.updateStatus');
        Route::get('/edit-customer/{id}', [CustomerController::class, 'edit_customer'])->name('edit.customer');
        Route::post('/update-customer', [CustomerController::class, 'update_customer'])->name('update.customer');
        
        Route::get('/reward-master', [RewardController::class, 'show_reward_master'])->name('reward.master');
        Route::get('/edit-reward-master/{id}', [RewardController::class, 'edit_reward_master'])->name('edit.reward.master');
        Route::post('/update-reward-master', [RewardController::class, 'update_reward_master'])->name('update.reward.master');
        
        Route::get('/service-master', [ServiceController::class, 'show_service_master'])->name('service.master');
        Route::get('/create-service-master', [ServiceController::class, 'create_service_master'])->name('create.service.master');
        Route::post('/store-service-master', [ServiceController::class, 'store_service_master'])->name('store.service.master');
        Route::get('/edit-service-master/{id}', [ServiceController::class, 'edit_service_master'])->name('edit.service.master');
        Route::post('/update-service-master', [ServiceController::class, 'update_service_master'])->name('update.service.master');
        Route::post('/destroy-service-master', [ServiceController::class, 'destroy_service_master'])->name('destroy.service.master');
        
        Route::get('/reward-management', [RewardController::class, 'show_reward_management'])->name('reward.management');

        Route::get('/gift-list', [GiftController::class, 'gift_list'])->name('gift.list');
        
        Route::get('/reward-report-list', [ReportController::class, 'reward_report_list'])->name('show.reward.report.list');
        Route::get('/report-reward-filter', [ReportController::class, 'filterRewardReport'])->name('report.reward.filter');
        
        Route::get('/gift-report-list', [ReportController::class, 'gift_report_list'])->name('show.gift.report.list');
        
        Route::get('/all-report-list', [ReportController::class, 'all_report_list'])->name('show.all.report.list');
        Route::get('/all-report-reward-filter', [ReportController::class, 'all_report_reward_filter'])->name('all.report.reward.filter');
        Route::get('/all-report-gift-filter', [ReportController::class, 'all_report_gift_filter'])->name('all.report.gift.filter');
        
        Route::get('/customer-report', [ReportController::class, 'customer_report'])->name('customer.report');
        Route::post('/search-customer-report', [ReportController::class, 'search_customer_report'])->name('search.customer.report');
        Route::get('/WhatsApp/{ids}', [RedeemController::class, 'autosendWhatsApp']);
    });

    // Employee-specific routes
    Route::middleware(['role:employee'])->group(function () {       
    });
});