<?php

namespace Eloquent;

use Illuminate\Database\Capsule\Manager as DB;

class Remark extends \Illuminate\Database\Eloquent\Model {
    
    public function church() {
        return $this->belongsTo('\Eloquent\Church');
    }

    function scopeSelectCreatedMonth($query) {
        return $query->addSelect(DB::raw('DATE_FORMAT(created_at,\'%Y-%m\') as created_month'), DB::raw('COUNT(*) as count_created_month'));
    }

    function scopeSelectCreatedYear($query) {
        return $query->addSelect(DB::raw('DATE_FORMAT(created_at,\'%Y\') as created_year'), DB::raw('COUNT(*) as count_created_year'));
    }

    function scopeCountByCreatedMonth($query) {
        return $query->selectCreatedMonth()
                        ->groupBy('created_month')->orderBy('created_month');
    }

    function scopeCountByCreatedYear($query) {
        return $query->selectCreatedYear()
                        ->groupBy('created_year')->orderBy('created_year');
    }
   
    public function getChurchAttribute($value) {
        return \Eloquent\Church::find($this->church_id);
        
    }    
    
    /* 
     * custom functions 
     */
    public function appendComment($text) {
        global $user;
        if($text != '') {
            $newline = "\n<img src='/img/edit.gif' align='absmiddle' title='" . $user->username . " (" . date('Y-m-d H:i:s') . ")'>" . $text;
            $this->adminmegj .= $newline;
        }
    }
    
    function emails() {                
        /*
         * miserend adminiok
         * egyházmegyei felelős(ök)
         * templom feltöltésre jogosult felhasználó
         */
        $emails = [];        
        /* Miserend Adminok */
        $admins = DB::table('user')->where('jogok','LIKE','%miserend%')->where('notifications',1)->get();
        foreach($admins as $admin) {
           $emails[$admin->email] = ['admin',$admin->email,$admin];
        }              
        /* Egyházmegyei felelős (csak felhasználónév alapján) */
        $responsabile = DB::table('egyhazmegye')->select('user.*')->where('egyhazmegye.id',$this->church->egyhazmegye)->leftJoin('user','user.login','=','egyhazmegye.felelos')->where('notifications',1)->first();
        if($responsabile) {
            $emails[$responsabile->email] = ['diocese', $responsabile->email, $responsabile];
        }
        /* Templom felelős. Még csak egy!! */
        $responsabile = DB::table('user')->where('login',$this->church->letrehozta)->where('notifications',1)->first();
        if($responsabile) {
            $emails[$responsabile->email] = ['responsible', $responsabile->email, $responsabile];
        }
        
        foreach($emails as $email) {
            $this->sendMail($email[0], $email[1], $email[2]);            
        }
                
        return true;
    }

    function sendMail($type, $to, $addressee = false) {
        if($addressee) $this->addressee = $addressee;
        else  $this->addressee = false;
                      
        $this->append('church');
        
        $mail = new \Eloquent\Email();                
        $mail->render('remark_'.$type,$this);
        $mail->send($to);
    }
}
