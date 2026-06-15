<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ── Page principale (SPA) ─────────────────────────────────────────────────────
Route::get('/', function () {
    // Les items du parcours viennent de la DB
    $timelineItems = \DB::table('timeline_items')
        ->orderBy('date_start', 'desc')
        ->get()
        ->toArray() ?? [];

    $certifications = \App\Models\Certification::orderBy('issued_at', 'desc')->get();
    
    // Récupération des projets
    $projects = \App\Models\Project::published()->orderBy('sort_order', 'asc')->latest()->get();
    
    // Récupération des médias de la galerie
    $gallery = \App\Models\MediaGallery::visible()->orderBy('sort_order', 'asc')->latest()->get();
    
    // Récupération des partenaires (ceux ayant un logo)
    $partners = \App\Models\Partnership::whereNotNull('logo_path')
        ->orderBy('created_at', 'desc')
        ->get();

    $skillCategories = \App\Models\SkillCategory::with(['skills' => function($q) {
        $q->orderBy('sort_order', 'asc')->orderBy('proficiency', 'desc');
    }])->orderBy('sort_order', 'asc')->get();

    $diplomas = \App\Models\Diploma::orderBy('year', 'desc')->get();

    return Inertia::render('Home', [
        'timelineItems' => $timelineItems,
        'projects' => $projects,
        'certifications' => $certifications,
        'gallery' => $gallery,
        'partners' => $partners,
        'skillCategories' => $skillCategories,
        'diplomas' => $diplomas,
    ]);
});

// ── Admin : redirige vers le login ────────────────────────────────────────────
Route::get('/admin/login', function () {
    return Inertia::render('Admin/Login');
})->name('login');

Route::post('/admin/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// OTP API Routes
Route::post('/api/otp/send', [\App\Http\Controllers\OtpVerificationController::class, 'send']);
Route::post('/api/otp/verify', [\App\Http\Controllers\OtpVerificationController::class, 'verify']);

// Routes protégées par OTP Passwordless (CV)
Route::middleware('otp.validated')->group(function () {
    Route::post('/download-cv', [\App\Http\Controllers\CvController::class, 'processDownload'])->name('cv.process');
});

// Routes publiques
Route::post('/partnerships', [\App\Http\Controllers\PartnershipController::class, 'store'])->name('partnerships.store');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/admin/skills/categories', [\App\Http\Controllers\Admin\SkillController::class, 'storeCategory']);
    Route::put('/admin/skills/categories/{category}', [\App\Http\Controllers\Admin\SkillController::class, 'updateCategory']);
    Route::delete('/admin/skills/categories/{category}', [\App\Http\Controllers\Admin\SkillController::class, 'destroyCategory']);
    Route::resource('/admin/skills', \App\Http\Controllers\Admin\SkillController::class);
    Route::resource('/admin/timeline', \App\Http\Controllers\Admin\TimelineController::class);
    Route::resource('/admin/projects', \App\Http\Controllers\Admin\ProjectController::class);
    Route::resource('/admin/certifications', \App\Http\Controllers\Admin\CertificationController::class);
    Route::resource('/admin/gallery', \App\Http\Controllers\Admin\GalleryController::class);
    Route::resource('/admin/contracts', \App\Http\Controllers\Admin\ContractController::class);
    Route::resource('/admin/diplomas', \App\Http\Controllers\Admin\DiplomaController::class);
    Route::get('/admin/cv-downloads', [\App\Http\Controllers\Admin\CvDownloadController::class, 'index'])->name('admin.cv-downloads.index');
    Route::delete('/admin/cv-downloads/{cvDownload}', [\App\Http\Controllers\Admin\CvDownloadController::class, 'destroy'])->name('admin.cv-downloads.destroy');
    Route::get('/admin/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('admin.analytics.index');
    // Alias: /admin/partnerships -> ContractController (Partnerships = Contracts)
    Route::get('/admin/partnerships', [\App\Http\Controllers\Admin\ContractController::class, 'index'])->name('admin.partnerships.index');
    // Alias: /admin/media -> GalleryController
    Route::get('/admin/media', [\App\Http\Controllers\Admin\GalleryController::class, 'index'])->name('admin.media.index');
});

// ── Fallback : toutes les routes SPA renvoient vers Home ─────────────────────
Route::fallback(function () {
    return Inertia::render('Home');
});
