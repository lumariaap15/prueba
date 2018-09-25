<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'nombre','categoria_id'
        ];
    protected $hidden = [
        'remember_token'
    ];
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
    public function detalle()
    {
        return $this->hasMany(Detalle::class);
    }

    public function movimiento()
    {
        return $this->belongsToMany(Movimiento::class,'detalles','article_id','movimiento_id');
    }
}
