<?php 

namespace Nocarefree\Systematics\Facades;

use Illuminate\Support\Facades\Facade;


class Systematics Extends Facade{

	protected static function getFacadeAccessor()
    {
        return $this->app->make('Systematics\Systematics');;
    }
}