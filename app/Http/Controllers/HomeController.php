<?php

namespace App\Http\Controllers;

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

        return view('home')->with(array('korisnici'=>$korisnici));
    }
}
