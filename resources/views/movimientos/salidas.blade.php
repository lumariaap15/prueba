@extends('layout')

@section('contenido')
    <h1 id="title">Registrar nueva salida</h1>


    <form action="{{ route('movimientos.store') }}" method="post" role="form" id="formulario">


        <h3>Tipo de movimiento</h3>
        <select id="tipo" class="form-control" required>
            <option>Selecciona...</option>
            <option>Venta</option>
            <option>Devolución en Compra</option>
        </select>

        <div id="devoluciónEnCompra">
            <h4>Fecha de Compra</h4>
            <select  id="fechasCompra"  class="form-control" required>
                <option value="">Selecciona...</option>
                @foreach($movimientos as $movimiento)
                    @if($movimiento->description === 'Compra')
                        <option value="{{$movimiento->id}}">{{ $movimiento->updated_at }}</option>
                    @endif
                @endforeach
            </select>
        </div>



        <div id="Venta">
        <label for="existencias">Existencias
            <input class="form-control" type="number" name="existencias" id="existencias" value="" disabled>
        </label>
        <h3>Seleccionar Artículo</h3>

        <div class="form-inline">
            <select class="form-control" id="article" name="article" required>
                <option value="">Selecciona...</option>
                @foreach($articles as $article)
                    <option value="{{$article->id}}">{{ $article->nombre }}</option>
                @endforeach
            </select>

            <label for="cantidad">Cantidad
                <input class="form-control" type="number" name="cantidad" id="cantidad" value="" required>
            </label>
            <div id="campoValor">
                <label for="valor">Valor
                    <input class="form-control" type="number" name="valor" id="valor" disabled>
                </label></div>
            <button id="prueba" type="submit" class="btn btn-primary">Agregar</button>
        </div>
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
    <button id="submit" type="submit" class="btn btn-primary">Guardar</button>
    <button id="actualizar" type="submit" class="btn btn-primary">Actualizar</button>

@stop
@section('scripts')
    <script>
        var cantidadTot = 0;
        var data = [];
        $(function () {

            $("#actualizar").hide()
            $("#submit").hide()
            $("#Venta").hide()
            $("#devoluciónEnCompra").hide()
            //Si el tipo cambia escondemos o mostramos los divs de Venta o Devolución
            $("#tipo").change(function () {
                $('#mitablita tbody').empty();
                if ($("#tipo").val() === 'Venta') {
                    $("#Venta").show()
                    $("#devoluciónEnCompra").hide()
                    $("#title").text('Registrar nueva Venta')
                    $("#actualizar").hide()
                    $("#submit").show()
                } else {
                    $("#Venta").hide()
                    $("#devoluciónEnCompra").show()
                    $("#actualizar").show()
                    $("#submit").hide()
                    $("#title").text('Registrar nueva Devolución en Compra')
                }
            })





            //Hace que el formulario ejecute la función buttonClicked()
            $("#prueba").click(function (e) {
                e.preventDefault();
                buttonClicked();
            });

            //En el formulario de Devolución en Compra
            //Si la persona elige una fecha debemos obtener los artículos que se compra ese dia
            $("#fechasCompra").change(function () {
                data = []
                $('#mitablita tbody').empty();

                if ($("#fechasCompra").val() !== '') {
                    valor = 0;
                    //Obtener el los artículos de la compra y agregarlos al select
                    $.post('{{ route('getArticlesCompra') }}', {idmovimiento: $("#fechasCompra").val()}, function (result) {

                        for (i = 0; i < result.detalles.length; i++) {

                            select = "<select onclick='actualizarUnidades(this," + i + ")' class='form-control-sm' id='selectCantidad'>";
                            for (j = 0; j <= result.detalles[i].cantidad; j++) {
                                select += "<option>" + j + "</option>";
                            }
                            select += "</select>";
                            var item = {
                                uuid: result.detalles[i].id,
                                articulo: result.detalles[i].article.id,
                                cantidad: result.detalles[i].cantidad,
                                articuloTex: result.detalles[i].article.nombre,
                                valor: result.detalles[i].costo,
                                opciones: select
                            };

                            html = renderGrid(item);
                            item.cantidad = 0;
                            data.push(item);


                        }

                    })
                }
            })
            //Selectores del formulario de Venta
            $("#article").change(function () {
                $.post("{{route('getValor')}}", {idarticle: $(this).val()}, function (result) {
                    $("#valor").val(result.data);
                    cantidadTot = result.cantidad;
                    $('#existencias').val(cantidadTot)
                });
            })
            $('#submit').click(function () {
                $.post('{{ route('movimientos.store') }}', {tipo: 'Salida', data: data, description: 'Venta'},function (result){
                    for(var i=0; i<data.length; i++){
                        $("#"+data[i].uuid+' td').remove()
                    }
                    data= []


                })
            })
            $('#actualizar').click(function () {
                if($("#fechasCompra").val() === ''){
                    alert('Selecciona una fecha de Compra')
                }else{

                    $.post('{{ route('movimientos.store') }}', {tipo: 'Salida', data: data, description: 'Devolución en Compra '+$('#fechasCompra option:selected').text(), idmovimiento: $('#fechasCompra').val()} ,function (result) {
                        for(var i=0; i<data.length; i++){
                            $("#"+data[i].uuid+' td').remove()
                        }
                        data= []
                    })
                }
            });
        });
        //Registra en la cantidad la cantidad devuelta
        var actualizarUnidades = function (select, position) {
            data[position].cantidad = $(select).val()
        }

        //Botón Agregar
        var buttonClicked = function () {
            if($('#cantidad').val()<=0 || $('#valor').val()<=0 || $('#cantidad').val()>cantidadTot){
                alert('Ingrese una cantidad válida');
            }
            else{

                var existe = false;
                var position = 0;
                for (i = 0; i < data.length; i++) {
                    if (data[i].articulo === $('#article').val()) {
                        position = i;
                        existe = true;
                        break;
                    }
                }
                if(!existe){
                    var uuid = guid()
                    var item = {
                        uuid: uuid,
                        articulo: $('#article').val(),
                        articuloTex: $('#article option:selected').text(),
                        cantidad: $('#cantidad').val(),
                        valor: $('#valor').val(),
                        opciones: `<button class="btn btn-danger" type="button" onclick="eliminar('${uuid}')">Eliminar</button>`
                    };
                    renderGrid(item);

                    data.push(item);
                } else{
                    data[position].cantidad = parseInt(data[position].cantidad) + parseInt($("#cantidad").val())
                    $("#"+data[position].uuid+' td').each(function (key, value) {
                        if(key==1){
                            $(this).html(data[position].cantidad);
                        }
                    })
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


        }

        //if(revisar(item.articulo) === false ){
        //};

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