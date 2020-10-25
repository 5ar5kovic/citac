<?php

namespace App\Http\Controllers;

use App\Stanje;
use Illuminate\Http\Request;
use App\Korisnik;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $korisnici = Korisnik::sviKorisnici();

        $mesec = date('m');
        $godina = date('Y');
        foreach ($korisnici as &$korisnik) {
            $korisnik['stanje'] = Stanje::getStanje($korisnik['id'], $mesec, $godina);
        }

        return view('home')->with(array('korisnici'=>$korisnici));
    }

    public function unesiStanje(Request $request)
    {
        $data = $request->input();
        $mesec = date('m');
        $godina = date('Y');

        $stanjeModel = new Stanje();
        $stanjeModel->korisnik_id = $data['idKorisnik'];
        $stanjeModel->mesec = $mesec;
        $stanjeModel->godina = $godina;
        $stanjeModel->vreme_citanja = date('Y-m-d H:i:s');
        $stanjeModel->stanje = $data['stanje'];
        $stanjeModel->save();
    }
}
