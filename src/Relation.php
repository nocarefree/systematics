<?php
namespace Nocarefree\Systematics;

use Illuminate\Database\Connection;

class Relation{
	public function __construct(Systematics $sys, string $source, string $target){
		$this->sys = $sys;

		$this->tableSource = $source;
		$this->tableTarget = $target;

		$this->type = $this->getTableTypes()->where(
			'code', '=',
			$source . $this->sys->getSplitChar() . $target
		)->first();
	}

	public function getId(){
		return $this->type ? $this->type->id : 0;
	}

	public function targets($sourceId, $targetKey = 'id'){
		return $this->sys->getTableRelations()
		  ->leftJoin(
		  		$this->tableTarget, 
		  		$this->tableTarget.'.'.$targetKey, 
		  		'=', 
		  		$this->sys->getRealtionsTableName().'.target_id'
		  	)
		  ->select($this->tableTarget.'.*')
		  ->where([
		  		$this->sys->getRealtionsTableName().'.source_id' => $sourceId, 
		  		$this->sys->getRealtionsTableName().'.type_id'   => $this->type->id
		  	]);
	}
	
	public function sources($targetId, $sourceKey){
		return $this->sys->getTableRelations()
		  ->leftJoin(
		  		$this->tableSource, 
		  		$this->tableSource.'.'.$sourceKey, 
		  		'=', 
		  		$this->sys->getRealtionsTableName().'.source_id'
		  	)
		->select($this->tableSource.'.*')
		->where([
		  		$this->sys->getRealtionsTableName().'.target_id' => $targetId, 
		  		$this->sys->getRealtionsTableName().'.type_id'   => $this->type->id
		]);
	}
}