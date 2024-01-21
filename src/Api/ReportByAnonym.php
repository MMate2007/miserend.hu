<?php

/*
 * This file is part of the Miserend App.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api;

class ReportByAnonym extends Report
{
    public function prepareUser()
    {
        $this->user = new \App\User();
        $this->user->name = 'Mobil felhasználó';
        if (isset($this->input['email'])) {
            $this->user->email = sanitize($this->input['email']);
        }
    }
}
