<?php

namespace ExternalApi;

use Illuminate\Database\Capsule\Manager as DB;
        
class ExternalApi {

    public $cache = "1 week"; //false or any time in strtotime() format
    public $cacheDir = PATH . 'fajlok/tmp/';
    public $queryTimeout = 30;
    public $query;
    public $name = 'external';
	public $format = 'json'; // enum('json','xml')
	public $strictFormat = true; // if rawData not in XML/JSON format throw new \Exception
	private $curl_opts = [];
	

    function run() {
        $this->runQuery();
    }

    function runQuery() {
        try {
        
            if (!isset($this->rawQuery)) {
                $this->buildQuery();
            }

            if ($this->cache) {
                $this->loadCacheFilePath();
                $this->tryToLoadFromCache();
            }

            if (!isset($this->rawData)) {
                $this->downloadData();
            }

            if ($this->cache) {
                $this->saveToCache();
            }
            
            
        } catch(\Exception $e){
			if($this->isTesting == true)
				throw new \Exception($e->getMessage());
				
            global $config;
            if($this->format == 'json' ) $this->jsonData = [];
			if($this->format == 'yml' ) $this->xmlData = [];
            $this->error = \Html\Html::printExceptionVerbose($e,true);
            if($config['debug'] > 1) echo $this->error;
            elseif($config['debug'] > 0) addMessage($this->error,'warning');
            return false;
        }
        return true;
    }

    function tryToLoadFromCache() {
        if (file_exists($this->cacheFilePath)) {
            $this->cacheFileTime = date('Y-m-d H:i:s',filemtime($this->cacheFilePath));
            if (filemtime($this->cacheFilePath) > strtotime("-" . $this->cache)) {
                $this->rawData = file_get_contents($this->cacheFilePath);
				if($this->format == 'json' ) {
					$this->jsonData = json_decode($this->rawData);
					if ($this->jsonData === null) {
						throw new \Exception("External API data has been loaded from cache but data is not a valid JSON!\n".$this->rawData);
					} else {
						return true;
					}
				} elseif($this->format == 'xml' ) {
					$this->xmlData = @simplexml_load_string($this->rawData);					
					if ($this->xmlData == false) {
						throw new \Exception("External API data has been loaded from cache but data is not a valid XML!\n".$this->rawData);
					} else {
						return true;
					}
				
				}
            } else {
                unlink($this->cacheFilePath);
                return false;
            }
        } else {
            return false;
        }
    }

    function saveToCache() {
        if (!file_put_contents($this->cacheFilePath, $this->rawData)) {
            throw new \Exception("We could not save the cacheFile to " . $this->cacheFilePath);
        }
    }

    function downloadData() {        
        $header = array("cache-control: no-cache","Content-Type: application/".$this->format);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->apiUrl . $this->rawQuery);
		//echo $this->apiUrl . $this->rawQuery."\n";
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        curl_setopt($ch, CURLOPT_HEADER  , false);  // we want headers
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, "miserend.hu");



		foreach($this->curl_opts as $name => $value ) {
			curl_setopt($ch, $name, $value );
		}
		
        $this->rawData = curl_exec($ch);
    
        $this->responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE ); 
		if(curl_error($ch)) {
			$this->error = [curl_errno($ch), curl_error($ch)];		
			throw new \Exception($this->error[1]." (curl)");
		}
        
        $this->saveStat();
        
		if($this->format == 'json' ) {
			$this->jsonData = json_decode($this->rawData);
			if ($this->jsonData === null ) {
				if($this->strictFormat)
					throw new \Exception("External API return data is not a valid JSON! \n<br/> ResponseCode: " . $this->responseCode . " \n<br/> Response: ". $this->rawData );
				else
					$this->jsonData = json_decode("[]");
			}
		}
		else if($this->format == 'xml' ) {
			$this->xmlData = @simplexml_load_string($this->rawData);
			if ($this->xmlData === null ) {
				if($this->strictFormat)
					throw new \Exception("External API return data is not a valid XML! \n<br/> ResponseCode: " . $this->responseCode . " <br/>\n Response: ". $this->rawData );
				else
					$this->xmlData = false;
			}
		}
		
		
		if(!in_array($this->responseCode,[200,404]))				
			throw new \Exception("External API returned bad http response code: " . $this->responseCode. "\n<br/> Response: ". $this->rawData);
        
    }

    function clearOldCache() {
        $this->cache;
        $this->cacheDir;
        $files = scandir($this->cacheDir);
        foreach ($files as $file) {
            if (preg_match('/^' . $this->name . '_(.*)\.'.$this->format.'/i', $file)) {
                $filemtime = filemtime($this->cacheDir . $file);
                $deadline = strtotime('now -' . $this->cache);
                if ($filemtime < $deadline) {
                    unlink($this->cacheDir . $file);
                }
            }
        }
    }

    function loadCacheFilePath() {
        $this->cacheFilePath = $this->cacheDir . $this->name . "_" . md5($this->query) . ".".$this->format;
    }
       
    function saveStat() {
        
        $url = $this->apiUrl.$this->rawQuery;
        $url = ( strlen($url) > 255 ) ? substr($url, 0, 252).'...' : $url;
        $query = DB::table('stats_externalapi')->where('url',$url)->where('date',date('Y-m-d'));
        if($current = $query->first()) {   
            if($current->rawdata != $this->rawData ) $diff = $current->diff + 1; else $diff = $current->diff;            
            $echo = $query->update([
                        'name' => $this->name,
                        'url' => $url ,                    
                        'date' => date('Y-m-d'),                
                        'responsecode' => $this->responseCode,
                        'rawdata' => $this->rawData,
                        'count'=> $current->count + 1,
                        'diff'=> $diff
            ]);
        } else {
            DB::table('stats_externalapi')->insert(
                [
                    'name' => $this->name,
                    'url' => $url,                    
                    'date' => date('Y-m-d'),                
                    'responsecode' => $this->responseCode,
                    'rawdata' => $this->rawData,
                    'count'=> 1,
                    'diff' => 1
                ]
            );
        }
    }
    
	
	function test() {
	
		$return = true;
		$this->isTesting = true;
		
		$cache = $this->cache;
		$this->cache = false;
		
		try {
			if(!isset($this->testQuery)) 
				throw new \Exception("Hiányzik a testQuery, így nem tudjuk ellenőrizni.");
			
			$this->query = $this->testQuery;
			
			$this->run();			
		
		}
		catch (\Exception $e) {
			$return = $e->getMessage();
		}
				
		$this->cache = $cache;
		$this->isTesting = false;
		return $return;	
	}
	
	function curl_setopt($name, $value) {
		$this->curl_opts[$name] = $value;
	
	
	}
	
	
}

class Exception extends \Exception {
	
}