<?php 

namespace Nocarefree\Systematics\Facades;

use Illuminate\Support\Facades\Facade;


class Sysmatics Extends Facade{

	protected static function getFacadeAccessor()
    {
        return $this->app->make('Sysmatics\Sysmatics');;
    }
}