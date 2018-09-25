@extends('layout')

@section('contenido')
    <h1>Inventario Inicial</h1>
    <form action="{{ route('movimientos.store') }}" method="post" role="form" id="formulario">

        <div class="form-inline" id="Compra">
            <h3>Seleccionar Artículo</h3>

            <select class="form-control" id="article" required>
                <option value="">Selecciona...</option>
                @foreach($articles as $article)
                    <option value="{{$article->id}}">{{ $article->nombre }}</option>
                @endforeach
            </select>

            <label for="cantidad">Cantidad
                <input class="form-control" type="number" name="cantidad" id="cantidad" value="" required>
            </label>
            <label for="valor">Valor
                <input class="form-control" type="number" name="valor" id="valor" value="" required>
            </label>
            <button id="boton" type="submit" class="btn btn-primary" onclick="">Agregar</button>
        </div>
    </form>
    <hr>
    <table class="table table-hover" id="mitablita">
        <thead>
        <tr>
            <th>Artículo</th>
            <th>Cantidad</th>
            <th>Costo Unitario</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <button id="submit" type="button" class="btn btn-primary">Submit</button>
@stop
@section('scripts')
        <script>
            var data = [];
            var valor = 0;
            $(function () {

                //Hace que el formulario ejecute la función buttonClicked()
                $("#formulario").submit(function (e) {
                    e.preventDefault();
                    buttonClicked();
                });

                $('#submit').click(function () {
                    $.post('{{ route('movimientos.store') }}', {tipo: 'Entrada', data: data,description: 'Compra'}, function (result) {
                        for (var i = 0; i < data.length; i++) {
                            $("#" + data[i].uuid + ' td').remove()
                        }
                        data = []
                        alert('Tu movimiento fue registrado')

                    })
                })

            })


            var buttonClicked = function () {

                if ($("#article").val() === '') {
                    alert('Debes seleccionar un artículo');
                } else {
                    if ($('#cantidad').val() <= 0 || $('#valor').val() <= 0) {
                        alert('Ingrese un valor válido');
                    } else {
                        var existe = false;
                        var position = 0;
                        for (i = 0; i < data.length; i++) {
                            if (data[i].articulo === $('#article').val()) {
                                position = i;
                                existe = true;
                                break;
                            }
                        }
                        if (!existe) {
                            var uuid= guid();
                            var item = {
                                uuid: uuid,
                                articulo: $('#article').val(),
                                cantidad: $('#cantidad').val(),
                                articuloTex: $('#article option:selected').text(),
                                valor: $('#valor').val(),
                                opciones: `<button class="btn btn-danger" type="button" onclick="eliminar('${uuid}')">Eliminar</button>\
`
                            };

                            renderGrid(item);
                        } else {
                            data[position].cantidad = parseInt(data[position].cantidad) + parseInt($("#cantidad").val())
                            data[position].valor = parseInt(data[position].valor) + parseInt($("#valor").val())
                            $("#" + data[position].uuid + ' td').each(function (key, value) {
                                if (key == 1) {
                                    $(this).html(data[position].cantidad);
                                }
                                if (key === 2) {
                                    $(this).html(data[position].valor)
                                }
                            })
                        }

                    }

                }

            }


            function renderGrid(item) {
                html = `<tr id="${item.uuid}">
                        <td>${item.articuloTex}</td>
                        <td>${item.cantidad}</td>
                        <td>${item.valor}</td>
                        <td>
                           ${item.opciones}
                        </td>
                    </tr>`

                $('#mitablita tbody').append(html);
                data.push(item);

            }

            function eliminar(uuid) {
                for (i = 0; i < data.length; i++) {
                    if (data[i].uuid === uuid) {
                        data.splice(i, 1);
                        $("#" + uuid).remove();
                        break;
                    }
                }
            }


            function guid() {
                function s4() {
                    return Math.floor((1 + Math.random()) * 0x10000)
                        .toString(16)
                        .substring(1);
                }

                return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
            }



        </script>

@stop