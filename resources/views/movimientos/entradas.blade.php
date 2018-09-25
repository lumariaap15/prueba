@extends('layout')

@section('contenido')
<h1 id="title">Registrar nueva entrada</h1>
@include('movimientos.form')
@stop
@section('scripts')
    @include('movimientos.scripts')
@stop