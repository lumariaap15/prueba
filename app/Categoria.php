<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model

{
    use Notifiable;
    public function article()
    {
        return $this->hasMany(Article::class);
    }
}
