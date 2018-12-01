<?php 

namespace Nocarefree\Systematics\Facades;

use Illuminate\Support\Facades\Facade;


class Sysmatics Extends Facade{

	protected static function getFacadeAccessor()
    {
        return Nocarefree\Systematics\Sysmatics::class;
    }
}