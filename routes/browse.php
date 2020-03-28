<?php

Route::group(['prefix' => 'smartplus', 'middleware' => ['auth', 'sametime_login']], function (): void {
    Route::get('download/{encodedFilename}', 'SmartPlusController@download')->where('encodedFilename', '.+');
});
