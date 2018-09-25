@extends('layout')

@section('contenido')
<h1>Movimiento {{$movimiento->id}} - {{$movimiento->description}}</h1>
    <h3>{{ $movimiento->updated_at }}</h3>
    <table class="table table-light">
        <thead>
        <tr>
            <th>Artículo</th>
            <th>Cantidad</th>
            <th>Valor Unitario</th>
            <th>Valor Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($detalles as $detalle)
            <tr>
                <td>{{ $detalle->article->nombre }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>{{ $detalle->costo }}</td>
                <td>{{ $detalle->cantidad *  $detalle->costo}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a class="btn btn-primary" role="button" href="{{ route('movimientos.index') }}">Volver</a>
    @stop