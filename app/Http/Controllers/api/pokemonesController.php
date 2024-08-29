<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Pokemones;

class pokemonesController extends Controller
{
   
    public function index(Request $request)
{
    $rows = (int) $request->input('rows', 10);
    $page = 1 + (int) $request->input('page', 0);


    \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
        return $page;
    });

    // Filtrar los productos que tienen eliminado = 0
    $productos = Pokemones::select('nombre', 'pokemones.id', 'url', 'pokemones.updated_at', 'users.name')
    ->join('users', 'users.id', '=', 'pokemones.id_user')
    ->where('eliminado', 0)
    ->paginate($rows);


    return response()->json([
        'estatus' => 1,
        'data'=> $productos->items(),
        'total'=> $productos->total()
    ]);
}


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'tipo' => 'required',
            'url' => 'required|url',
            'hp' => 'required',
            'defensa' => 'required',
            'ataque' => 'required',
            'rapidez' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => $validator->errors()
            ]);
        }

        $id_user = auth()->user()->id;

        $pokemon = new Pokemones();
        $pokemon->nombre = $request->nombre;
        $pokemon->tipo = $request->tipo;
        $pokemon->url = $request->url;
        $pokemon->hp = $request->hp;
        $pokemon->defensa = $request->defensa;
        $pokemon->ataque = $request->ataque;
        $pokemon->rapidez = $request->rapidez;
        $pokemon->id_user = $id_user;
        $pokemon->eliminado = 0;
        $pokemon->save();

        return response()->json([
            'data'=> $pokemon,
            'estatus' => 1,
            'mensaje' => 'Pokemon registrado con exito'
        ]);
    }




    public function show(string $id)
    {
            $pokemon = Pokemones::select('pokemones.id', 'nombre', 'hp', 'rapidez', 'ataque', 'url', 'tipo', 'users.name', 'defensa') 
                ->join('users', 'users.id', '=', 'pokemones.id_user')
                ->where('pokemones.id', $id)
                ->where('pokemones.eliminado', 0)
                ->first();
        

        if (!$pokemon || $pokemon->eliminado == 1) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'Pokemon no encontrado'
            ]);
        }

        return response()->json([
            'estatus' => 1,
            'data' => $pokemon
        ]);
    }






    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'tipo' => 'required',
            'url' => 'required|url',
            'hp' => 'required|integer',
            'defensa' => 'required|integer',
            'ataque' => 'required|integer',
            'rapidez' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => $validator->errors()
            ]);
        }
    
        $pokemon = Pokemones::find($id);
        if (!$pokemon || $pokemon->eliminado == 1) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'Pokemon no encontrado'
            ]);
        }
    
        // Verificación para asegurarse de que solo el creador puede actualizar
        if ($pokemon->id_user !== auth()->user()->id) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'No se puede actualizar un Pokémon que no has creado'
            ]);
        }
    
        // Actualización del Pokémon
        $pokemon->update([
            'nombre' => $request->nombre,
            'tipo' => $request->tipo,
            'url' => $request->url,
            'hp' => $request->hp,
            'defensa' => $request->defensa,
            'ataque' => $request->ataque,
            'rapidez' => $request->rapidez,
        ]);
    
        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Pokemon actualizado con éxito'
        ]);
    }
    

    public function destroy(string $id)
    {
        // Buscar el Pokémon por ID
        $pokemon = Pokemones::find($id);
    
        // Si no se encuentra o está marcado como eliminado, retorna un error
        if (!$pokemon || $pokemon->eliminado == 1) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'Pokemon no encontrado'
            ]);
        }
    
        // Verificación para asegurarse de que solo el creador puede eliminar
        if ($pokemon->id_user !== auth()->user()->id) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'No se puede eliminar un Pokémon que no has creado'
            ]);
        }
    
        // Marcar el Pokémon como eliminado
        $pokemon->eliminado = 1;
        $pokemon->save();
    
        // Retorna una respuesta de éxito
        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Pokemon eliminado'
        ]);
    }
    
}
