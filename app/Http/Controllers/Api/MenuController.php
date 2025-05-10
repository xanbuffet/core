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
        try {
            $cache_key = 'xan.menu.all';
            $menus = Cache::remember($cache_key, now()->addHours(8), function () {
                return Menu::with('dishes')->get();
            });

            return response()->json([
                'status' => 'success',
                'data' => $menus,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching menu items',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dayOfWeek = strtolower($id);
        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        if (! in_array($dayOfWeek, $validDays)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thứ không hợp lệ. Vui lòng nhập một trong các giá trị: '.implode(', ', $validDays),
            ], 400);
        }

        $cache_key = 'xan.menu.'.$dayOfWeek;
        $menu = Cache::remember($cache_key, now()->addHours(8), function () use ($dayOfWeek) {
            return Menu::with('dishes')->where('day_of_week', $dayOfWeek)->first();
        });

        if (! $menu) {
            return response()->json([
                'status' => 'error',
                'message' => "Không tìm thấy thực đơn cho thứ {$dayOfWeek}",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $menu,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
