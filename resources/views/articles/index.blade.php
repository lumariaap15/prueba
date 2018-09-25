@extends('layout')

@section('contenido')

<h1>Artículos</h1><a href="{{ route('articles.create') }}" role="button" class="btn float-right">Nuevo Artículo</a>
@if(count($articles) === 0)
<p>No tienes artículos para mostrar</p>
    <a href="{{ route('articles.create') }}" role="button" class="btn btn-primary">Crea un nuevo artículo</a>
@else
<table class="table table-hover">
    <thead>
    <tr>
        <th>Id</th>
        <th>Nombre</th>
        <th>Categoría</th>
        <th>Unidades</th>
        <th>Costo Unitario</th>
    </tr>
    </thead>
    <tbody>

        @foreach($articles as $article)
        <tr>
            <td>{{ $article->id }}</td>
            <td><a href="{{ route('articles.show', $article->id) }}">{{ $article->nombre }}</a></td>
            <td>{{  $article->categoria->name }}</td>
            <td>{{ $article->entrada_cantidad - $article->salida_cantidad}}</td>
            @if(($article->entrada_cantidad - $article->salida_cantidad) > 0)
                <td>{{ ($article->sum_costos_entrada - $article->sum_costos_salida)  / ($article->entrada_cantidad - $article->salida_cantidad)  }}</td>
                @else
                <td>0</td>
            @endif
        </tr>
        @endforeach
        @endif

    </tbody>
</table>
@stop