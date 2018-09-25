<?php

namespace App\Http\Controllers;

use App\Article;
use App\Detalle;
use App\Http\Requests\CreateMovimientoRequest;
use App\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $movimientos = Movimiento::with('article')->get();
        $detalles = Detalle::all();
        $articles = Article::with('categoria')->get();
        $inventarioInicial = true;
        return view('movimientos.index',compact('movimientos','detalles','articles','inventarioInicial'));
    }

    /**
     * Muestra el formulario para crear un movimiento
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $tipo = $request->get('tipo');
        $articles = Article::with('categoria')->get();
        $movimientos = Movimiento::with('article','detalle')->get();
        //Puede mostrar el formulario de entradas o de salidas
        return view(('movimientos.' . $tipo), compact('articles','movimientos','inventarioInicial'));

    }

    //Esta función se usa para una devolución en venta, obtiene los detalles de una venta determinada
    public function getArticlesVenta(Request $request)
    {
        //devuelve los detalles del movimiento seleccionado

     $detalles = Detalle::query()
         ->where('movimiento_id',$request->idmovimiento)
         ->with('article')->get();
     //Trae todos los detalles de todas las devoluciones anteriores de esa venta
     $detallesDevolucion = Detalle::query()->whereHas('movimiento' ,function($query) use($request){
         $query->where('movimiento_id',$request->idmovimiento);
     })->get();

        for ($i=0;$i<$detalles->count();$i++)
        {
            //Para cada detalle verifica si existen devoluciones de esa venta, y le resta las cantidades
            foreach ($detallesDevolucion as $devolucion){
                if($detalles[$i]->article_id == $devolucion->article_id){
                    $detalles[$i]->cantidad -= $devolucion->cantidad;

                }
            }
        }
        //Envía los detalles con las nuevas cantidades que se pueden devolver
        return response()->json(['status'=>'ok',"detalles"=>$detalles],200);
        }

    public function getArticlesCompra(Request $request)
    {
        //devuelve los detalles del movimiento seleccionado

        $detalles = Detalle::query()
            ->where('movimiento_id',$request->idmovimiento)
            ->with('article')->get();
        //Trae todos los detalles de todas las devoluciones anteriores de esa compra
        $detallesDevolucion = Detalle::query()->whereHas('movimiento' ,function($query) use($request){
            $query->where('movimiento_id',$request->idmovimiento);
        })->get();

        for ($i=0;$i<$detalles->count();$i++)
        {
            //Para cada detalle verifica si existen devoluciones de esa compra, y le resta las cantidades
            foreach ($detallesDevolucion as $devolucion){
                if($detalles[$i]->article_id == $devolucion->article_id){
                    $detalles[$i]->cantidad -= $devolucion->cantidad;

                }
            }
        }


        //Envía los detalles con las nuevas cantidades que se pueden devolver
        return response()->json(['status'=>'ok',"detalles"=>$detalles],200);
    }

    public function getDetallesVenta(Request $request)
    {
        $detalle = Detalle::query()
            ->where('movimiento_id',$request->idmovimiento)
            ->where('article_id',$request->idarticle)->first();
        return response()->json(['status'=>'ok',"costo"=>$detalle->costo,"cantidad"=>$detalle->cantidad],200);
    }

    public function getValor(Request $request)
    {
        $article = Article::with('categoria')
            ->withCount(['detalle as entrada_cantidad' => function ($query) {
                $query->whereHas('movimiento', function ($query) {
                    $query->where('tipo', 'Entrada');
                });
                $query->select(DB::raw('sum(cantidad)'));
            }])
            ->withCount(['detalle as salida_cantidad' => function ($query) {
                $query->whereHas('movimiento', function ($query) {
                    $query->where('tipo', 'Salida');
                });
                $query->select(DB::raw('sum(cantidad)'));
            }])
            ->withCount(['detalle as sum_costos_entrada' => function ($query) {
                $query->whereHas('movimiento', function ($query) {
                    $query->where('tipo', 'Entrada');
                });
                $query->select(DB::raw('sum(costo * cantidad)'));
            }])
            ->withCount(['detalle as sum_costos_salida' => function ($query) {
                $query->whereHas('movimiento', function ($query) {
                    $query->where('tipo', 'Salida');
                });
                $query->select(DB::raw('sum(costo * cantidad)'));
            }])
            ->where('id', $request->idarticle)->first();
        $costounitario = ($article->sum_costos_entrada - $article->sum_costos_salida) / ($article->entrada_cantidad - $article->salida_cantidad);
        $cantidadTotal = $article->entrada_cantidad - $article->salida_cantidad;
        return response()->json(['status'=>'ok',"data"=>$costounitario,"cantidad"=>$cantidadTotal],200);
    }

    /**
     * Guarda un nuevo movimiento
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->data;

        if ($request->tipo === 'Entrada'){
            //Si el movimiento es entrada
            $movimiento = new Movimiento();
            $movimiento->tipo=$request->tipo;
            if($request->description === 'Compra')
            {
                $movimiento->description = 'Compra';
            }else{
                //Si es devolucion en Venta lo relaciona con el id de la venta
                $movimiento->description = $request->description;
                $movimiento->movimiento_id = $request->idmovimiento;
            }

            $movimiento->save();
            //Aqui guarda cada detalle en la pivot
            foreach ($data as $item){
            $movimiento->article()->attach($item['articulo'], ['cantidad'=>$item['cantidad'], 'costo'=>$item['valor']]);
            }
            return response()->json(['status'=>'ok'],200);
        }
        else{

            //Si el movimiento es tipo salida
            $movimiento = new Movimiento();
            $movimiento->tipo=$request->tipo;

            if($request->description === 'Venta')
            {
                $movimiento->description = 'Venta';
            }else{
                //Si es devolucion en Compra lo relaciona con el id de la compra
                $movimiento->description = $request->description;
                $movimiento->movimiento_id = $request->idmovimiento;
            }
            $movimiento->save();
            foreach ($data as $item){
                $movimiento->article()->attach($item['articulo'], ['cantidad'=>$item['cantidad'], 'costo'=>$item['valor']]);
            }
            return response()->json(['status'=>'ok'],200);
        }

        return redirect()->route('articles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movimiento = Movimiento::findOrFail($id);
        $detalles = Detalle::query()->where('movimiento_id',$movimiento->id)->get();

      return view('movimientos.show',compact('movimiento','detalles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        dd($id);
        $movimiento = Movimiento::findOrFail($id);
        $movimiento->delete();
    }
}
