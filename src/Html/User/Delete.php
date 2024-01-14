<?php

/*
 * This file is part of the Miserend App.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Html\User;

class Delete extends \App\Html\Html
{
    public function __construct()
    {
        $this->title = 'Felhasználó törlése';
        $this->template = 'layout.twig';
        $this->input['uid'] = \App\Request::IntegerRequired('uid');

        $this->user2delete = new \App\User($this->input['uid']);
        if (0 == $this->user2delete->uid) {
            addMessage('Nincs ilyen felhasználó!', danger);

            return;
        }

        $this->input['confirmation'] = \App\Request::SimpleText('confirmation');
        if (!$this->input['confirmation']) {
            $this->askConfirmation();

            return;
        } else {
            $this->delete();
        }
    }

    public function delete()
    {
        global $user;
        if (!$user->checkRole('user')) {
            throw new \Exception('Hiányzó jogosultság miatt nem lehetséges a törlése!');
        }
        $this->user2delete->delete();
        header('Location: /user/catalogue');
    }

    public function askConfirmation()
    {
        $kiir = "\n<span class=kiscim>Biztosan törölni akarod a következő felhasználót?</span>";
        $kiir .= "\n<br><br><span class=alap>".$this->user2delete->username.' ('.$this->user2delete->nev.')</span>';
        $kiir .= "<br><br><a href='/user/".$this->user2delete->uid."/delete?confirmation=confirmed' class=link>Igen</a> - <a href='/user/".$this->user2delete->uid."/edit' class=link>NEM</a>";

        $this->content = $kiir;
    }
}
