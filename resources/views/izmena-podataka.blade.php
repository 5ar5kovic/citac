@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="col-md-8 offset-md-2">
            <form action="{{ route('upis-podataka') }}" method="POST">
                @csrf
                <input type="hidden" id="id" name="id" value="{{ $korisnik['id'] }}">
                <div class="form-group">
                    <label for="ime">Име</label>
                    <input type="text" class="form-control" id="ime" name="ime" value="{{ $korisnik['ime'] }}">
                </div>
                <div class="form-group">
                    <label for="prezime">Презиме</label>
                    <input type="text" class="form-control" id="prezime" name="prezime" value="{{ $korisnik['prezime'] }}">
                </div>
                <div class="form-group">
                    <label for="jmbg">ЈМБГ</label>
                    <input type="text" class="form-control" id="jmbg" name="jmbg" value="{{ $korisnik['jmbg'] }}">
                </div>
                <div class="form-group">
                    <label for="sifra_objekta">Шифра објекта</label>
                    <input type="text" class="form-control" id="sifra_objekta" name="sifra_objekta" value="{{ $korisnik['sifra_objekta'] }}">
                </div>
                <div class="form-group">
                    <label for="broj_vodomera">Број водомера</label>
                    <input type="text" class="form-control" id="broj_vodomera" name="broj_vodomera" value="{{ $korisnik['broj_vodomera'] }}">
                </div>
                <div class="form-group">
                    <label for="broj_clanova_domacinstva">Број чланова домаћинства</label>
                    <input type="text" class="form-control" id="broj_clanova_domacinstva" name="broj_clanova_domacinstva" value="{{ $korisnik['broj_clanova_domacinstva'] }}">
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="pausalac" name="pausalac" {{ ($korisnik['pausalac']) ? "checked" : "" }}>
                    <label class="form-check-label" for="pausalac">
                        Паушалац
                    </label>
                </div>
                <br>
                <div class="form-group">
                    <label for="pausalac_kubika">Паушално плаћа м3</label>
                    <input type="text" class="form-control" id="pausalac_kubika" name="pausalac_kubika" value="{{ $korisnik['pausalac_kubika'] }}">
                </div>
                <button type="submit" class="btn btn-primary">Сачувај</button>
            </form>
        </div>
    </div>

@endsection
