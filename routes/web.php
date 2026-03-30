<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\CaseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GuidanceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\StudentAssessmentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\GuidanceBookingController;
use App\Http\Controllers\LatenessController;
use App\Http\Controllers\AssessmentLinkController;
use App\Models\Student;
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

Route::get('/', [JobVacancyController::class, 'landing'])->name('landing');

Route::post('/submit', [GuidanceBookingController::class, 'handlePost'])->name('submit.form');

Route::get('/coba', function () {
    return view('coba', ['siswa' => Student::with(['class', 'status'])->get(), 'active' => 'coba']);
});

Auth::routes(['verify' => true]);

Route::get('/pending', function() {
    return view('pending');
})->name('pending')->middleware('auth');

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/settings', [UsersController::class, 'settings'])->name('user.settings');
    Route::get('/settings/{id}/image', [UsersController::class, 'showImage'])->name('user.showImage');
    Route::put('/settings/{id}', [UsersController::class, 'setting_account'])->name('user.setting_account');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/siswa', [StudentController::class, 'index'])->name('student.index')->middleware('permission:Lihat Siswa');
    Route::get('/siswa/{id}/image', [StudentController::class, 'showImage'])->name('student.showImage');
    Route::get('/siswa/{id}/download', [StudentController::class, 'download'])->name('student.download');
    Route::post('/siswa', [StudentController::class, 'store'])->name('student.store')->middleware('permission:Tambah Siswa');
    Route::post('/siswa/import', [StudentController::class, 'import'])->name('student.import')->middleware('permission:Tambah Siswa');
    Route::get('/siswa/export', [StudentController::class, 'export'])->name('student.export')->middleware('permission:Tambah Siswa');
    Route::get('/student/download-format', [StudentController::class, 'downloadFormat'])->name('student.download_format')->middleware('permission:Tambah Siswa');
    Route::put('/siswa/{id}', [StudentController::class, 'update'])->name('student.update')->middleware('permission:Ubah Siswa');
    Route::delete('/siswa/{id}', [StudentController::class, 'destroy'])->name('student.destroy')->middleware('permission:Hapus Siswa');

    Route::get('/api/student-by-name', [App\Http\Controllers\Api\StudentApiController::class, 'findByName']);
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/keterlambatan', [LatenessController::class, 'index'])->name('lateness.index')->middleware('permission:Lihat Keterlambatan');
    Route::post('/keterlambatan', [LatenessController::class, 'store'])->name('lateness.store')->middleware('permission:Tambah Keterlambatan');
    Route::put('/keterlambatan/{id}', [LatenessController::class, 'update'])->name('lateness.update')->middleware('permission:Ubah Keterlambatan');
    Route::delete('/keterlambatan/{id}', [LatenessController::class, 'destroy'])->name('lateness.destroy')->middleware('permission:Hapus Keterlambatan');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/users', [UsersController::class, 'index'])->name('user.index')->middleware('permission:Lihat User');
    Route::get('/users/{id}/image', [UsersController::class, 'showImage'])->name('user.showImage');
    Route::get('/users/{id}/download', [UsersController::class, 'download'])->name('user.download');
    Route::post('/users', [UsersController::class, 'store'])->name('user.store')->middleware('permission:Tambah User');
    Route::put('/users/{id}', [UsersController::class, 'update'])->name('user.update')->middleware('permission:Ubah User');
    Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('user.destroy')->middleware('permission:Hapus User');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/jurusan', [MajorController::class, 'index'])->name('major.index')->middleware('permission:Lihat Jurusan');
    Route::post('/jurusan', [MajorController::class, 'store'])->name('major.store')->middleware('permission:Tambah Jurusan');
    Route::put('/jurusan/{id}', [MajorController::class, 'update'])->name('major.update')->middleware('permission:Ubah Jurusan');
    Route::delete('/jurusan/{id}', [MajorController::class, 'destroy'])->name('major.destroy')->middleware('permission:Hapus Jurusan');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/kelas', [ClassController::class, 'index'])->name('class.index')->middleware('permission:Lihat Kelas');
    Route::post('/kelas', [ClassController::class, 'store'])->name('class.store')->middleware('permission:Tambah Kelas');
    Route::put('/kelas/{id}', [ClassController::class, 'update'])->name('class.update')->middleware('permission:Ubah Kelas');
    Route::delete('/kelas/{id}', [ClassController::class, 'destroy'])->name('class.destroy')->middleware('permission:Hapus Kelas');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/akses', [RoleController::class, 'index'])->name('role.index')->middleware('permission:Lihat Role');
    Route::post('/akses', [RoleController::class, 'store'])->name('role.store')->middleware('permission:Tambah Role');
    Route::put('/akses/{id}', [RoleController::class, 'update'])->name('role.update')->middleware('permission:Ubah Role');
    Route::delete('/akses/{id}', [RoleController::class, 'destroy'])->name('role.destroy')->middleware('permission:Hapus Role');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/status', [StatusController::class, 'index'])->name('status.index')->middleware('permission:Lihat Status');
    Route::post('/status/import', [StatusController::class, 'import'])->name('status.import')->middleware('permission:Tambah Status');
    Route::post('/status', [StatusController::class, 'store'])->name('status.store')->middleware('permission:Tambah Status');
    Route::put('/status/{id}', [StatusController::class, 'update'])->name('status.update')->middleware('permission:Ubah Status');
    Route::delete('/status/{id}', [StatusController::class, 'destroy'])->name('status.destroy')->middleware('permission:Hapus Status');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/asesmen-link', [AssessmentLinkController::class, 'index'])->name('assessment_link.index');
    Route::post('/asesmen-link', [AssessmentLinkController::class, 'store'])->name('assessment_link.store');
    Route::put('/asesmen-link/{id}', [AssessmentLinkController::class, 'update'])->name('assessment_link.update');
    Route::delete('/asesmen-link/{id}', [AssessmentLinkController::class, 'destroy'])->name('assessment_link.destroy');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/bookingBimbingan', [GuidanceBookingController::class, 'index'])->name('guidanceBooking.index')->middleware('permission:Lihat Booking Bimbingan');
    Route::get('/booking-bimbingan/form', [GuidanceBookingController::class, 'bookingForm'])->name('guidanceBooking.form');
    Route::post('/booking-bimbingan/store', [GuidanceBookingController::class, 'store'])->name('guidanceBooking.store');
    Route::get('/guidanceBooking/{id}/image', [GuidanceBookingController::class, 'showImage'])->name('guidanceBooking.showImage');
    Route::get('/guidanceBooking/{id}/download', [GuidanceBookingController::class, 'download'])->name('guidanceBooking.download');
    Route::post('/bookingBimbingan', [GuidanceBookingController::class, 'handlePost'])->name('guidanceBooking.store')->middleware('permission:Tambah Booking Bimbingan');
    Route::put('/bookingBimbingan/{id}', [GuidanceBookingController::class, 'update'])->name('guidanceBooking.update')->middleware('permission:Ubah Booking Bimbingan');
    Route::delete('/bookingBimbingan/{id}', [GuidanceBookingController::class, 'destroy'])->name('guidanceBooking.destroy')->middleware('permission:Hapus Booking Bimbingan');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/bimbingan', [GuidanceController::class, 'index'])->name('guidance.index')->middleware('permission:Lihat Bimbingan');
    Route::get('/guidance/{id}/image', [GuidanceController::class, 'showImage'])->name('guidance.showImage');
    Route::get('/guidance/{id}/download', [GuidanceController::class, 'download'])->name('guidance.download');
    Route::post('/bimbingan', [GuidanceController::class, 'store'])->name('guidance.store')->middleware('permission:Tambah Bimbingan');
    Route::post('/bimbingan/import', [GuidanceController::class, 'import'])->name('guidance.import')->middleware('permission:Tambah Bimbingan');
    Route::get('/bimbingan/export', [GuidanceController::class, 'export'])->name('guidance.export')->middleware('permission:Tambah Bimbingan');
    Route::get('/bimbingan/download-format', [GuidanceController::class, 'downloadFormat'])->name('guidance.download_format')->middleware('permission:Tambah Bimbingan');
    Route::put('/bimbingan/{id}', [GuidanceController::class, 'update'])->name('guidance.update')->middleware('permission:Ubah Bimbingan');
    Route::delete('/bimbingan/{id}', [GuidanceController::class, 'destroy'])->name('guidance.destroy')->middleware('permission:Hapus Bimbingan');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/kasus', [CaseController::class, 'index'])->name('case.index')->middleware('permission:Lihat Kasus');
    Route::get('/kasus/{id}/image', [CaseController::class, 'showImage'])->name('case.showImage');
    Route::get('/kasus/{id}/download', [CaseController::class, 'download'])->name('case.download');
    Route::post('/kasus', [CaseController::class, 'store'])->name('case.store')->middleware('permission:Tambah Kasus');
    Route::post('/kasus/import', [CaseController::class, 'import'])->name('case.import')->middleware('permission:Tambah Kasus');
    Route::get('/kasus/export', [CaseController::class, 'export'])->name('case.export')->middleware('permission:Tambah Kasus');
    Route::get('/kasus/download-format', [CaseController::class, 'downloadFormat'])->name('case.download_format')->middleware('permission:Tambah Kasus');
    Route::put('/kasus/{id}', [CaseController::class, 'update'])->name('case.update')->middleware('permission:Ubah Kasus');
    Route::delete('/kasus/{id}', [CaseController::class, 'destroy'])->name('case.destroy')->middleware('permission:Hapus Kasus');
});

