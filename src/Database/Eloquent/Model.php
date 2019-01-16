<?php

namespace Nocarefree\Systematics\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Nocarefree\Systematics\Facades\Systematics;

/**
 * Parents _targets() 
 * 
 */
class Model extends EloquentModel
{
	/**
	 * [systematicsSource description]
	 * @param  [type] $model [description]
	 * @return [type]        [description]
	 */
	public function _targets($model)
	{
		return $this->belongsToMany($model, Systematics::getRealtionsTableName(), 'source_id', 'target_id')
                	->wherePivot('type_id', Systematics::relation($this->getTabel(), $model::getTable())->getId());
	}

	public function _sources($model)
	{
		return $this->belongsToMany($model, Systematics::getRealtionsTableName(), 'target_id', 'source_id')
                	->wherePivot('type_id', Systematics::relation($model::getTable(), $this->getTabel())->getId());
	}

}