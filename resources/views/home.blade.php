@extends('layouts.app')

@section('content')
    <script type="application/javascript">
        jQuery(document).ready( function () {
            $('#korisniciTable').DataTable();
        } );
        $('body').on('shown.bs.modal', '.modal', function () {
            $('input:visible:enabled:first', this).focus();
        })

        function unesiStanje(idKorisnik) {
            var stanje = $('#stanje_' + idKorisnik).val();

            $.ajax({
                type: "POST",
                url: '{{ route('unesi-stanje') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    idKorisnik: idKorisnik,
                    stanje: stanje
                },
                success: function(result) {
                    $('#modal_'+idKorisnik).modal('hide');
                    $('#stanjePrikaz_'+idKorisnik).empty();
                    $('#stanjePrikaz_'+idKorisnik).append(stanje);
                    if (result == 1) {
                        $('#redPrikaz_'+idKorisnik).css('background-color', '#99ff99');
                    } else {
                        $('#redPrikaz_'+idKorisnik).css('background-color', '#ffffff');
                    }
                },
                error: function(result) {
                    console.log(result);
                }
            });
        }
    </script>

<div class="container-fluid">
    <div class="col-md-8 offset-md-2">
        <div class="col-12 text-center">
            <h5>{{ __('Месец: ' . date('m.Y.')) }} године</h5>
        </div>
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <table id="korisniciTable" class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Име и презиме / бр. водомера</th>
                        <th class="text-center bg-light">П. ст.</th>
                        <th class="text-center bg-light">Стање</th>
                        <th class="text-center">Измена</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($korisnici as $i=>$korisnik)
                        @if ($korisnik['stanje'])
                            <tr id="redPrikaz_{{ $korisnik['id'] }}" style="background-color: #99ff99">
                        @else
                            @if($korisnik['pausalac'])
                                <tr id="redPrikaz_{{ $korisnik['id'] }}" style="background-color: #A9A9A9">
                            @else
                                <tr id="redPrikaz_{{ $korisnik['id'] }}">
                            @endif
                        @endif
                        <td scope="row" class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $korisnik['ime'] . ' ' . $korisnik['prezime'] }} <span style="display:none">{{ $korisnik['latinica'] }}</span><br/><small>{{ $korisnik['broj_vodomera'] }}</small></td>
                            <td class="text-center">{{ $korisnik['prethodno_stanje'] }}</td>
                            @if($korisnik['pausalac'])
                                <td class="text-center" id="stanjePrikaz_{{ $korisnik['id'] }}">{{ $korisnik['pausalac_kubika'] }}m3</td>
                            @else
                                <td class="text-center" id="stanjePrikaz_{{ $korisnik['id'] }}">{{ $korisnik['stanje'] }}</td>
                            @endif
                            <td class="text-center">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-lg btn-primary" data-toggle="modal" data-target="#modal_{{ $korisnik['id'] }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="modal_{{ $korisnik['id'] }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    {{ $korisnik['ime'] . " " . $korisnik['prezime'] }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div> <!-- end of .modal-header -->
                                            <div class="modal-body text-left">
                                                <input type="hidden" id="korisnik_{{ $korisnik['id'] }}" name="korisnik_{{ $korisnik['id'] }}" value="{{ $korisnik['id'] }}" autofocus>
                                                <div class="form-group text-center">
                                                    <label class="col-form-label col-form-label-lg" for="stanje">Стање:</label>
                                                    <input id="stanje_{{ $korisnik['id'] }}" class="form-control form-control-lg text-center" maxlength="4" type="tel" pattern="\d*">
                                                </div>
                                                <p><strong>ЈМБГ:</strong> {{ $korisnik['jmbg'] }}</p>
                                                <p><strong>Шифра објекта:</strong> {{ $korisnik['sifra_objekta'] }}</p>
                                                <p><strong>Број водомера:</strong> {{ $korisnik['broj_vodomera'] }}</p>
                                            </div> <!-- end of .modal-body -->
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Одустани</button>
                                                <button type="button" class="btn btn-primary" onclick="unesiStanje({{ $korisnik['id'] }})">Сачувај</button>
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

@endsection
