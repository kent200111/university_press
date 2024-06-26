<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\http\Controllers\DashboardController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\ImController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AdjustmentLogController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\UserController;

Auth::routes([
    'verify' => true
]);
Route::get('/', function () {
    return view('landing_page');
})->name('landing-page');
Route::middleware('auth')->group(function () {
    Route::view('about', 'about')->name('about');
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::resource('batches', BatchController::class);
    Route::resource('masterlist', ImController::class);
    Route::resource('authors', AuthorController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('colleges', CollegeController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('adjustment_logs', AdjustmentLogController::class);
    Route::get('/items', function () {
        return view('sales_management.point_of_sale'); 
    });
    Route::resource('purchases', PurchaseController::class);
    Route::resource('monitoring', MonitoringController::class);
    Route::resource('reports', ReportController::class);
    Route::resource('filters', FilterController::class);
});
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::resource('users', UserController::class);
});