@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="col-md-8 offset-md-2">
            <div class="d-flex align-items-center p-3 my-3 text-white-50 rounded shadow-sm" style="background-color:#6f42c1">
                <div class="lh-100">
                    <h6 class="mb-0 text-white lh-100">Претрага корисника</h6>
                </div>
            </div>
            
            <div class="my-3 p-3 bg-white rounded shadow-sm">
                <form method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName">Име</label>
                            <input type="text" class="form-control" id="ime" name="ime" placeholder="Унесите име" value="{{request('ime')}}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName">Презиме</label>
                            <input type="text" class="form-control" id="prezime" name="prezime" placeholder="Унесите презиме" value="{{request('prezime')}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-md-6 offset-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="pausalac" name="pausalac">
                                <label class="form-check-label" for="pausalac">
                                    Паушалац
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="background-color:#6f42c1"><i class="fa fa-search"></i> Претрага</button>
                </form>
            </div>
            <div class="my-3 p-3 bg-white rounded shadow-sm">
                @foreach($korisnici as $korisnik)
                    <div class="card bg-warning">
                        <div class="card-body">
                            <h5 class="card-title"><a href="{{ route('korisnik', ['id'=>$korisnik['id']] ) }}">{{ $korisnik['ime'] . " " . $korisnik['prezime']}}</a></h5>
                            <p class="card-text">
                                <strong>ЈМБГ: </strong>{{ $korisnik['jmbg'] }}
                                <br/> 
                                <strong>Шифра објекта: </strong>{{ $korisnik['sifra_objekta'] }}
                                <br/>
                                <strong>Број водомера: </strong>{{ $korisnik['broj_vodomera'] }}
                                <br/>
                            </p>
                        </div>
                    </div>
                    <br>
                @endforeach
            </div>
        </div>
    </div>

@endsection
