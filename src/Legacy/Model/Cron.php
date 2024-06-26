<?php

/*
 * This file is part of the Miserend App.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Legacy\Model;

class Cron extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = ['class', 'function'];

    public function success()
    {
        $this->attempts = 0;
        $this->lastsuccess_at = date('Y-m-d H:i:s');
        $this->renewDeadline();
        $this->save();
    }

    public function renewDeadline()
    {
        $this->deadline_at = date('Y-m-d H:i:s', strtotime('now +'.$this->frequency));
    }

    public function scopeNextJobs($query)
    {
        return $query->where('deadline_at', '<', date('Y-m-d H:i:s'))
                        ->where(function ($query) {
                            $query->where('attempts', '<', 10)
                            ->orWhere('updated_at', '<', date('Y-m-d H:i:s', strtotime('-12 hour')));
                        })
                        ->orderBy('attempts', 'ASC')->orderBy('deadline_at', 'ASC');
    }

    public function initialize()
    {
        $jobsToSave = [
            [self::class, 'initialize', '1 week'],
           ['\App\Legacy\Message', 'clean', '1 hour'],
            ['\App\Legacy\Api\Sqlite', 'cron', '1 day'],
            ['\App\Legacy\Services\ExternalApi\OverpassApi', 'updateUrlMiserend', '1 day'],
            ['\App\Legacy\Services\ExternalApi\OverpassApi', 'clearOldCache', '1 day'],
            ['\App\Legacy\OSM', 'checkBoundaries', '5 min'],
            ['\App\Legacy\OSM', 'checkUrlMiserend', '1 day'],
            ['\App\Legacy\KeywordShortcut', 'updateAll', '1 day'],
            ['\App\Legacy\Distance', 'updateSome', '15 min'],
            ['\App\Legacy\Token', 'cleanOut', '2 hours'],
            ['\App\Legacy\Photos', 'cron', '1 week'],
            ['\App\Legacy\Crons', 'gorogkatolizalas', '1 week'],
            ['\App\Legacy\Crons', 'generateMassTolIgTmp', '1 week'],
        ];
        foreach ($jobsToSave as $jobToSave) {
            $job = self::firstOrCreate(['class' => $jobToSave[0], 'function' => $jobToSave[1]]);
            $job->frequency = $jobToSave[2];
            $job->save();
        }
    }

    public function run()
    {
        $className = $this->class;
        $functionName = $this->function;
        if (class_exists($className)) {
            $object = new $className();
        } else {
            throw new \Exception("Class '$className' does not exists.");
        }
        if (method_exists($object, $functionName)) {
            $object->$functionName();
        } else {
            throw new \Exception('Function '.$className.'->'.$functionName.'() does not exists.');
        }
        $this->success();
    }
}
