<?php

use Illuminate\Support\Facades\Route;
use \App\Livewire\Pages\Home\HomeIntranet;
use \App\Livewire\Pages\Tools\ToolsIntranet;
use \App\Livewire\Pages\Externals\ExternalsIntranet;
use \App\Livewire\Pages\Phones\PhonesIntranet;
use \App\Livewire\Pages\News\NewsIntranet;
use \App\Livewire\Pages\News\NewsDetail;
use \App\Livewire\Pages\Nacional\NacionalIntranet;
use \App\Livewire\Pages\Academy\AcademyIntranet;

Route::get('/', HomeIntranet::class);

Route::get("/home", HomeIntranet::class);
Route::get("/tools", ToolsIntranet::class);
Route::get("/externals", ExternalsIntranet::class);
//ToolsSection
Route::get("/academy", AcademyIntranet::class);
Route::get("/phones", PhonesIntranet::class);
Route::get("/news", NewsIntranet::class);
Route::get("/news/{id}", NewsDetail::class)->name('news.detail');
Route::get("/nacional", NacionalIntranet::class);
Route::get("/nacional/{codigo}", NacionalIntranet::class);
Route::get('/proxy-pdf', function (\Illuminate\Http\Request $request) {
    $url = $request->query('url');
    abort_if(!$url, 404);

    return response()->stream(function () use ($url) {
        echo file_get_contents($url);
    }, 200, [
        'Content-Type' => 'application/pdf',
        'Access-Control-Allow-Origin' => '*',
    ]);
});
