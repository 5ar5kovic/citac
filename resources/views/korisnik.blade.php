@extends('layouts.app')

@section('content')
    <div class="jumbotron">
        <div class="container">
            <h1 class="display-5 text-center">{{ $korisnik['ime'] . ' ' . $korisnik['prezime'] }}</h1>
            <br/>
            <strong>ЈМБГ: </strong>{{ $korisnik['jmbg'] }}
            <br>
            <strong>Шифра објекта: </strong>{{ $korisnik['sifra_objekta'] }}
            <br>
            <strong>Број водомера: </strong>{{ $korisnik['broj_vodomera'] }}
            <br>
            <strong>Број чланова домаћинства: </strong>{{ $korisnik['broj_clanova_domacinstva'] }}
            <br>
            <strong>Паушалац: </strong>
            @if ($korisnik['pausalac'] == 1)
                ДА
            <br>
            <strong>Паушално троши м3: </strong>{{ $korisnik['pausalac_kubika'] }}
            @else
                НЕ
            @endif
            <br>
            @if (Auth::id() == 1)
                <div class="text-right">
                    <a href="{{ route('izmena-podataka', ['id'=>$korisnik['id']]) }}" class="btn btn-sm btn-primary">Измени податке корисника</a>
                </div>
            @endif
        </div>
    </div>
    <div class="container-fluid">
        <div class="col-md-8 offset-md-2">

            <table class="table table-hover table-dark text-center">
                <thead>
                    <tr>
                        <th scope="col">Месец</th>
                        <th scope="col">Стање водомера</th>
                        <th scope="col">Потрошено m3</th>
                        <th scope="col">Рачун</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($potrosnje as $i=>$potrosnja)
                        <tr>
                            <th scope="row">
                                {{ $potrosnja['mesec'] . '.' . $potrosnja['godina'] }}
                            </th>
                            <td>{{ $potrosnja['stanje'] }}</td>
                            <td>
                                {{ $potrosnja['potroseno'] }}
                            </td>
                            <td>
                                @php
                                    if ($potrosnja['pausalac']) {
                                        echo '<span class="badge badge-danger">Паушалац</span>';
                                    }
                                @endphp
                                {{ number_format($potrosnja['racun'], 2, ',' , '.') . " динара" }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
