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

    public static function korisnikPoImenuIPrezimenu($ime, $prezime) {
        $korisnici = Korisnik::where('ime', 'like' , '%' . $ime . '%')
            ->where('prezime', 'like', '%' . $prezime . '%')
            ->get()
            ->toArray();

        return $korisnici;
    }
}
