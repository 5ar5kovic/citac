@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="col-md-8 offset-md-2">
        <div class="d-flex align-items-center p-3 my-3 text-white-50 rounded shadow-sm bg-secondary">
            <div class="lh-100">
                <h5 class="mb-0 text-white lh-100">Месец: {{ $mesec . "." . $godina . "." }}</h5>
            </div>
        </div>
        <div class="d-flex align-items-center p-3 my-3 text-white-50 rounded shadow-sm bg-primary">
            <div class="lh-100">
                <h6 class="mb-0 text-white lh-100">Укупно фактурисано: {{ number_format($ukupno, 2, ',', '.') }} динара, потрошено кубика {{ $kubika }}</h6>
            </div>
        </div>
        <div class="my-3 p-3 bg-white rounded shadow-sm">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Име и презиме / Назив</th>
                        <th scope="col">Бр. чланова</th>
                        <th scope="col">Потрошио</th>
                        <th scope="col">Рачун</th>
                    </tr>
                </thead>
                <tbody>
                    @php $br = 1 @endphp
                    @foreach($rezultat as $rez)
                        <tr>
                            <th scope="row">{{ $br++ }}</th>
                            <td>{{ $rez['ime'] }}</td>
                            <td>{{ $rez['broj_clanova_domacinstva'] }}</td>
                            <td>{{ $rez['potroseno'] }} м3</td>
                            <td>{{ number_format($rez['racun'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection