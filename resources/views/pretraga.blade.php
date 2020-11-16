@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="col-md-8 offset-md-2">
            <form method="POST">
                @csrf
                <div class="row">
                    <div class="form-group">
                        <label for="ime">Име</label>
                        <input type="text" class="form-control" id="ime" name="ime" placeholder="Унесите име">
                    </div>
                    <div class="form-group">
                        <label for="prezime">Презиме</label>
                        <input type="text" class="form-control" id="prezime" name="prezime" placeholder="Унесите презиме">
                    </div>
                </div>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Претрага</button>
            </form>
            <div class="content" style="margin-top:2em">
                @foreach($korisnici as $korisnik)
                    <div class="card bg-info">
                        <div class="card-body">
                            <h5 class="card-title">{{ $korisnik['ime'] . " " . $korisnik['prezime']}}</h5>
                            <p class="card-text">
                                <strong>ЈМБГ: </strong>{{ $korisnik['jmbg'] }}
                                <br/> 
                                <strong>Шифра објекта: </strong>{{ $korisnik['sifra_objekta'] }}
                                <br/>
                                <strong>Број водомера: </strong>{{ $korisnik['broj_vodomera'] }}
                                <br/>
                            </p>
                            <a href="{{ route('korisnik', ['id'=>$korisnik['id']] ) }}" class="btn btn-primary">Отвори</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection
