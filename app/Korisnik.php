<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Korisnik extends Model
{
    protected $table = 'korisnik';

    public static function sviKorisnici() {
        $korisnici = Korisnik::all()->toArray();

        return $korisnici;
    }
}
