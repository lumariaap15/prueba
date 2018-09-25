<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $fillable = [
       'tipo','description'
    ];
    public function detalle()
    {
        return $this->hasMany(Detalle::class);
    }

    public function article()
    {
        return $this->belongsToMany(Article::class,'detalles','movimiento_id','article_id');
    }
}
