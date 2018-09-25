@extends('layout')

@section('contenido')
    <h1>Categorías</h1>
    <div class="form form-group">
        <h4>Categoría</h4>
    <select name="categoria_id" id="categoria_id">
        <option value="">Selecciona...</option>
        @foreach($categorias as $categoria)
            @empty($categoria->categoria_id )
                <option value="{{$categoria->id}}">{{ $categoria->name }}</option>
            @endempty

        @endforeach
    </select>
        <button class="btn-primary" onclick="showNewCategoria()">+</button>
        <div id="newCategoria" class="form-group">
        <label for="categoría">Nombre
            <input type="text" name="categoría" id="categoría" required>
            <button onclick="buttonAgregarCategoria()" type="submit">Agregar</button>
            </label>
        </div>
        <h4>Subcategoría</h4>
        <select id="subcategoría"name="subcategoria">
            <option>Selecciona...</option>
        </select>

        <button class="btn-primary" onclick="showNewSubcategoria()">+</button>
        <div id="newSubcategoria" class="form-group">
            <label for="nombreSubcategoría">Nombre
                <input type="text" name="nombreSubcategoría" id="inputSubcategoría" required>
                <button onclick="buttonAgregarSubcategoria()" type="submit">Agregar</button>
            </label>
        </div>

    </div>
    <table class="table table-sm" id="mitablita">
        <thead>
        <tr>
            <th>Id</th>
            <th>Artículo</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <br>

    @stop
@section('scripts')
    <script>
        //se ejecuta al cargar la página
        $(function () {
            $("#newSubcategoria").hide()
            $("#newCategoria").hide()

            //si el select de categorías cambia
            $("#categoria_id").change(function () {
                //Siempre que se selecciona una nueva categoría se vacían las subcategorías
                $("#subcategoría").empty()
                var html = document.createElement("option");
                html.innerHTML = "Selecciona..."
                $("#subcategoría").append(html)
                //se borra la tabla de artículos
                $('#mitablita tbody').empty();

                if($(this).val() !== ''){
                    //Si no es nulo obtenemos todas las subcategorías de la opción seleccionada
                    //Utiliza ArticlesController@getCategoria
                    $.post("{{route('getCategoria')}}", {idcategoria: $(this).val()}, function (result) {
                        for (i = 0; i < result.data.length; i++) {
                            //Agrega todas las subcategorías al select de subcategorías
                            var html = document.createElement("option");
                            html.setAttribute('value',result.data[i].id);
                            html.innerHTML = result.data[i].name
                            $("#subcategoría").append(html)

                        }
                    });
                }
            })
            $("#subcategoría").change(function () {
                //Si se selecciona una subcategoría se obtienen los articulos de la seleccionada
                $.post("{{route('getArticulos')}}", {idcategoria: $(this).val()}, function (result) {
                    //si no tiene artículos
                    if(result.data.length === 0){
                        var mensaje = document.createElement("p");
                        mensaje.innerHTML = "No tienes artículos"
                        $('#mitablita tbody').empty();
                    }else{
                        //si tiene articulos los agrega en una tabla
                        for (i = 0; i < result.data.length; i++) {
                            html = `<tr>
                                <td>${result.data[i].id}</td>
                                <td>${result.data[i].nombre}</td>
                            </tr>`

                            $('#mitablita tbody').append(html);
                        }

                    }


                });


            })
        })

        var showNewCategoria = function () {
            //Si el botón de nueva categoría (+) se selecciona
        $("#newCategoria").show();
        }
        var showNewSubcategoria = function () {
            //Si el botón de nueva categoría (+) se selecciona
            $("#newSubcategoria").show();
        }

        //Si Agregar categoria se clickea
        var buttonAgregarCategoria = function(){
            if($("#categoría").val() === ""){
            alert("Complete el campo")
            }else{
                //Lo guarda utilizando el ArticlesController@storeCategoria
            $.post("{{route('storeCategoria')}}", {namecategoria: $("#categoría").val()}, function (result) {

            });
            //esconde el formulario de agregar
            $("#newCategoria").hide()
            //Agrega la nueva categoría al select
                var option = document.createElement("option");
                option.innerHTML = $("#categoría").val();
                $("#categoria_id").append(option)
            }
            //Vacía el input del formulario
            $("#categoría").val('')
        }
        var buttonAgregarSubcategoria = function () {

            //Verifica que se halla seleccionado categoría
            if($("#categoria_id").val() === ""){
                alert("Selecciona una categoría")
            }else{
                //Envia el id de la categoria seleccionada y el nombre de la nueva subcategoria
                $.post("{{route('storeSubcategoria')}}", {idcategoria: $("#categoria_id").val(), subcategoria: $("#inputSubcategoría").val() }, function (result) {

                });
                //esconde el formulario de agregar
                $("#newSubcategoria").hide()
                //Agrega la nueva categoría al select
                var option = document.createElement("option");
                option.innerHTML = $("#inputSubcategoría").val();
                $("#subcategoría").append(option)
            }
            //Vacía el input del formulario
            $("#inputSubcategoría").val('')
            }

    </script>
    @stop