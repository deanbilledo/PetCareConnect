<?php
namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favoriteShops = auth()->user()->favoriteShops()->withAvg('ratings', 'rating')->get();
        return view('favorites.index', compact('favoriteShops'));
    }

    public function toggle(Request $request, Shop $shop)
    {
        $user = auth()->user();
        
        if ($user->favorites()->where('shop_id', $shop->id)->exists()) {        
            $user->favorites()->where('shop_id', $shop->id)->delete();
            $isFavorited = false;
        } else {
            $user->favorites()->create(['shop_id' => $shop->id]);
            $isFavorited = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'isFavorited' => $isFavorited
            ]);
        }

        return back()->with('success', $isFavorited ? 'Added to favorites' : 'Removed from favorites');
    }

    public function check(Shop $shop)
    {
        if (!auth()->check()) {
            return response()->json(['isFavorited' => false]);
        }

        $isFavorited = auth()->user()->favorites()->where('shop_id', $shop->id)->exists();

        return response()->json([
            'isFavorited' => $isFavorited
        ]);
    }
} 