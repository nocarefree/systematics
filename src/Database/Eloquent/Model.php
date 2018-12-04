<?php

namespace Nocarefree\Systematics\Eloquent;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Systematics;

class Model extends EloquentModel
{
	public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

	public function systematics($type)
	{
		return (new static)->newQuery()->with(function($query){
			$query->where('code', $type);
		});
	}

}