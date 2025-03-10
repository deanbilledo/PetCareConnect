<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\HasShop;

class ShopProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', HasShop::class]);
    }

    public function show()
    {
        $shop = auth()->user()->shop->load('operatingHours');
        return view('shop.profile', compact('shop'));
    }

    public function update(Request $request)
    {
        $shop = auth()->user()->shop;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $shop->update($validated);

        return back()->with('success', 'Shop profile updated successfully');
    }

    public function updateImage(Request $request)
    {
        try {
            $request->validate([
                'shop_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $shop = auth()->user()->shop;

            if ($request->hasFile('shop_image')) {
                // Delete old image if exists
                if ($shop->image && Storage::disk('public')->exists($shop->image)) {
                    Storage::disk('public')->delete($shop->image);
                }
                
                // Store new image
                $imagePath = $request->file('shop_image')->store('shop-images', 'public');
                
                // Update shop with new image path
                $shop->update([
                    'image' => $imagePath
                ]);

                return back()->with('success', 'Shop image updated successfully');
            }

            return back()->with('error', 'No image was uploaded');
        } catch (\Exception $e) {
            \Log::error('Error updating shop image: ' . $e->getMessage());
            return back()->with('error', 'Failed to update shop image: ' . $e->getMessage());
        }
    }

    public function uploadGalleryPhoto(Request $request)
    {
        $request->validate([
            'gallery_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            if ($request->hasFile('gallery_photo')) {
                $path = $request->file('gallery_photo')->store('shop-gallery', 'public');
                
                $shop = auth()->user()->shop;
                $shop->gallery()->create([
                    'image_path' => $path
                ]);

                return back()->with('success', 'Photo added to gallery successfully');
            }

            return back()->with('error', 'No photo was uploaded');
        } catch (\Exception $e) {
            \Log::error('Error uploading gallery photo: ' . $e->getMessage());
            return back()->with('error', 'Failed to upload photo');
        }
    }

    public function deleteGalleryPhoto($photoId)
    {
        try {
            $shop = auth()->user()->shop;
            $photo = $shop->gallery()->findOrFail($photoId);
            
            // Delete file from storage
            if (Storage::disk('public')->exists($photo->image_path)) {
                Storage::disk('public')->delete($photo->image_path);
            }
            
            // Delete record from database
            $photo->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error deleting gallery photo: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
} 