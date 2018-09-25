
    <script>
        var data = [];
        var valor = 0;
        $(function () {
            //Esconde los formulario de compra y devolución en Venta
            $("#actualizar").hide()
            $("#submit").hide()
            $("#Compra").hide()
            $("#devoluciónVenta").hide()
            //Hace que el formulario ejecute la función buttonClicked()
            $("#formulario").submit(function (e) {
                e.preventDefault();
                buttonClicked();
            });
            //Se selecciona si es Compra o Devolución en Venta
            $("#tipo").change(function () {
                if ($("#tipo").val() === 'Compra') {
                    $("#Compra").show()
                    $("#devoluciónVenta").hide()
                    $("#submit").show()
                    $("#actualizar").hide()
                    $("#title").text('Registrar nueva Compra')
                } else {
                    $("#Compra").hide()
                    $("#devoluciónVenta").show()
                    $("#submit").hide()
                    $("#actualizar").show()
                    $("#title").text('Registrar nueva Devolución en Venta')
                }
            })

            //En el formulario de Devolución en Venta
            //Si la persona elige una fecha debemos obtener los artículos que se vendieron ese dia
            $("#fechasVenta").change(function () {
                data = []
                $('#mitablita tbody').empty();

                if ($("#fechasVenta").val() !== '') {
                    valor = 0;
                    //Obtener el los artículos de la venta y agregarlos al select
                    $.post('{{ route('getArticlesVenta') }}', {idmovimiento: $("#fechasVenta").val()}, function (result) {

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
            //Obtener el valor al que se vendió el artículo escogido
            $("#articlesVenta").change(function () {
                if ($("#articlesVenta").val() !== '') {
                    $.post('{{ route('getDetallesVenta') }}', {
                        idmovimiento: $("#fechasVenta").val(),
                        idarticle: $("#articlesVenta").val()
                    }, function (result) {
                        $("#costoVenta").val(result.costo)
                        $("#cantidadVenta").val(result.cantidad)
                    })
                }
            })


            $('#submit').click(function () {
                $.post('{{ route('movimientos.store') }}', {tipo: 'Entrada', data: data,description: 'Compra'}, function (result) {
                    for (var i = 0; i < data.length; i++) {
                        $("#" + data[i].uuid + ' td').remove()
                    }
                    data = []
                    alert('Tu movimiento fue registrado')

                })
            })
            $('#actualizar').click(function () {
                if($("#fechasVenta").val() === ''){
                    alert('Selecciona una fecha de Venta')
                }else{

                $.post('{{ route('movimientos.store') }}', {tipo: 'Entrada', data: data, description: 'Devolución en Venta '+$('#fechasVenta option:selected').text(), idmovimiento: $('#fechasVenta').val()} ,function (result) {
                    for (var i = 0; i < data.length; i++) {
                        $("#" + data[i].uuid + ' td').remove()
                    }
                    data = []
                    alert('Tu movimiento fue registrado')
                })
                }
            });
        })
            var actualizarUnidades = function (select, position) {
                data[position].cantidad = $(select).val();
            }
            var buttonClicked = function () {
                //var text = document.getElementById('article');
                if ($("#article").val() === '') {
                    alert('Debes seleccionar un artículo');
                }
                else {
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
                        var uuid = guid()
                        if (!existe) {
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
                            data.push(item);

                        }
                        else {
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
