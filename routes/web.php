<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;

Route::get('/',          [SiteController::class, 'home'])->name('home');
Route::get('/empresa',   [SiteController::class, 'sobre'])->name('sobre');
Route::get('/catalogo',  [SiteController::class, 'catalogo'])->name('catalogo');
Route::get('/contato',   [SiteController::class, 'contato'])->name('contato');
Route::post('/contato',  [SiteController::class, 'enviarContato'])->name('contato.enviar');
Route::get('/servicos/{slug}', [SiteController::class, 'servico'])->name('servico');
Route::get('/sitemap.xml',    [SiteController::class, 'sitemap'])->name('sitemap');
