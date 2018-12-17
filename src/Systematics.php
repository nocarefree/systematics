<?php
namespace Nocarefree\Systematics\Query;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Cache;
/**
 * 一个 DB 类型 ，一个是Eloquent
 * User->sysmatics('sdfs')->getMany();
 * User->sysmatics('sdfs')->getOne();
 * User->sysmatics('sdfs')->getBelong();
 * User->sysmatics('sdfs')->getTree();
 * User->sysmatics('sdfs')->getBranch();
 */
class Systematics{
	/**
	 * [$connection default db connection]
	 * @var [Illuminate\Database\Connection]
	 */
	private $connection;
	/**
	 * [$tableSysmaticsName relations table name]
	 * @var [string]
	 */
	private $tableSysmaticsName;
	/**
	 * [$tableSysmaticsTypesName relations type table name]
	 * @var [string]
	 */
	private $tableSysmaticsTypesName;
	/**
	 * [$cache description]
	 * @var [type]
	 */
	private $cache;
	private $cacheAvailable;

	public function __construct(Connection $connection, $tableSysmaticsName = '', $tableSysmaticsTypesName = '' ,$cache = ture){
		$this->connection = $connection;
		$this->tableSysmaticsName      = $tableSysmaticsName?:config('sysmatics.databse.tableSysmaticsName'); 
		$this->tableSysmaticsTypesName = $tableSysmaticsTypesName?:config('sysmatics.databse.tableSysmaticsTypesName'); 
		$this->cacheAvailable = $cache;
	}
	public function dbRelation(){
		return $this->connection->table($this->tableSysmaticsName);
	}
	public function dbType(){
		return $this->connection->table($this->tableSysmaticsTypesName);
	}
	public function getCache($key){
		if($this->cacheAvailable){
			return $this->cache[$key];
		}
		return null;
	}
	public function setCacahe($key, $value){
		if($this->cacheAvailable){
			$this->cache[$key] = $value;
		}
	}
	public function flush(){
		$this->cache = [];
	}
	public function item($relationCode, $attributes = [], $primaryKey = 'id'){
		return new Item($this, $relationCode, $attributes, $primaryKey);
	}
	public function getTypeByCode($code){
		$key = 'getRelationTypeByCode-'. $code;
		if( (strpos($code, '/') > 0) == false ){
			return null;
		}
		if( empty($value = $this->getCache($key)) ){
			$value = $this->dbType()->where('code', $code)->first();
			if($value){
				list($value->tableSource, $value->tableTarget) = explode('/',$code);
			}
			$this->setCacahe($key, $value);
		}
		return $value;
	}
	public function getTargets($relationCode, $sourceId, $targetKey){
		$key = 'getTargets-'. $relationCode . $sourceId;
		if( empty($value = $this->system->getCache($key)) ){
			if($type = $this->getRelationTypeByCode($relationCode)){
				$value = $this->dbRelation()
							  ->leftJoin(
							  		$type->tableTarget, 
							  		$type->tableTarget.'.'.$targetKey, 
							  		'=', 
							  		$this->tableSysmaticsTypesName.'.target_id'
							  	)
							  ->select($type->tableTarget.'.*')
							  ->where([
							  		$this->tableSysmaticsTypesName.'.source_id' => $sourceId, 
							  		$this->tableSysmaticsTypesName.'.type_id' => $type->id
							  	])
							  ->get();
				$this->system->setCache($key, $value);
			}
		}
		return $value;
	}
	public function getSource($relationCode, $targetId, $sourceKey){
		$key = 'getSource-'. $relationCode . $targetId;
		if( empty($value = $this->system->getCache($key)) ){
			if($type = $this->getRelationTypeByCode($relationCode)){
				$value = $this->dbRelation()
							  ->leftJoin(
							  		$type->tableSource, 
							  		$type->tableSource.'.'.$sourceKey, 
							  		'=', 
							  		$this->tableSysmaticsTypesName.'.source_id'
							  	)
							  ->select($type->tableSource.'.*')
							  ->where([
							  		$this->tableSysmaticsTypesName.'.target_id' => $targetId, 
							  		$this->tableSysmaticsTypesName.'.type_id' => $type->id
							  	])->first();
				$this->system->setCache($key, $value);
			}
		}
		return $value;
	}
}