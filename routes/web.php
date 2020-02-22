<?php
Route::macro('optionRoutes',function(){
    Route::group([
        'namespace' => '\Haruncpi\LaravelOptionFramework\Controllers',
        'middleware' => config('option-framework.middleware')
    ], function () {
        Route::get(config('option-framework.view_route_path'), 'OptionController@getIndex');
        Route::post(config('option-framework.view_route_path'), 'OptionController@postIndex');
    });
});
