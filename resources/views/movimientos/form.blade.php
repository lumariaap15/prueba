
<form action="{{ route('movimientos.store') }}" method="post" role="form" id="formulario" >
    <h3>Tipo de movimiento</h3>
    <select id="tipo" class="form-control" required>
        <option>Selecciona...</option>
        <option>Compra</option>
        <option>Devolución en Venta</option>
    </select>


    <div class="form-inline" id="devoluciónVenta">
       <h4>Fecha de Venta</h4>
        <select class="form-control" id="fechasVenta" required>
            <option value="">Selecciona...</option>
            @foreach($movimientos as $movimiento)
                @if($movimiento->description === 'Venta')
                <option value="{{$movimiento->id}}">{{ $movimiento->updated_at }}</option>
                @endif
                    @endforeach
        </select>


    </div>
    <div id="Compra">
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
        <button id="boton" type="submit" class="btn btn-primary" onclick="buttonClicked()">Agregar</button>
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
<button id="actualizar" type="button" class="btn btn-primary">Actualizar</button>
<button id="submit" type="button" class="btn btn-primary">Submit</button>
<h1>cambio deiby</h1>



