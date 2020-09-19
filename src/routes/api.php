<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'admin'], function ($api) {

        $api->get('web/files', 'VCComponent\Laravel\File\Http\Controllers\Api\Admin\StaticFileController@index');
        $api->get('web/files/content', 'VCComponent\Laravel\File\Http\Controllers\Api\Admin\StaticFileController@getFileContent');
        $api->put('web/files', 'VCComponent\Laravel\File\Http\Controllers\Api\Admin\StaticFileController@update');
    });
});
