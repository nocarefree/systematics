<?php 


Route::get('login','Auth\Login@login')->name('admin/login');


Route::middleware('admin')->group(function(){
	Route::get('logout','Auth\Login@logout');


});
             