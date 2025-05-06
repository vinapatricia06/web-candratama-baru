<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OmsetController;
use App\Http\Controllers\ProgressProjectController;
use App\Http\Controllers\SuratMarketingController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\SuratFinanceController;
use App\Http\Controllers\SuratAdminController;
use App\Http\Controllers\SuratWarehouseController;
use App\Http\Controllers\SuratPurchasingController;
use App\Http\Controllers\SuratEkspedisiController;
use App\Http\Controllers\SuratCleaningController;
use App\Http\Controllers\SuratInteriorConsultanController;
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

Route::get('/', function () {
    return view('auth.login');
});

// Route::resource('users', UserController::class);

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');



Route::middleware(['auth', 'role:marketing,interior_consultan,finance,'])->group(function () {
    Route::get('/omsets', [OmsetController::class, 'index'])->name('omsets.index');
    Route::get('/omsets/create', [OmsetController::class, 'create'])->name('omsets.create');
    Route::post('/omsets', [OmsetController::class, 'store'])->name('omsets.store');
    Route::get('/omsets/{omset}/edit', [OmsetController::class, 'edit'])->name('omsets.edit');
    Route::put('/omsets/{omset}', [OmsetController::class, 'update'])->name('omsets.update');
    Route::delete('/omsets/{omset}', [OmsetController::class, 'destroy'])->name('omsets.destroy');

    Route::get('/omsets/rekap', [OmsetController::class, 'rekapBulanan'])->name('omsets.rekap');
    Route::get('omsets/export', [OmsetController::class, 'exportToExcel'])->name('omsets.export');
    Route::get('/omset/download-pdf', [OmsetController::class, 'downloadPDF'])->name('omset.download-pdf');
    Route::post('/omset/upload-chart', [OmsetController::class, 'uploadChart'])->name('omset.upload-chart');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/progress_projects/create', [ProgressProjectController::class, 'create'])->name('progress_projects.create');
    Route::post('/progress_projects', [ProgressProjectController::class, 'store'])->name('progress_projects.store');
    Route::get('/progress_projects/{progressProject}/edit', [ProgressProjectController::class, 'edit'])->name('progress_projects.edit');
    Route::put('/progress_projects/{progressProject}', [ProgressProjectController::class, 'update'])->name('progress_projects.update');
    Route::delete('/progress_projects/{progressProject}', [ProgressProjectController::class, 'destroy'])->name('progress_projects.destroy');
    Route::post('progress_projects/hapusBulan', [ProgressProjectController::class, 'hapusBulan'])->name('progress_projects.hapusBulan');
});
    Route::get('/progress_projects', [ProgressProjectController::class, 'index'])->name('progress_projects.index');
    Route::get('/progress-projects/download', [ProgressProjectController::class, 'downloadPdf'])->name('progress_projects.downloadPdf');
    
