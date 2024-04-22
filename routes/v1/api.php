<?php

use App\Http\Controllers\V1\Auth\Company\LoginCompanyController;
use App\Http\Controllers\V1\Auth\Company\LogoutCompanyController;
use App\Http\Controllers\V1\Auth\Company\RegisterCompanyController;
use App\Http\Controllers\V1\Auth\Contact\LoginContactController;
use App\Http\Controllers\V1\Auth\Contact\LogoutContactController;
use App\Http\Controllers\V1\Auth\Contact\RegisterContactController;
use App\Http\Controllers\V1\CompanyController;
use App\Http\Controllers\V1\ContactController;
use App\Http\Controllers\V1\NoteController;
use Illuminate\Support\Facades\Route;

Route::post('company/register', RegisterCompanyController::class);
Route::post('company/login', LoginCompanyController::class);
Route::post('contact/register', RegisterContactController::class);
Route::post('contact/login', LoginContactController::class);

Route::apiResource('company', CompanyController::class);
Route::apiResource('contact', ContactController::class);
Route::post('contact/change-status', [ContactController::class, 'changeStatus']);
Route::post('contact/{contact}/attach-company', [ContactController::class, 'AttachCompany']);
Route::get('contact/{contact}/notes', [ContactController::class, 'getNotes']);
Route::apiResource('note', NoteController::class)->middleware('auth:sanctum');

Route::get('logout/company', LogoutCompanyController::class)->middleware('auth:sanctum');
Route::get('logout/contact', LogoutContactController::class)->middleware('auth:sanctum');
