<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;


class CarroController extends Controller
{

    public function __construct(Carro $carro)
    {
        $this->carro = $carro;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $carroRepository = new CarroRepository($this->carro);
     
        if ($request->has('atributos_modelo')) {
            $atributos_modelo = 'modelo:id,'. $request->atributos_modelo;
            $carroRepository->selectAtibutosRegistrosRelacionados($atributos_modelo);
        } else {
            $carroRepository->selectAtibutosRegistrosRelacionados('modelo');
        }

        if ($request->has('filtro')) {
            $carroRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {

            $carroRepository->selectAtributos($request->atributos);
        }

        return response()->json($carroRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCarroRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules());

        $carro = $this->carro->create([
            'modelo_id'=> $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km, 
       
       
        ]);
        return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $carro = $this->carro->with('modelo')->find($id);
        if ($carro === null) {
            return response()->json(['error' => 'Nenhum registro encontrado'], 404);
        }

        return response()->json($carro, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCarroRequest  $request
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $carro = $this->carro->find($id);

        if ($carro == null) {
            return  response()->json(['error' => 'N??o foi poss??vel atualizar o registro, pois n??o existe'], 404);
        }

        if ($request->method() === 'PATCH') {
            $regasDinamicas = [];

            foreach ($carro->rules() as $input => $regras) {
                if (array_key_exists($input, $request->all())) {
                    $regasDinamicas[$input] = $regras;
                }
            }

            $request->validate($regasDinamicas);
            // dd($regasDinamicas);

        } else {
            $request->validate($carro->rules());
        }

        $carro->fill($request->all());
        $carro->save();

        return  response()->json($carro, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $carro = $this->carro->find($id);

        if ($carro == null) {
            return  response()->json(['error' => 'Marca n??o existe'], 404);
        }

        $carro->delete();
        return  response()->json(['msg' => 'Marca deletada Com Sucesso'], 200);
    }
}
