<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DasboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// log in
Route::middleware(['guest:karyawan'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
});

// log in user/admin
Route::middleware(['guest:user'])->group(function () {
    Route::get('/admin', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');


    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
});

Route::middleware(['auth:karyawan'])->group(function () {
    Route::get('/dasboard', [DasboardController::class, 'index']);
    Route::get('/proseslogout', [AuthController::class, 'proseslogout']);

    //presensi
    Route::get('/presensi/create', [PresensiController::class, 'create']);
    Route::post('/presensi/store', [PresensiController::class, 'store']);
    // edit profil
    Route::get('/editprofile', [PresensiController::class, 'editprofile']);
    Route::post('/presensi/{nik}/updateprofile', [PresensiController::class, 'updateprofile']);

    // Izin
    Route::get('/presensi/izin', [PresensiController::class, 'izin']);
    Route::get('/presensi/buatizin', [PresensiController::class, 'buatizin']);
    Route::post('/presensi/storeizin', [PresensiController::class, 'storeizin']);
    Route::post('/presensi/cekpengajuanizin', [PresensiController::class, 'cekpengajuanizin']);
    
    // Histori
    Route::get('/presensi/histori', [PresensiController::class, 'histori']);
    Route::post('/gethistori', [PresensiController::class, 'gethistori']);

});

Route::middleware(['auth:user'])->group(function () {
    Route::get('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin']);
    // mencegah masuk lewat url dasboard
    Route::get('/admin/dasboardadmin', [DasboardController::class, 'dasboardadmin'])->middleware(['roleahs']);
    
    // karyawan
    Route::get('/karyawan', [KaryawanController::class, 'index'])->middleware(['roleah']);
    Route::post('/karyawan/store', [KaryawanController::class, 'store'])->middleware(['roleah']);
    Route::post('/karyawan/edit', [KaryawanController::class, 'edit'])->middleware(['roleah']);
    Route::post('/karyawan/{nik}/update', [KaryawanController::class, 'update'])->middleware(['roleah']);
    Route::post('/karyawan/{nik}/delete', [KaryawanController::class, 'delete'])->middleware(['roleah']);

    // Departemen
    Route::get('/departemen', [DepartemenController::class, 'index'])->middleware(['roleah']);
    Route::post('/departemen/store', [DepartemenController::class, 'store'])->middleware(['roleah']);
    Route::post('/departemen/edit', [DepartemenController::class, 'edit'])->middleware(['roleah']);
    Route::post('/departemen/{kode_dept}/update', [DepartemenController::class, 'update'])->middleware(['roleah']);
    Route::post('/departemen/{kode_dept}/delete', [DepartemenController::class, 'delete'])->middleware(['roleah']);

    // User
    Route::get('/user', [UserController::class, 'index'])->middleware(['roleadmin']);
    Route::post('/user/store', [UserController::class, 'store'])->middleware(['roleadmin']);
    Route::post('/user/edit', [UserController::class, 'edit'])->middleware(['roleadmin']);
    Route::post('/user/{nik}/update', [UserController::class, 'update'])->middleware(['roleadmin']);
    Route::post('/user/{nik}/delete', [UserController::class, 'delete'])->middleware(['roleadmin']);


    // Monitoring
    Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring'])->middleware(['roleahs']);
    Route::post('/getpresensi', [PresensiController::class, 'getpresensi'])->middleware(['roleahs']);

    // Laporan
    Route::get('/presensi/laporan', [PresensiController::class, 'laporan'])->middleware(['roleahs']);
    Route::post('/presensi/cetaklaporan', [PresensiController::class, 'cetaklaporan'])->middleware(['roleahs']);

    // Cetak Rekap
    Route::post('/presensi/cetakrekap', [PresensiController::class, 'cetakrekap'])->middleware(['roleahs']);

    // Izin Sakit
    Route::get('/presensi/izinsakit', [PresensiController::class, 'izinsakit'])->middleware(['roleahs']);
    Route::post('/presensi/terimaizinsakit', [PresensiController::class, 'terimaizinsakit'])->middleware(['roleahs']);
    Route::get('/presensi/{id}/batalkanizinsakit', [PresensiController::class, 'batalkanizinsakit'])->middleware(['roleahs']);
});
