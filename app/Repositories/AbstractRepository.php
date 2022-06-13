<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function selectAtibutosRegistrosRelacionados($atributos)
    {
        //atualiza o estado do model para ir montando a query 
        $this->model = $this->model->with($atributos);
    }

    public function filtro($filtros)
    {
        $filtros = explode(';', $filtros);

        foreach ($filtros as $key => $condicao) {
            $c = explode(':', $condicao);
        
            //atualiza novamente para ser montada
            $this->model = $this->model->where($c[0], $c[1], $c[2]);
        }
    }

    public function selectAtributos($atributos)
    {
      
        //mantendo o estado do objeto durante a sequencia de chamadas
        $this->model = $this->model->selectRaw($atributos);
    }

    public function getResultado() {
        return $this->model->get();
    }

    public function getResultadoPaginado($numeroRegistroPorPagina) {
        return $this->model->paginate($numeroRegistroPorPagina);
    }
}

?>
