<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;

class ClienteController extends Controller
{


    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $clienteRepository = new ClienteRepository($this->cliente);

        if ($request->has('filtro')) {
            $clienteRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {

            $clienteRepository->selectAtributos($request->atributos);
        }

        return response()->json($clienteRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreClienteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->cliente->rules());

        $cliente = $this->cliente->create([
            'nome'=> $request->nome,       
        ]);
        return response()->json($cliente, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cliente = $this->cliente->find($id);
        if ($cliente === null) {
            return response()->json(['error' => 'Nenhum registro encontrado'], 404);
        }
        return response()->json($cliente, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateClienteRequest  $request
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cliente = $this->cliente->find($id);

        if ($cliente == null) {
            return  response()->json(['error' => 'Não foi possível atualizar o registro, pois não existe'], 404);
        }

        if ($request->method() === 'PATCH') {
            $regasDinamicas = [];

            foreach ($cliente->rules() as $input => $regras) {
                if (array_key_exists($input, $request->all())) {
                    $regasDinamicas[$input] = $regras;
                }
            }

            $request->validate($regasDinamicas);
            // dd($regasDinamicas);

        } else {
            $request->validate($cliente->rules());
        }

        $cliente->fill($request->all());
        $cliente->save();

        return  response()->json($cliente, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cliente = $this->cliente->find($id);

        if ($cliente == null) {
            return  response()->json(['error' => 'Cliente não existe'], 404);
        }

        $cliente->delete();
        return  response()->json(['msg' => 'Cliente deletada Com Sucesso'], 200);
    }
}