Route::middleware(['auth', 'role:admin,marketing'])->group(function () {
    Route::get('/maintenances/create', [MaintenanceController::class, 'create'])->name('maintenances.create');
    Route::post('/maintenances', [MaintenanceController::class, 'store'])->name('maintenances.store');
    Route::get('/maintenances/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('maintenances.edit');
    Route::put('/maintenances/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenances.update');
    Route::delete('/maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenances.destroy');
    Route::post('maintenances/hapusBulan', [MaintenanceController::class, 'hapusBulan'])->name('maintenances.hapusBulan');
});
Route::get('/maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
Route::get('/maintenances/download', [MaintenanceController::class, 'downloadPdf'])->name('maintenances.downloadPdf');
    
Route::middleware(['auth', 'role:marketing,admin,finance,warehouse,purchasing,interior_consultan'])->group(function () {
    Route::get('/digital-marketing/list', [SuratMarketingController::class, 'list'])->name('surat.digital_marketing.list');
    Route::put('/surat/marketing/updateStatusPengajuan/{id}', [SuratMarketingController::class, 'updateStatusPengajuan'])->name('surat.marketing.updateStatusPengajuan');
    Route::get('/surat-marketing/download/{id}', [SuratMarketingController::class, 'downloadfile'])->name('surat.marketing.downloadfile');
    Route::get('/surat-marketing/view/{id}', [SuratMarketingController::class, 'viewPDF'])->name('surat.marketing.viewPDF');
    
    Route::get('/surat/finance', [SuratFinanceController::class, 'index'])->name('surat.finance.index');
    Route::get('/surat/finance/downloadfile/{id}', [SuratFinanceController::class, 'downloadfile'])->name('surat.finance.downloadfile');
    Route::get('/surat/finance/viewpdf/{id}', [SuratFinanceController::class, 'viewPDF'])->name('surat.finance.viewPDF');
    Route::put('/surat/finance/{id}/update-status', [SuratFinanceController::class, 'updateStatusPengajuan'])->name('surat.finance.updateStatusPengajuan'); 
    
    Route::get('/surat/admin', [SuratAdminController::class, 'index'])->name('surat.admin.index');
    Route::get('/surat/admin/downloadfile/{id}', [SuratAdminController::class, 'downloadfile'])->name('surat.admin.downloadfile');
    Route::get('/surat/admin/viewpdf/{id}', [SuratAdminController::class, 'viewPDF'])->name('surat.admin.viewPDF');
    Route::put('/surat-admin/{id}/update-status', [SuratAdminController::class, 'updateStatusPengajuan'])->name('surat.admin.updateStatusPengajuan');

    Route::get('/surat-warehouse', [SuratWarehouseController::class, 'index'])->name('surat.warehouse.index');
    Route::get('/surat-warehouse/download/{id}', [SuratWarehouseController::class, 'downloadfile'])->name('surat.warehouse.download');
    Route::put('/surat-warehouse/status/{id}', [SuratWarehouseController::class, 'updateStatusPengajuan'])->name('surat.warehouse.updateStatus');
    Route::get('/surat-warehouse/view/{id}', [SuratWarehouseController::class, 'viewPDF'])->name('surat.warehouse.view');
    
    Route::get('/surat/purchasing', [SuratPurchasingController::class, 'index'])->name('surat.purchasing.index');
    Route::get('/surat/purchasing/download/{id}', [SuratPurchasingController::class, 'downloadfile'])->name('surat.purchasing.download');
    Route::put('/surat/purchasing/update-status/{id}', [SuratPurchasingController::class, 'updateStatusPengajuan'])->name('surat.purchasing.updateStatus');
    Route::get('/surat/purchasing/view/{id}', [SuratPurchasingController::class, 'viewPDF'])->name('surat.purchasing.view');
    
});

Route::middleware(['auth', 'role:marketing'])->group(function () {
    Route::get('/dashboard/marketing', [SuratMarketingController::class, 'dashboard'])->name('dashboard.marketing');
    Route::get('/surat-marketing/generate', [SuratMarketingController::class, 'index'])->name('surat.marketing.generate.form');
    Route::post('/surat-marketing/generate', [SuratMarketingController::class, 'generate'])->name('surat.marketing.generate');
    Route::get('/surat-marketing/{id}/edit', [SuratMarketingController::class, 'edit'])->name('surat.marketing.edit');
    Route::put('/surat-marketing/{id}', [SuratMarketingController::class, 'update'])->name('surat.marketing.update');
    Route::get('/surat/marketing/create', [SuratMarketingController::class, 'create'])->name('surat.marketing.create');
});

Route::middleware(['auth', 'role:finance'])->group(function () {
    Route::get('/surat/finance/create', [SuratFinanceController::class, 'create'])->name('surat.finance.create');
    Route::post('/surat/finance/generate', [SuratFinanceController::class, 'generate'])->name('surat.finance.generate');
    Route::get('/surat/finance/edit/{id}', [SuratFinanceController::class, 'edit'])->name('surat.finance.edit');
    Route::put('/surat/finance/update/{id}', [SuratFinanceController::class, 'update'])->name('surat.finance.update');
    Route::get('/surat/finance/dashboard', [SuratFinanceController::class, 'dashboard'])->name('surat.finance.dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/surat/admin/create', [SuratAdminController::class, 'create'])->name('surat.admin.create');
    Route::post('/surat/admin/generate', [SuratAdminController::class, 'generate'])->name('surat.admin.generate');
    Route::get('/surat/admin/edit/{id}', [SuratAdminController::class, 'edit'])->name('surat.admin.edit');
    Route::put('/surat/admin/update/{id}', [SuratAdminController::class, 'update'])->name('surat.admin.update');
    Route::get('/surat/admin/dashboard', [SuratAdminController::class, 'dashboard'])->name('surat.admin.dashboard');
});

Route::middleware(['auth', 'role:warehouse'])->group(function () {
    Route::post('/surat-warehouse/generate', [SuratWarehouseController::class, 'generate'])->name('surat.warehouse.generate');
    Route::get('/surat-warehouse/edit/{id}', [SuratWarehouseController::class, 'edit'])->name('surat.warehouse.edit');
    Route::put('/surat-warehouse/update/{id}', [SuratWarehouseController::class, 'update'])->name('surat.warehouse.update');
    Route::get('/surat-warehouse/create', [SuratWarehouseController::class, 'create'])->name('surat.warehouse.create');
    Route::get('/surat/warehouse/dashboard', [SuratWarehouseController::class, 'dashboard'])->name('surat.warehouse.dashboard');
});

Route::middleware(['auth', 'role:purchasing'])->group(function () {
    Route::post('/surat/purchasing/generate', [SuratPurchasingController::class, 'generate'])->name('surat.purchasing.generate');
    Route::get('/surat/purchasing/edit/{id}', [SuratPurchasingController::class, 'edit'])->name('surat.purchasing.edit');
    Route::put('/surat/purchasing/update/{id}', [SuratPurchasingController::class, 'update'])->name('surat.purchasing.update');
    Route::get('/surat/purchasing/create', [SuratPurchasingController::class, 'create'])->name('surat.purchasing.create');
    Route::get('/surat/purchasing/dashboard', [SuratPurchasingController::class, 'dashboard'])->name('surat.purchasing.dashboard');
});


use App\Http\Controllers\AuthController;
// Route Login & Logout
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard/CEO', [AuthController::class, 'dashboardCEO'])->name('dashboard.ceo')->middleware('auth', 'role:CEO');


Route::post('/notif-clear', function () {
    session()->forget('statusUpdated');
    return back();
})->name('notif.clear');


Route::middleware(['auth', 'role:ekspedisi'])->group(function () {
    Route::get('/surat-ekspedisi/create', [SuratEkspedisiController::class, 'create'])->name('surat.ekspedisi.create');
    Route::post('/surat-ekspedisi', [SuratEkspedisiController::class, 'store'])->name('surat.ekspedisi.store');
    Route::get('/surat-ekspedisi/{id}/edit', [SuratEkspedisiController::class, 'edit'])->name('surat.ekspedisi.edit');
    Route::put('/surat-ekspedisi/{id}', [SuratEkspedisiController::class, 'update'])->name('surat.ekspedisi.update');

});

Route::get('/surat-ekspedisi', [SuratEkspedisiController::class, 'index'])->name('surat.ekspedisi.index');
Route::delete('/surat-ekspedisi/{id}', [SuratEkspedisiController::class, 'destroy'])->name('surat.ekspedisi.destroy');
Route::put('/surat-ekspedisi/{id}/status', [SuratEkspedisiController::class, 'updateStatusPengajuan'])->name('surat.ekspedisi.updateStatus');
Route::get('/surat/ekspesidi/download/{id}', [SuratEkspedisiController::class, 'downloadfile'])->name('surat.ekspedisi.download');
Route::get('/surat/ekspedisi/view/{id}', [SuratEkspedisiController::class, 'viewPDF'])->name('surat.ekspedisi.view');
    

Route::middleware(['auth', 'role:cleaning_services'])->group(function () {
    Route::get('surat/cleaning/create', [SuratCleaningController::class, 'create'])->name('surat.cleaning.create'); // To show the create form
    Route::post('surat/cleaning', [SuratCleaningController::class, 'store'])->name('surat.cleaning.store'); // To handle the form submission (store)
    Route::get('surat/cleaning/{id}/edit', [SuratCleaningController::class, 'edit'])->name('surat.cleaning.edit'); // To show the edit form
    Route::put('surat/cleaning/{id}', [SuratCleaningController::class, 'update'])->name('surat.cleaning.update'); // To handle the form submission (update)
});

Route::get('surat/cleaning', [SuratCleaningController::class, 'index'])->name('surat.cleaning.index'); // To show all surat cleaning
Route::put('surat/cleaning/{id}/updateStatus', [SuratCleaningController::class, 'updateStatus'])->name('surat.cleaning.updateStatus'); // To update status
Route::get('surat/cleaning/{id}/download', [SuratCleaningController::class, 'download'])->name('surat.cleaning.download'); // To download the file
Route::get('surat/cleaning/{id}/view', [SuratCleaningController::class, 'viewPDF'])->name('surat.cleaning.view'); // To view the file

Route::middleware(['auth', 'role:interior_consultan'])->group(function () {
    Route::get('surat/interior_consultan/create', [SuratInteriorConsultanController::class, 'create'])->name('surat.interior_consultan.create');
    Route::post('surat/interior_consultan', [SuratInteriorConsultanController::class, 'store'])->name('surat.interior_consultan.store');
    Route::get('surat/interior_consultan/{id}/edit', [SuratInteriorConsultanController::class, 'edit'])->name('surat.interior_consultan.edit');
    Route::put('surat/interior_consultan/{id}', [SuratInteriorConsultanController::class, 'update'])->name('surat.interior_consultan.update');

});


Route::get('surat/interior_consultan', [SuratInteriorConsultanController::class, 'index'])->name('surat.interior_consultan.index');
Route::put('surat/interior_consultan/{id}/status', [SuratInteriorConsultanController::class, 'updateStatusPengajuan'])->name('surat.interior_consultan.updateStatusPengajuan');
Route::get('surat/interior_consultan/{id}/download', [SuratInteriorConsultanController::class, 'downloadfile'])->name('surat.interior_consultan.downloadfile');
Route::get('surat/interior_consultan/{id}/view', [SuratInteriorConsultanController::class, 'viewPDF'])->name('surat.interior_consultan.viewPDF');

// routes/web.php
Route::post('/notif/cleardm', function () {
    session()->forget('statusUpdated');
    session()->forget('suratKeDM');
    return redirect()->route('dashboard.marketing');
})->name('notif.cleardm');

Route::post('/notif/clearwrh', function () {
    session()->forget('statusUpdatedwrh');
    session()->forget('suratKeWarehouse');
    return redirect()->route('dashboard.warehouse');
})->name('notif.clearwrh');

Route::post('/notif/clearpch', function () {
    session()->forget('statusUpdatedpch');
    session()->forget('suratDM');
    session()->forget('suratWRH');
    session()->forget('suratADM');
    return redirect()->route('surat.purchasing.dashboard');
})->name('notif.clearpch');

Route::post('/notif/clearpch', function () {
    session()->forget('statusUpdatedAdmin');
    return redirect()->route('surat.admin.dashboard');
})->name('notif.clearadm');


Route::post('/clear-notification', function () {
    session()->forget('status_messageCS');
    return response()->json(['status' => 'success']);
});


Route::post('/clear-notification', function () {
    session()->forget('status_message');
    return response()->json(['status' => 'success']);
});

Route::post('/clear-notification', function () {
    session()->forget('status_messageEKP');
    return response()->json(['status' => 'success']);
});


Route::post('/notif-clear', function () {
    session()->forget('statusUpdatedAdmin'); // Hapus hanya session untuk Admin
    return redirect()->back();
})->name('notif.clearadm');

Route::post('/notif-clear', function () {
    session()->forget('statusUpdatedpch'); // Hapus hanya session untuk Admin
    return redirect()->back();
})->name('notif.clearpch');




Route::delete('/surat-admin/{id}', [SuratAdminController::class, 'destroy'])->name('surat.admin.destroy')->middleware('auth'); // Pastikan hanya user yang sudah login bisa mengakses
Route::delete('/surat-cleaning/{id}', [SuratCleaningController::class, 'destroy'])->name('surat.cleaning.destroy')->middleware('auth'); // Pastikan hanya user yang sudah login bisa mengakses
Route::delete('/surat-marketing/{id}', [SuratMarketingController::class, 'destroy'])->name('surat.marketing.destroy')->middleware('auth'); // Pastikan hanya user yang sudah login bisa mengakses
Route::delete('/surat-ekspedisi/{id}', [SuratEkspedisiController::class, 'destroy'])->name('surat.ekspedisi.destroy')->middleware('auth'); // Pastikan hanya user yang sudah login bisa mengakses
Route::delete('/surat-finance/{id}', [SuratFinanceController::class, 'destroy'])->name('surat.finance.destroy')->middleware('auth'); // Pastikan hanya user yang sudah login bisa mengakses
Route::delete('/surat-interior_consultan/{id}', [SuratInteriorConsultanController::class, 'destroy'])->name('surat.interior_consultan.destroy')->middleware('auth'); // Pastikan hanya user yang sudah login bisa mengakses
Route::delete('/surat-purchasing/{id}', [SuratPurchasingController::class, 'destroy'])->name('surat.purchasing.destroy')->middleware('auth'); // Pastikan hanya user yang sudah login bisa mengakses
Route::delete('/surat-warehouse/{id}', [SuratWarehouseController::class, 'destroy'])->name('surat.warehouse.destroy')->middleware('auth'); // Pastikan hanya user yang sudah login bisa mengakses


Route::get('/surat/finance/pending', [SuratFinanceController::class, 'pending'])->name('surat.finance.pending');



Route::get('surat-marketing/filter-by-year', [SuratMarketingController::class, 'filterByYear'])->name('surat.marketing.filterByYear');
Route::post('surat-marketing/delete-by-year', [SuratMarketingController::class, 'deleteByYear'])->name('surat.marketing.deleteByYear');

Route::get('surat-admin/filter-by-year', [SuratAdminController::class, 'filterByYear'])->name('surat.admin.filterByYear');
Route::post('surat-admin/delete-by-year', [SuratAdminController::class, 'deleteByYear'])->name('surat.admin.deleteByYear');

Route::get('surat-finance/filter-by-year', [SuratFinanceController::class, 'filterByYear'])->name('surat.finance.filterByYear');
Route::post('surat-finance/delete-by-year', [SuratFinanceController::class, 'deleteByYear'])->name('surat.finance.deleteByYear');

Route::get('surat-warehouse/filter-by-year', [SuratWarehouseController::class, 'filterByYear'])->name('surat.warehouse.filterByYear');
Route::post('surat-warehouse/delete-by-year', [SuratWarehouseController::class, 'deleteByYear'])->name('surat.warehouse.deleteByYear');

Route::get('surat-purchasing/filter-by-year', [SuratPurchasingController::class, 'filterByYear'])->name('surat.purchasing.filterByYear');
Route::post('surat-purchasing/delete-by-year', [SuratPurchasingController::class, 'deleteByYear'])->name('surat.purchasing.deleteByYear');

Route::delete('/surat/interior_consultan/destroy-all', [SuratInteriorConsultanController::class, 'destroyAll'])->name('surat.interior_consultan.destroyAll');

Route::delete('/surat/ekspedisi/destroy-all', [SuratEkspedisiController::class, 'destroyAll'])->name('surat.ekspedisi.destroyAll');

Route::delete('/surat/cleaning/destroy-all', [SuratCleaningController::class, 'destroyAll'])->name('surat.cleaning.destroyAll');

