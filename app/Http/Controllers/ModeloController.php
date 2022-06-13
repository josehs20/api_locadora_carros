<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Repositories\ModeloRespository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\This;

class ModeloController extends Controller
{

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $modeloRepository = new ModeloRespository($this->modelo);
     
        if ($request->has('atributos_marca')) {
            $atributos_marca = 'marca:id,'. $request->atributos_marca;
            $modeloRepository->selectAtibutosRegistrosRelacionados($atributos_marca);
        } else {
            $modeloRepository->selectAtibutosRegistrosRelacionados('marca');
        }

        if ($request->has('filtro')) {
            $modeloRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {

            $modeloRepository->selectAtributos($request->atributos);
        }


        // $modelos = [];

        // if ($request->has('atributos_marca')) {

        //     $atributos_marca = $request->atributos_marca;
        //     $modelos = $this->modelo->with('marca:id,'.$atributos_marca);
        // } else {
        //     $modelos = $this->modelo->with('marca');
        // }

        // if ($request->has('filtro')) {

        //     $filtros = explode(';', $request->filtro);
        //     foreach ($filtros as $key => $condicao) {
        //         $c = explode(':', $condicao);

        //         $modelos = $modelos->where($c[0], $c[1], $c[2]);
        //     }
        // }

        // if ($request->has('atributos')) {
        //     $atributos = $request->atributos;

        //     $modelos = $modelos->selectRaw($atributos)->get();
        // } else {
        //     $modelos = $modelos->get();
        // }


        return response()->json($modeloRepository->getResultado(), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());

        $imagem = $request->file('imagem');
        $img_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $img_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);
        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id);

        if ($modelo === null) {
            return response()->json(['error' => 'Nenhum registro encontrado'], 404);
        }

        return response()->json($modelo, 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo == null) {
            return  response()->json(['error' => 'Não foi possível atualizar o registro, pois não existe'], 404);
        }

        if ($request->method() === 'PATCH') {
            $regasDinamicas = [];

            foreach ($modelo->rules() as $input => $regras) {

                if (array_key_exists($input, $request->all())) {
                    $regasDinamicas[$input] = $regras;
                }
            }

            $request->validate($regasDinamicas);
            // dd($regasDinamicas);

        } else {
            $request->validate($modelo->rules());
        }

        if ($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $img_urn = $imagem ? $imagem->store('imagens/modelos', 'public') : $modelo->imagem;

        $modelo->fill($request->all());
        $modelo->imagem = $img_urn;
        $modelo->save();

        // $modelo->update([
        //     'marca_id' => $request->marca_id,
        //     'nome' => $request->nome,
        //     'imagem' => $img_urn,
        //     'numero_portas' => $request->numero_portas,
        //     'lugares' => $request->lugares,
        //     'air_bag' => $request->air_bag,
        //     'abs' => $request->abs
        // ]);

        return  response()->json(['msg' => 'Marca Atualizada Com Sucesso'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo == null) {
            return  response()->json(['error' => 'Marca não existe'], 404);
        }


        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return  response()->json(['msg' => 'Modelo deletada Com Sucesso'], 200);
    }
}
