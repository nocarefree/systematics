<?php
namespace Nocarefree\Systematics;

use Illuminate\Database\Connection;

class Systematics{

	public function __construct(Connection $connection){
		$this->connection = $connection;
		$this->config     = config('systematics.connections')[$connection->getName()];
	}

	public function getRealtionsTableName(){
		return $this->config['table_prefix'] . $this->config['table_name']['relations'];
	}

	public function getTypesTableName(){
		return $this->config['table_prefix'] . $this->config['table_name']['types'];
	}

	public function getTableRelations(){
		return $this->connection->table($this->getRealtionsTableName());
	}

	public function getTableTypes(){
		return $this->connection->table($this->getTypesTableName());
	}

	public function getSplitChar(){
		return $this->config['table_split'];
	}

	public function relation($source, $target){
		return new relation($this, $source, $target);
	}
}