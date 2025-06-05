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
            $cache_key = 'xan.api.menu.all';
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
    public function show(string $day)
    {
        $day_of_week = strtolower($day);
        $valid_days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        if (!in_array($day_of_week, $valid_days)) {
            return response()->json([
                'message' => 'Thứ không hợp lệ. Vui lòng dùng: ' . implode(', ', $valid_days),
            ], 400);
        }

        $cache_key = 'xan.api.menu.'.$day_of_week;

        $menu = Cache::remember($cache_key, now()->addHours(8), fn () =>
            Menu::with('dishes')
                ->where('day_of_week', $day_of_week)
                ->first()?->toResource()
        );

        if (! $menu) {
            return response()->json([
                'message' => "Không tìm thấy menu cho {$day_of_week}",
            ], 404);
        }

        return $menu;
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
