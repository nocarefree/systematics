<?php

namespace Nocarefree\Systematics\Eloquent;

use Illuminate\Database\Eloquent\Model as EloquentModel;

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
	private function _targets($model)
	{
		$type = $this->getTabel() . '/' . $model::getTable();

		return $this->belongsToMany($model, config('systematics.database.table'), 'source_id', 'target_id')
                	->wherePivot('type', $type);
	}

	private function _sources($model)
	{
		$type = $model::getTable() . '/'. $this->getTabel();

		return $this->belongsToMany($model, config('systematics.database.table'), 'target_id', 'source_id')
                	->wherePivot('type', $type);
	}

}