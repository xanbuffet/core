<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::with('dishes')->get()->toResourceCollection();

        return $menus;
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        return $menu->load('dishes')->toResource();
    }
}
