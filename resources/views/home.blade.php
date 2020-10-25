@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Месец: ' . date('d.m.Y H:i:s')) }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Име и презиме</th>
                            <th scope="col">Шифра објекта</th>
                            <th scope="col">Број водомера</th>
                            <th scope="col">Стање</th>
                            <th scope="col">Унос/измена</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($korisnici as $i=>$korisnik)
                        <tr>
                            <th scope="row">{{ $i + 1 }}</th>
                            <td>{{ $korisnik['ime'] . ' ' . $korisnik['prezime'] }}</td>
                            <td>{{ $korisnik['sifra_objekta'] }}</td>
                            <td>{{ $korisnik['broj_vodomera'] }}</td>
                            <td>{{ $korisnik['jmbg'] }}</td>
                            <td>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_{{ $korisnik['id'] }}">
                                    Launch demo modal
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="modal_{{ $korisnik['id'] }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div> <!-- end of .modal-header -->
                                            <div class="modal-body">
                                                {{ $korisnik['id'] }}
                                            </div> <!-- end of .modal-body -->
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary">Save changes</button>
                                            </div> <!-- end of .modal-footer -->
                                        </div> <!-- end of .modal-content -->
                                    </div> <!-- end of .modal-dialog -->
                                </div> <!-- end of .modal -->
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
