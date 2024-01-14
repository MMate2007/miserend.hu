<?php

/*
 * This file is part of the Miserend App.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Html\Church;

use App\Html\Html;

class Church extends Html
{
    public function __construct($routeArguments)
    {
        global $user;

        $tid = $routeArguments['church_id'] ?? null;

        if (null === $tid) {
            throw new \Exception('Church does not exist.');
        }

        $church = \App\Model\Church::find($tid);
        if (!$church && $user->checkRole('miserend')) {
            $church = \App\Model\Church::withTrashed()->find($tid);
            if ($church) {
                addMessage('Ez a templom törölve van. Nem létezik. Elhunyt. Vége.', 'danger');
            }
        }

        if (!$church) {
            throw new \Exception("Church with tid = '$tid' does not exist.");
        }
        $church = $church->append(['readAccess', 'writeAccess', 'liturgiatv']);

        if (!$church->readAccess) {
            throw new \Exception("Read access denied to church tid = '$tid'");
        }

        if ('n' == $church->ok) {
            addMessage('Ez a templom le van tiltva! Csak adminisztrátorok számára látható ez az oldal.', 'warning');
        } elseif ('f' == $church->ok) {
            addMessage('Ez a templom áttekintésre vár. Csak adminisztrátorok számára látható ez az oldal.', 'warning');
        }

        $church->photos = $church->photos()->get();

        if ($church->osm) {
            $church->accessibility = $church->osm->tagList;
        }
        $church->kozossegek = $church->kozossegek;

        global $_honapok;
        $this->_honapok = $_honapok;

        if ('' != $church->lat && !isset($church->location->city)) {
            $church->MdownloadOSMBoundaries();
        }

        $church->MgetReligious_administration();

        if (\count($church->neighbours) < 1) {
            // $distance = new \Distance();
            // $distance->MupdateChurch($church);
        }

        copyArrayToObject($church->toArray(), $this);

        $this->church = ['remarksicon' => $church->remarksicon, 'id' => $church->id]; // A church/_adminlinks.twig számára kell ez. Bocsi.
        $this->neighbours = $church->neighbours;

        if (isset($this->location->city)) {
            $this->setTitle($this->nev.' ('.$this->location->city['name'].')');
        } else {
            $this->setTitle($this->nev);
        }

        $this->updated = str_replace('-', '.', $this->frissites).'.';

        // Miseidőpontok
        $misek = getMasses($tid);

        // Convert to OSM ServiceTimes
        if (isset($user->isadmin) && 1 == $user->isadmin) {
            $serviceTimes = new \App\ServiceTimes();
            $serviceTimes->loadMasses($tid);
            if (!isset($serviceTimes->error)) {
                $this->service_times = print_r(preg_replace('/;/', ";\n", $serviceTimes->string), 1)."\n".$serviceTimes->linkForDetails;
            } else {
                $this->service_times = $serviceTimes->error;
            }
        }

        /*
          $staticmap = "kepek/staticmaps/" . $tid . "_227x140.jpeg";
          if (file_exists($staticmap))
          $cim .= "<a href=\"http://www.openstreetmap.org/?mlat=$lat&mlon=$lng#map=15/$lat/$lng\" target=\"_blank\"><img src='/kepek/staticmaps/" . $tid . "_227x140.jpeg'></a>";
          else
          $cim .= "<br/>";
         */

        $this->photos;
        if (isset($this->photos[0])) {
            $this->addExtraMeta('og:image', '/kepek/templomok/'.$tid.'/'.$this->photos[0]->fajlnev);
        }

        if ($user->checkFavorite($tid)) {
            $this->favorite = 1;
        }

        $data = \App\Html\Map::getGeoJsonDioceses();
        $this->dioceseslayer = [];
        $this->dioceseslayer['geoJson'] = json_encode($data);

        $this->miserend = $misek;
        $this->alert = LiturgicalDayAlert('html');

        $this->isChurchHolder = $user->getHoldingData($this->id);
    }

    public static function factory($routeArguments)
    {
        return new self($routeArguments);
    }
}
