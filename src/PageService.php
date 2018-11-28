<?php 

/**
 * 完成文件下载
 */

namespace Nocarefree\Spider;

use Illuminate\Support\Collection;
use Nocarefree\Spider\Jobs\Download;

class SpiderPool{

	/**
	 * [$this->db Database Collection]
	 * @var [Illuminate\Support\Collection]
	 */
	private $db;

	/**
	 * [$this->group_id Task Group ID]
	 * @var integer
	 */	
	private $group_id;

	/**
	 * [$this->urls Task Url]
	 * @var [array]
	 */
	private $urls = [];

	/**
	 * [$this->process_num Task Thead Num]
	 * @var integer
	 */
	private $process_num = 1;

	/**
	 * [$this->setting Download Setting]
	 * @var [array]
	 */
	private $setting = [];

	public function __construct(Collection $connect, int $group_id = 0, int $process_num = 1, array $setting = []){
		
		$this->db = $connect;
		$this->group_id = 0; 
		$this->process_num = $process_num>0 ? $process_num : 1;
		$this->setting = $setting;
		$this->table_name = config('spider.database.table', 'spider_logs');
	}

	public function run(array $status = [Download::STATUS_INIT]){
		$this->init();

		array_splice($status, array_search(Download::STATUS_INIT, $status),1);

		$ids = [];
		$this->db->table($this->table_name)->select('id')
			->where('group_id', $this->group_id)
			->whereIn('status', $status)
			->where('status', '<>', Download::STATUS_RUNNING)->chuck(1000,function($logs) use(&$ids){
				$ids = array_merge($ids, $logs->pluck('id')->toArray());
			});
		
		$this->db->table($this->table_name)->whereIn('id', $ids)->update(['status'=>Download::STATUS_PREPARE]);

		foreach(array_chunk($ids, $this->process_num) as $chunk) {
			Download::dispatch($this->db->all(), $chunk, $this->setting);
		}
		
		return $result;
	}

	public function setting(){
		$this->setting = $setting;
	}

	public function add($base_urls){
		$urls = [];
		foreach($base_urls as $url){
			$md5 = md5($url);
			$urls[$md5] = [
				'url' => $url,
				'md5' => $md5,
				'group_id' =>  $this->group_id,
			];
		}

		$exist_logs = $this->db->table($this->table_name)->whereIn('md5', array_kes($urls))->where('group_id', $this->group_id)->get();

		foreach($exist_logs as $log){
			unset($urls[$log->md5]);
		}

		if(!empty($urls)){
			$this->db->table($this->table_name)->insert($urls);
		}
	}

	public function delete(){
		$this->db->table($this->table_name)->where('group_id', $this->group_id)->delete();
	}


}	