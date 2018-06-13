<?php
/**
 * Created by PhpStorm.
 * User: sturt
 * Date: 04/06/18
 * Time: 3:52 PM
 */

Route::any('/api/v1/providers','Sturt\Citationscraper\Http\Controllers\Citation@listProviders');
Route::any('/api/v1/research_data','Sturt\Citationscraper\Http\Controllers\Citation@index');