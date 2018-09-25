@extends('layout')

@section('contenido')
    <h1>Artículo {{ $article->nombre }}</h1>
   <a href="{{ route('articles.index') }}"> <-Volver</a>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Descripción</th>
            <th>Entradas</th>
            <th>Salidas</th>
            <th>Existencias</th>
            <th>Valor unitario</th>
        </tr>
        </thead>
        <tbody>
        @foreach($article->detalle as $detail)
        <tr>

                <td><a href="{{ route('movimientos.show', $detail->movimiento->id) }}">{{ $detail->movimiento->created_at }}</a></td>
                <td>{{ $detail->movimiento->description }}</td>

            <td>
                @if($detail->movimiento->tipo === 'Entrada')
                    <p>CANTIDAD: {{$detail->cantidad}}</p>
                    <p>COSTO: {{$detail->costo}}</p>
                    <p>COSTO TOTAL: {{$detail->cantidad * $detail->costo}}</p>
                @endif
            </td>
            <td>
                @if($detail->movimiento->tipo === 'Salida')
                    <p>CANTIDAD: {{$detail->cantidad}}</p>
                    <p>COSTO: {{$valorUnit}}</p>
                    <p>COSTO TOTAL: {{$valorUnit  * $detail->cantidad}}</p>
                @endif
            </td>
            <td>
                @if($detail->movimiento->tipo === 'Entrada')
                   <p>CANTIDAD:{{ $cantidadTotal = $cantidadTotal + $detail->cantidad}}</p>
                    <p>COSTO TOTAL:{{ $costoTotal = $costoTotal + $detail->cantidad * $detail->costo}}</p>
                @else
                    <p>CANTIDAD:{{ $cantidadTotal = $cantidadTotal - $detail->cantidad}}</p>
                    <p>COSTO TOTAL:{{ $costoTotal = $costoTotal - $valorUnit  * $detail->cantidad}}</p>
                @endif
            </td>
            <td>{{ $valorUnit = $costoTotal/$cantidadTotal }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
@stop