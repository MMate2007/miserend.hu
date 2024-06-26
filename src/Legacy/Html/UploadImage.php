<?php

/*
 * This file is part of the Miserend App.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Legacy\Html;

use Illuminate\Database\Capsule\Manager as DB;

class UploadImage extends Html
{
    public function __construct($path)
    {
        $this->tid = $path[0];
        $this->church = \App\Legacy\Model\Church::find($this->tid);
        $this->pageDescription = 'új kép feltöltése';

        if (isset($_REQUEST['upload'])) {
            $this->ajax();
            exit;
        }
    }

    public function ajax()
    {
        $tid = $_POST['id'];
        if ($tid != $this->tid) {
            throw new \Exception('The church.id of the page and the form are not the same.');
        }

        $photo = new \App\Legacy\Model\Photo();
        $photo->church_id = $this->church->id;
        $photo->uploadFile($_FILES['FileInput']);

        $photo->title = htmlspecialchars($_REQUEST['description']);
        $photo->save();
        echo "Siker! Feltöltöttük. Jöhet a következő!<br/><img src='".$photo->smallUrl."'>";

        $this->photo = $photo;

        /*
         * miserend adminiok
         * egyházmegyei felelős(ök)
         * templom feltöltésre jogosult felhasználó
         */
        $emails = [];
        /* Miserend Adminok */
        $admins = DB::table('user')->where('jogok', 'LIKE', '%miserend%')->where('notifications', 1)->get();
        foreach ($admins as $admin) {
            $emails[$admin->email] = ['image_admin', $admin->email, $admin];
        }
        /* Egyházmegyei felelős (csak felhasználónév alapján) */
        $responsabile = DB::table('egyhazmegye')->select('user.*')->where('egyhazmegye.id', $this->church->egyhazmegye)->leftJoin('user', 'user.login', '=', 'egyhazmegye.felelos')->where('notifications', 1)->first();
        if ($responsabile) {
            $emails[$responsabile->email] = ['image_diocese', $responsabile->email, $responsabile];
        }
        /* Templom felelősök */
        $churchHolders = DB::table('church_holders')->where('church_id', $this->church->id)->where('church_holders.status', 'allowed')->leftJoin('user', 'user.uid', '=', 'church_holders.user_id')->where('user.notifications', 1)->get();
        foreach ($churchHolders as $churchHolder) {
            $emails[$churchHolder->email] = ['image_responsible', $churchHolder->email, $churchHolder];
        }

        foreach ($emails as $email) {
            if (isset($email[2])) {
                $this->addressee = $email[2];
            } else {
                $this->addressee = false;
            }
            $mail = new \App\Legacy\Model\Email();
            $mail->render($email[0], $this);
            $mail->send($email[1]);
        }

        exit;
    }
}
