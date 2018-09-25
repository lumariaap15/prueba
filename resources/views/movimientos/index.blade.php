@extends('layout')

@section('contenido')
    <h1>Movimientos</h1>
    @if(count($detalles)===0)
        @if(count($articles)===0)
            <p>No tienes ningún artículo</p>
            <a href="{{ route('articles.create') }}" role="button" class="btn btn-primary">Crea un nuevo artículo</a>
            @else
            <h1>Inventario Inicial</h1>
            @include('movimientos.inventarioInicial')
        @endif
        @else

    <a href="{{ route('movimientos.create',["tipo"=>"entradas"]) }}" role="button" class="btn btn-success">Nueva Entrada</a>
    <a href="{{ route('movimientos.create',["tipo"=>"salidas"]) }}" role="button" class="btn btn-danger">Nueva Salida</a>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Descripción</th>
            <th>Artículos</th>
        </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $movimiento)
                <tr>
                    <td>{{$movimiento->updated_at}}</td>
                    <td>{{$movimiento->description}}</td>
                    <td><a href="{{ route('movimientos.show',$movimiento->id) }}">Ver</a></td>
                </tr>
                @endforeach
        </tbody>
    </table>
    @endif
@stop
@section('scripts')
    @include('movimientos.scripts')
@stop