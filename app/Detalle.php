<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    protected $fillable = [
        'movimiento_id','article_id','cantidad','costo'
    ];
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
    public function movimiento()
    {
        return $this->belongsTo(Movimiento::class);
    }
}
