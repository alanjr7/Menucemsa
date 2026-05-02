<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MenuController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Menu::class);

        // Paginamos solo los menús raíz (donde parent_id es null)
        $menus = Menu::whereNull('parent_id')
            ->with('children')
            ->orderBy('order')
            ->paginate(4); // Cambiado de get() a paginate()

        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        $this->authorize('create', Menu::class);

        // Traemos los padres para el select de "Menú Padre"
        $parents = Menu::whereNull('parent_id')->orderBy('order')->get();
        return view('menus.create', compact('parents'));
    }

    public function store(StoreMenuRequest $request)
    {
        Menu::create($request->validated());
        return redirect()->route('menus.index')->with('success', 'Menú creado correctamente.');
    }

    public function edit(Menu $menu)
    {
        $this->authorize('update', $menu);

        $parents = Menu::whereNull('parent_id')->where('id', '!=', $menu->id)->orderBy('order')->get();
        return view('menus.edit', compact('menu', 'parents'));
    }

    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $menu->update($request->validated());
        return redirect()->route('menus.index')->with('success', 'Menú actualizado correctamente.');
    }

    public function destroy(Menu $menu)
    {
        $this->authorize('delete', $menu);

        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menú eliminado correctamente.');
    }
}
