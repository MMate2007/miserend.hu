<?php

/*
 * This file is part of the Miserend App.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Html\User;

class LostPassword extends \App\Html\Html
{
    public function __construct()
    {
        $this->input['lostPassword'] = \App\Request::simpleText('lostPassword');
        if ('sendMeMyPassword' == $this->input['lostPassword']) {
            $this->input['data'] = \App\Request::TextRequired('data');

            if ($this->recoverUser()) {
                $this->newpassword = $this->recoveredUser->generatePassword();
                $this->recoveredUser->newPassword($this->newpassword);
                $this->sendNewPasswordMail();
            }
        } else {
            $this->input['data'] = \App\Request::Text('data');
        }
    }

    public function recoverUser()
    {
        $userByNevOrEmail = new \App\User($this->input['data']);
        if ($userByNevOrEmail->uid > 0) {
            $this->recoveredUser = $userByNevOrEmail;

            return true;
        } else {
            addMessage('A megadott adatok alapján nem találtunk felhasználót.', 'danger');

            return false;
        }
    }

    public function sendNewPasswordMail()
    {
        $email = new \App\Model\Email();
        $this->recoveredUser->newpwd = $this->newpassword;

        $email->render('user_newpassword', $this->recoveredUser);
        $email->send($this->recoveredUser->email);

        addMessage('Az új jelszót elküldtük a regisztrált emailcímre. Kérjük lépjen be, és mihamarabb módosítsa.', 'success');
    }
}
