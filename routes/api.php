<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');

Route::group([
    "middleware"    => 'jwt.verify'
], function(){
    Route::delete('logout', 'AuthController@logout');
    Route::get('me', 'AuthController@me');

    // ===================
    // Finance Account
    // ===================
    Route::get("finance-accounts", "FinanceAccountController@financeAccounts");
    Route::post("finance-account", "FinanceAccountController@createFinanceAccounts");
    Route::put("finance-account/{id}", "FinanceAccountController@createFinanceAccounts");
    Route::delete("finance-account/{id}", "FinanceAccountController@deleteFinanceAccount");
    Route::get("finance-account/{id}", "FinanceAccountController@detailFinanceAccount");
    Route::put("finance-account/{id}/restore", "FinanceAccountController@restoreFinanceAccount");
    // ===================

    
    // ===================
    // Finance Transaction
    // ===================
    Route::get("finance-transactions", "FinanceTransactionController@financeTransactions");
    Route::post("finance-transaction", "FinanceTransactionController@createFinanceTransaction");
    Route::put("finance-transaction/{id}", "FinanceTransactionController@createFinanceTransaction");
    Route::delete("finance-transaction/{id}", "FinanceTransactionController@deleteFinanceTransaction");
    Route::get("finance-transaction/{id}", "FinanceTransactionController@detailFinanceTransaction");
    Route::put("finance-transaction/{id}/restore", "FinanceTransactionController@restoreFinanceTransaction");
    Route::get("daily-reports", "FinanceTransactionController@dailyReports");
    Route::get("monthly-reports", "FinanceTransactionController@monthlyReports");
    // ===================
});
