<?php

namespace Nocarefree\Systematics\Query;

use Illuminate\Support\Facades\DB;

/**
 * 一个 DB 类型 ，一个是Eloquent
 * User->sysmatics('sdfs')->getMany();
 * User->sysmatics('sdfs')->getOne();
 * User->sysmatics('sdfs')->getBelong();
 * User->sysmatics('sdfs')->getTree();
 * User->sysmatics('sdfs')->getBranch();
 */

class Item{

	private $attribtes;
	private $code;
	private $primaryKey;

	public function __construct($system, $relationCode, $attribtes, $primaryKey = 'id'){
		$this->system        = $system;
		$this->attribtes     = $attribtes;
		$this->relationCode  = $code;
		$this->primaryKey    = $primaryKey;
		$this->id = $attribtes[$this->primaryKey]?:0;
	}

	public function targets($targetKey = 'id'){
		if( $this->id ){
			return null;
		}
		return $this->system->getTargets($this->relationCode, $this->id, $targetKey);
	}

	public function value(){
		$many = $this->targets();
		return empty($many)?null:$many[0];
	}

	public function source($sourceKey){
		if( $this->id ){
			return null;
		}

		return $this->system->getSource($this->relationCode, $this->id, $sourceKey);
	}

/*	public function branch(){
		if( !isset($this->attribtes[$this->primaryKey]) ){
			return [];
		}

		return $this->system->getChildren($this->type, $this->source_id);
	}*/

}