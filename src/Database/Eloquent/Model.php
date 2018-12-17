<?php

namespace Nocarefree\Systematics\Eloquent;

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
	private function _targets($model)
	{
		return $this->belongsToMany($model, config('systematics.database.table'), 'source_id', 'target_id')
                	->wherePivot('type_id', $this->_getTypeId($this->getTabel().'/'.$model::getTable()) );
	}

	private function _sources($model)
	{
		return $this->belongsToMany($model, config('systematics.database.table'), 'target_id', 'source_id')
                	->wherePivot('type_id', $this->_getTypeId($model::getTable().'/'.$this->getTabel()) );
	}

	private function _getTypeId($code){
		$type = Systematics::getTypeByCode($code);
		return $type ? $type->id : 0;
	}

}