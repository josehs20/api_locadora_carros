<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $marcaRepository = new MarcaRepository($this->marca);
     
        if ($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,'. $request->atributos_modelos;
            $marcaRepository->selectAtibutosRegistrosRelacionados($atributos_modelos);
        } else {
            $marcaRepository->selectAtibutosRegistrosRelacionados('modelos');
        }

        if ($request->has('filtro')) {
            $marcaRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {

            $marcaRepository->selectAtributos($request->atributos);
        }

        return response()->json($marcaRepository->getResultadoPaginado(3), 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->marca->rules(), $this->marca->feedback());

        $imagem = $request->file('imagem');
        $img_urn = $imagem->store('imagens', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $img_urn
        ]);
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = $this->marca->whith('modelo')->find($id);
        if ($marca === null) {
            return response()->json(['error' => 'Nenhum registro encontrado'], 404);
        }

        return response()->json($marca, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Integer
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);

        if($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no Model
            foreach($marca->rules() as $input => $regra) {
                
                //coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            
            $request->validate($regrasDinamicas, $marca->feedback());

        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }
        
        //preenchendo o objeto $marca com todos os dados do request
        $marca->fill($request->all());

        //se a imagem foi encaminhada na requisição
        if($request->file('imagem')) {
            //remove o arquivo antigo
            Storage::disk('public')->delete($marca->imagem);

            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens', 'public');
            $marca->imagem = $imagem_urn;
        }

        $marca->save();
        return response()->json($marca, 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);

        if ($marca == null) {
            return  response()->json(['error' => 'Marca não existe'], 404);
        }


        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();
        return  response()->json(['msg' => 'Marca deletada Com Sucesso'], 200);
    }
}
