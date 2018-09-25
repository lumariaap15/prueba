@extends('layout')

@section('contenido')
    @if(count($categorias)===0)
        <p>No tienes ninguna categoría</p>
        <a href="{{ route('articles.categorias')}}" class="btn btn-primary">Crear nueva categoría</a>
        @else
    <h1>Agregar Nuevo Artículo</h1>
    <form action="{{ route('articles.store') }}" method="post" class="form-control-lg">
        {!! csrf_field() !!}
        <label for="nombre">Nombre
            <input type="text" name="nombre" id="nombre" value="" required>
        </label>
        <h3>Categoría</h3>
        <select name="categoria_id" id="categoria_id">
            <option value="null">Selecciona...</option>
            @foreach($categorias as $categoria)
                @empty($categoria->categoria_id )
                    <option value="{{$categoria->id}}">{{ $categoria->name }}</option>
                @endempty

            @endforeach
        </select>
        <div>
            <h3>Subcategoría</h3>
            <select id="subcategoría"name="subcategoria">
                <option>Selecciona...</option>
            </select>
        </div>
        <hr>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
    @endif
@stop
@section('scripts')
    <script>
        $(function () {

            $("#categoria_id").change(function () {

                    if($(this).val() === 'null'){
                    $("#subcategoría").empty()
                        var html = document.createElement("option");
                        html.innerHTML = "Selecciona..."
                        $("#subcategoría").append(html)

                    }else{
                    $.post("{{route('getCategoria')}}", {idcategoria: $(this).val()}, function (result) {
                        for (i = 0; i < result.data.length; i++) {
                                var html = document.createElement("option");
                            html.setAttribute('value',result.data[i].id);
                                html.innerHTML = result.data[i].name
                                $("#subcategoría").append(html)

                        }
                    });
            }
            })
        })

    </script>
@stop