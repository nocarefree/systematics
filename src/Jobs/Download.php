<?php

namespace Nocarefree\Spider\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class Download implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $retry = 1;
    public $timeout = 300;

    const STATUS_INIT       = 0;
    const STATUS_PREPARE    = 1;
    const STATUS_RUNNING    = 2;
    const STATUS_FAILED     = 8;
    const STATUS_COMPLETE   = 9;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $connect_config, array $ids, array $setting)
    {
        $this->db = new Collection($connect_config);
    	$this->ids = $ids;
        $this->setting = $setting;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $table_name = config('spider.database.table', 'spider_logs');

        $this->db->table($table_name)->whereIn('id', $this->ids)->update(['status'=>Download::STATUS_RUNNING]);
        $logs = $this->db->table($table_name)->whereIn('id', $this->ids)->get();

    	$save_dir = rtrim($this->setting['save_dir']);
        $mh = curl_multi_init();
        $temp = [];

        foreach($logs as $log){

            $id = $log->id;
            $ch = curl_init();
            $pa = $save_dir . '/' . $log->id. substr($log->url, strrpos($log->url, '.')+1);
            $fp = fopen($pa , 'a');

            curl_setopt($ch, CURLOPT_URL, $log->url );

            if(!empty($this->setting['proxy'])){
                curl_setopt($ch, CURLOPT_PROXY, $this->setting['proxy']['value']);
                curl_setopt($ch, CURLOPT_PROXYTYPE, $this->setting['proxy']['type']);
            }

            if(starts_with($log->url ,'https://')){
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            }

            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->setting['timeout']);


            if(!empty($this->setting['user_agent'])){
                curl_setopt($ch, CURLOPT_USERAGENT, $this->setting['user_agent']);
            }else{
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
            }
            curl_multi_add_handle($mh, $ch);

            $temp[$id] = [];
            $temp[$id]['ch'] = $ch;
            $temp[$id]['pa'] = $pa;
            $temp[$id]['fp'] = $fp;

        }

        $active = null;

        do{
            curl_multi_exec($mh, $active);
        } while($active);

        $error = [];
        foreach($temp as $id=>$value){
            $ch = $value['ch'];
            $pa = $value['pa'];
            $fp = $value['fp'];

            $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($info>400){
                $error[] = $id;
            }else{
                fwrite($fp, curl_multi_getcontent($ch));
                $this->db->table($table_name)
                    ->where('id', $result[self::STATUS_COMPLETE])
                    ->update([
                        'status'=>Download::STATUS_COMPLETE, 
                        'path'=> realpath($pa)
                    ]);
            }
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
            fclose($fp);
        }
        $this->db->table($table_name)->whereIn('id', $error)->update(['status'=>Download::STATUS_FAILED]);
    }

}
