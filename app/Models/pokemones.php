<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class pokemones extends Model {
    use HasFactory;
    protected $table=  'pokemones';
    protected $primaryKey='id';
    protected $fillable=[
        'nombre',
        'tipo',
        'url',
        'hp',
        'defensa',
        'ataque',
        'rapidez',
        'created_at',
        'updated_at'

    ];
protected $hidden=[
    'eliminado',
    

];

public $timestamps=true;
Public function user(){
    return $this->belongsTo(User::class, 'id_user');
}
}
