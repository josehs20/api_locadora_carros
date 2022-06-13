<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'imagem',
    ];

    public function rules()
    {
        return [
            'nome' => 'required:marcas,nome,'.$this->marca.'|min:3',
            'imagem' => 'required|file|mimes:png,jpeg,jpg',
        ];
    }

    public function feedback()
    {
        return [
            'required' => 'O campo attribute é obrigatório',
            'nome.unique' => 'O nome marca já existe',
            'nome.min' => 'Nome deve ter no mínimo 3 caracteres',
            'imagem.mimes' => 'Arquivo deve ser do tipo png,jpeg,jpg'
        ];
    }

    public function modelos()
    {
        return $this->hasMany(Modelo::class);
    }
}
