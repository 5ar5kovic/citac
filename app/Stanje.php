<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stanje extends Model
{
    protected $table = 'stanje';

    public static function getStanje($idKorisnik, $mesec, $godina) {
        $stanje = Stanje::where('korisnik_id', $idKorisnik)
            ->where('mesec', $mesec)
            ->where('godina', $godina)
            ->get()
            ->toArray();

        if (count($stanje) > 0) {
            return $stanje[0]['stanje'];
        } else {
            return null;
        }
    }
}
