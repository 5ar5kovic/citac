@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="col-md-8 offset-md-2">
            <form action="{{ route('napravi-izvestaj') }}">
                <div class="form-group">
                    <label for="mesec">Месец</label>
                    <select class="form-control" id="mesec" name="mesec">
                        <option value="1">Јануар</option>
                        <option value="2">Фебруар</option>
                        <option value="3">Март</option>
                        <option value="4">Април</option>
                        <option value="5">Мај</option>
                        <option value="6">Јун</option>
                        <option value="7">Јул</option>
                        <option value="8">Август</option>
                        <option value="9">Септембар</option>
                        <option value="10">Октобар</option>
                        <option value="11">Новембар</option>
                        <option value="12">Децембар</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="godina">Година</label>
                    <select class="form-control" id="godina" name="godina">
                        <option value="2021">2021</option>
                        <option value="2020">2020</option>
                        <option value="2019">2019</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Извештај</button>
            </form>
        </div>
    </div>

@endsection
