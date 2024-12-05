<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favoriteShops = auth()->user()->favorites()
            ->with(['shop' => function($query) {
                $query->withAvg('ratings', 'rating');
            }])
            ->get()
            ->pluck('shop');

        return view('favorites.index', compact('favoriteShops'));
    }

    public function toggle(Shop $shop)
    {
        $user = auth()->user();
        $favorite = $user->favorites()->where('shop_id', $shop->id)->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
        } else {
            $user->favorites()->create([
                'shop_id' => $shop->id
            ]);
            $isFavorited = true;
        }

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'isFavorited' => $isFavorited
            ]);
        }

        return back()->with('success', $isFavorited ? 'Added to favorites' : 'Removed from favorites');
    }

    public function check(Shop $shop)
    {
        $isFavorited = auth()->user()->favorites()
            ->where('shop_id', $shop->id)
            ->exists();

        return response()->json([
            'isFavorited' => $isFavorited
        ]);
    }
} 