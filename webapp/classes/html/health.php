<?php

namespace Html;

use Illuminate\Database\Capsule\Manager as DB;

class Health extends Html {

    public function __construct() {
        parent::__construct();
        $this->setTitle('Miserend.hu állapotáról');
		
		//General informations
		global $config;
		
		$this->infos = [
			['server', $_SERVER['SERVER_SOFTWARE']],
			['php verzió', phpversion()],
			['php extensions', implode(', ',get_loaded_extensions())],
			['environment', $config['env'] ],
			['debug', $config['debug']],
			['error_reporting', $config['error_reporting'] ],
			['mail/debug', $config['mail']['debug'] ]
		];
		
		
		$results = [];
		for($i=1;$i<=4;$i++) {		
			$sqlite = new \Api\Sqlite();
			$sqlite->version = $i;			
			
			if(!$sqlite->checkSqliteFile()) {
				$alert = 'danger';
			} else 
				$alert = 'success';
				
			$results[] = " <a class=\"alert-".$alert."\" href=\"$sqlite->folder.$sqlite->sqliteFileName\">".$sqlite->sqliteFileName."</a> ";
		}
		$this->infos[] = ["sqlite files",implode(", ",$results)];
		$result = false;

		
		// Health of CronJobs
		$this->cronjobs = \Eloquent\Cron::orderBy('deadline_at','DESC')->get()->toArray();
		
		
		
		
		return;
			
    }
}