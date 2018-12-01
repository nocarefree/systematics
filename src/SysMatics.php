<?php

namespace Nocarefree\Systematics;

use Illuminate\Support\Facades\DB;

class Sysmatics{
	private $db;
	private $table_sysmatics_type_map;
	private $table_sysmatics;

	public function __construct(DB $db){
		$this->db = $db;
		$this->sysmatics = $db->table('sysmatics');
		$this->sysmatics_type = $db->table('sysmatics_type');
	}

	public function getTypeByCode($code){
		$type = NULL;

		if(isset($this->$table_sysmatics_type_map[$code])){
			$type = $this->$table_sysmatics_type_map[$code];
		}else{
			$type = $this->sysmatics_type->where('code', $code)->first();
			if($type){
				$type = $type->toArray();
				$this->$table_sysmatics_type_map[$code] = $type;
			}
		}

		return $type;
	}

	public function setTypeMap($code_map){
		$this->$table_sysmatics_type_map = $code_map;
	}

	public function getChildren($source_id, $type_code){
		$type = $this->getTypeByCode($type_code);
		if(!empty($type)){
			return $sysmatics->where(['source_id'=>$source_id, 'type_id'=>$type['id']])->get();
		}
		return false;
	}

	public function getAllChildren($source_id, $type_code, $children_key = 'children'){
		$type = $this->getTypeByCode($type_code);
		$data = NULL;
		if(!empty($type)){
			$rows = $this->getChildren($source_id, $type_code);
			foreach($rows as &$row){
				$next = $this->getChildren($row->source_id, $type_code);
				if(!empty($next)){
					$row->{$children_key} = $next;
				}
			}
			$data = $rows;
		}
		return $data;
	}

	public function set($source_id, $target_id, $type_code){
		$type = $this->getTypeByCode($type_code);
		if(!empty($type)){
			return $sysmatics->insert(['source_id'=>$source_id, 'target_id'=>$target_id, 'type_id'=>$type_id]);
		}
		return false;
	}
}