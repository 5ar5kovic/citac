@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="col-md-8 offset-md-2">
            <div class="d-flex align-items-center p-3 my-3 text-white-50 rounded shadow-sm bg-primary">
                <div class="lh-100">
                    <h6 class="mb-0 text-white lh-100">Очекивани приход</h6>
                </div>
            </div>
            <div class="my-3 p-3 bg-white rounded shadow-sm">
                <form action="{{ route('ocekivani-prihod-rezultat') }}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mesec">Месец</label>
                            <select class="form-control" id="mesec" name="mesec">
                                <option value="1" {{ $trenutniMesec == 1 ? "selected" : "" }}>Јануар</option>
                                <option value="2" {{ $trenutniMesec == 2 ? "selected" : "" }}>Фебруар</option>
                                <option value="3" {{ $trenutniMesec == 3 ? "selected" : "" }}>Март</option>
                                <option value="4" {{ $trenutniMesec == 4 ? "selected" : "" }}>Април</option>
                                <option value="5" {{ $trenutniMesec == 5 ? "selected" : "" }}>Мај</option>
                                <option value="6" {{ $trenutniMesec == 6 ? "selected" : "" }}>Јун</option>
                                <option value="7" {{ $trenutniMesec == 7 ? "selected" : "" }}>Јул</option>
                                <option value="8" {{ $trenutniMesec == 8 ? "selected" : "" }}>Август</option>
                                <option value="9" {{ $trenutniMesec == 9 ? "selected" : "" }}>Септембар</option>
                                <option value="10" {{ $trenutniMesec == 10 ? "selected" : "" }}>Октобар</option>
                                <option value="11" {{ $trenutniMesec == 11 ? "selected" : "" }}>Новембар</option>
                                <option value="12" {{ $trenutniMesec == 12 ? "selected" : "" }}>Децембар</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="godina">Година</label>
                            <select class="form-control" id="godina" name="godina">
                                <option value="2021" {{ $trenutnaGodina == 2021 ? "selected" : "" }}>2021</option>
                                <option value="2020" {{ $trenutnaGodina == 2020 ? "selected" : "" }}>2020</option>
                                <option value="2019" {{ $trenutnaGodina == 2019 ? "selected" : "" }}>2019</option>
                            </select> </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Прикажи</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
