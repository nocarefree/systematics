<?php
namespace Nocarefree\Systematics;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

/**
 * 多应用管理
 */
class Pool{

	static $cache;

	public function __construct(array $config){
		$this->config = $config;
		$this->items = [];

		$connections = DB::getConnections();
		foreach($connections as $name => $dbConnection){
			$this->items[$name] = new Item($name, $connection, $this->config['connections']['name']);
		}
	}

	public function getItem(string $name){
		if(!isset($this->items[$name])){
			throw new \Exception();
		}
		return $this->items[$name];
	}


	
}