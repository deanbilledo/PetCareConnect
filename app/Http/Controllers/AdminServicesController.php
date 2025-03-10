<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class AdminServicesController extends Controller
{
    public function index()
    {
        $services = Service::with(['shop'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.services', compact('services'));
    }

    public function updateStatus(Request $request, Service $service)
    {
        $service->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service status updated successfully'
        ]);
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }

    public function show(Service $service)
    {
        $service->load('shop');
        return response()->json([
            'success' => true,
            'service' => $service
        ]);
    }
} 