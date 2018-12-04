<?php

namespace Nocarefree\Systematics\Eloquent;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * @mixin \Illuminate\Database\Query\Builder
 */
class Builder extends EloquentBuilder
{
	public function systematics()
	{
		
	}

	
}