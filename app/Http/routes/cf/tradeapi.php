<?php
Route::group(['prefix' => 'tradeapi/v1', 'middleware' => 'api.auth.basic'], function() {
    Route::resource(
        'transaction',
        'Cf\TradeApi\TransactionController',
        ['only' => ['store']]
    );
});

Route::get('/', ['as' => 'home', 'uses' => 'Cf\HomeController@index']);
