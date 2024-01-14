<?php

/*
 * This file is part of the Miserend App.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = ['name', 'type', 'uid', 'timeout'];
    protected $appends = ['isValid'];

    public function getIsValidAttribute($value)
    {
        if ($this->timeout < date('Y-m-d H:i:s')) {
            return false;
        }

        return true;
    }

    public function extend()
    {
        global $config;
        $this->timeout = date('Y-m-d H:i:s', strtotime('+'.$config['token'][$this->type]));
        $this->save();
    }
}
