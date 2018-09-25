<?php

namespace App\Http\Controllers;

use App\Article;
use App\Categoria;
use DB;
use Illuminate\Http\Request;


class ArticlesController extends Controller
{
    /**
     * Muestra una lista de artículos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $articles = Article::with('categoria','movimiento')
            //Busca todos los detalles donde el movimiento es de tipo entrada
            ->withCount(['detalle as entrada_cantidad' => function($query){
                $query->whereHas('movimiento',function ($query){
                   $query->where('tipo','Entrada');
                });
                //Suma la cantidad y lo guarda como entrada_cantidad
                $query->select(DB::raw('sum(cantidad)'));
            }])
            ->withCount(['detalle as salida_cantidad' => function($query){
                //Busca todos los detalles donde el movimiento es de tipo salida
                $query->whereHas('movimiento',function ($query){
                    $query->where('tipo','Salida');
                });
                //Suma la cantidad y lo guarda como salida_cantidad
                $query->select(DB::raw('sum(cantidad)'));
            }])
            ->withCount(['detalle as sum_costos_entrada' => function($query){
                //Busca todos los detalles donde el movimiento es de tipo entrada
                $query->whereHas('movimiento',function ($query){
                    $query->where('tipo','Entrada');
                });
                //Suma el costo por la cantidad de todas las filas seleccionadas
                // y lo guarda como sum_costos_entrada
                $query->select(DB::raw('sum(costo * cantidad)'));
            }])
            ->withCount(['detalle as sum_costos_salida' => function($query){
                //Busca todos los detalles donde el movimiento es de tipo salida
                $query->whereHas('movimiento',function ($query){
                    $query->where('tipo','Salida');
                });
                //Suma el costo por la cantidad de todas las filas seleccionadas
                // y lo guarda como sum_costos_salida
                $query->select(DB::raw('sum(costo * cantidad)'));
            }])
            ->get();


        return view('articles.index',compact('articles'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Retorna la vista articles.create y le pasa todas las categorías
        $categorias = Categoria::all();
        return view('articles.create',compact('categorias'));
    }

    public function getCategoria(Request $request)
    {
        //Recibe idcategoría y retorna todas las subcategorías que le pertenecen
     $subcategorias = Categoria::query()->where('categoria_id', $request->idcategoria)->get();
     //Envía un arreglo con las subcategorías a la vista categorías
     return response()->json(['status'=>'ok',"data"=>$subcategorias],200);
    }
    public function getArticulos(Request $request)
    {
        //Recibe idcategoría y retorna todas los artículos que le pertenecen
        $articulos = Article::query()->where('categoria_id', $request->idcategoria)->get();
        return response()->json(['status'=>'ok',"data"=>$articulos],200);
    }

    //Se utiliza en la vista articles.categorias
    public function storeCategoria(Request $request)
    {
        //Recibe el nombre de la categoría y crea una nueva categoría
        $categoria = new Categoria();
        $categoria->name = $request->namecategoria;
        $categoria->save();
    }
    public function storeSubcategoria(Request $request)
    {
        //Recibe el nombre de la Subcategoría y el id de la categoría a la que pertenece
        //Crea subcategoría
        $categoria = new Categoria();
        $categoria->name = $request->subcategoria;
        $categoria->categoria_id = $request->idcategoria;
        $categoria->save();
    }
    /**
     * Guarda un nuevo artículo en la base de datos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $article = new Article();
        $article->nombre = $request->nombre;
        $article->categoria_id = $request->subcategoria;
       $article->save();

        return redirect()->route('articles.index');
    }

    /**
     * Muestra un artículo específico accediendo con su id
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //se consulta el artículo
        $article = Article::query()->findOrFail($id);
        $cantidadTotal = 0;
        $costoTotal = 0;
        //Esta vista muestra la tabla Kardex de cada artículo
        return view('articles.show',compact('article','cantidadTotal','costoTotal'));
    }

    /**
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function categorias()
    {
        //Todas las categorías con sus artículos
        $categorias = Categoria::with('article')->get();
       return view('articles.categorias',compact('categorias'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