Route::group(['middleware' => 'auth'], function () {
    // Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendance.index')->middleware('permission:Lihat Absensi');
    Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/absensi/add-student', [AttendanceController::class, 'addStudent'])->name('attendance.addStudent')->middleware('permission:Tambah Absensi');
    Route::post('/absensi', [AttendanceController::class, 'store'])->name('attendance.store')->middleware('permission:Tambah Absensi');
    Route::post('/absensi/import', [AttendanceController::class, 'import'])->name('attendance.import')->middleware('permission:Tambah Absensi');
    Route::put('/absensi/{id}', [AttendanceController::class, 'update'])->name('attendance.update')->middleware('permission:Ubah Absensi');
    Route::delete('/absensi/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy')->middleware('permission:Hapus Absensi');
});

// Loker - akses publik tanpa login
Route::get('/loker/{id}/image', [JobVacancyController::class, 'showImage'])->name('jobVacancy.showImage');
Route::get('/loker/{id}/download', [JobVacancyController::class, 'download'])->name('jobVacancy.download');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/loker', [JobVacancyController::class, 'index'])->name('jobVacancy.index')->middleware('permission:Lihat Loker');
    Route::post('/loker', [JobVacancyController::class, 'store'])->name('jobVacancy.store')->middleware('permission:Tambah Loker');
    Route::post('/loker/import', [JobVacancyController::class, 'import'])->name('jobVacancy.import')->middleware('permission:Tambah Loker');
    Route::get('/loker/export', [JobVacancyController::class, 'export'])->name('jobVacancy.export')->middleware('permission:Tambah Loker');
    Route::get('/loker/download-format', [JobVacancyController::class, 'downloadFormat'])->name('jobVacancy.download_format')->middleware('permission:Tambah Loker');
    Route::put('/loker/{id}', [JobVacancyController::class, 'update'])->name('jobVacancy.update')->middleware('permission:Ubah Loker');
    Route::delete('/loker/{id}', [JobVacancyController::class, 'destroy'])->name('jobVacancy.destroy')->middleware('permission:Hapus Loker');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/prestasi', [AchievementController::class, 'index'])->name('achievement.index')->middleware('permission:Lihat Prestasi');
    Route::get('/prestasi/{id}/image', [AchievementController::class, 'showImage'])->name('achievement.showImage');
    Route::get('/prestasi/{id}/download', [AchievementController::class, 'download'])->name('achievement.download');
    Route::post('/prestasi', [AchievementController::class, 'store'])->name('achievement.store')->middleware('permission:Tambah Prestasi');
    Route::post('/prestasi/import', [AchievementController::class, 'import'])->name('achievement.import')->middleware('permission:Tambah Prestasi');
    Route::get('/prestasi/export', [AchievementController::class, 'export'])->name('achievement.export')->middleware('permission:Tambah Prestasi');
    Route::put('/prestasi/{id}', [AchievementController::class, 'update'])->name('achievement.update')->middleware('permission:Ubah Prestasi');
    Route::delete('/prestasi/{id}', [AchievementController::class, 'destroy'])->name('achievement.destroy')->middleware('permission:Hapus Prestasi');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/asesmen_siswa', [StudentAssessmentController::class, 'index'])->name('student_assessment.index')->middleware('permission:Lihat Asesmen Siswa');
    Route::post('/asesmen_siswa', [StudentAssessmentController::class, 'store'])->name('student_assessment.store')->middleware('permission:Tambah Asesmen Siswa');
    Route::post('/asesmen_siswa/import', [StudentAssessmentController::class, 'import'])->name('student_assessment.import')->middleware('permission:Tambah Asesmen Siswa');
    Route::get('/asesmen_siswa/export', [StudentAssessmentController::class, 'export'])->name('student_assessment.export')->middleware('permission:Tambah Asesmen Siswa');
    Route::get('/asesmen_siswa/download-format', [StudentAssessmentController::class, 'downloadFormat'])->name('student-assessment.download_format')->middleware('permission:Tambah Asesmen Siswa');
    Route::put('/asesmen_siswa/{id}', [StudentAssessmentController::class, 'update'])->name('student_assessment.update')->middleware('permission:Ubah Asesmen Siswa');
    Route::delete('/asesmen_siswa/{id}', [StudentAssessmentController::class, 'destroy'])->name('student_assessment.destroy')->middleware('permission:Hapus Asesmen Siswa');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/autentifikasi', [AuthenticationController::class, 'index'])->name('autentifikasi.index')->middleware('permission:Lihat Autentifikasi');
    Route::post('/autentifikasi', [AuthenticationController::class, 'store'])->name('autentifikasi.store')->middleware('permission:Tambah Autentifikasi');
    Route::put('/autentifikasi/{id}', [AuthenticationController::class, 'update'])->name('autentifikasi.update')->middleware('permission:Ubah Autentifikasi');
    Route::delete('/autentifikasi/{id}', [AuthenticationController::class, 'destroy'])->name('autentifikasi.destroy')->middleware('permission:Hapus Autentifikasi');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index')->middleware('permission:Lihat Perizinan');
    Route::post('/permission', [PermissionController::class, 'store'])->name('permission.store')->middleware('permission:Tambah Perizinan');
    Route::put('/permission/{id}', [PermissionController::class, 'update'])->name('permission.update')->middleware('permission:Ubah Perizinan');
    Route::delete('/permission/{id}', [PermissionController::class, 'destroy'])->name('permission.destroy')->middleware('permission:Hapus Perizinan');
});

require __DIR__.'/auth.php';
