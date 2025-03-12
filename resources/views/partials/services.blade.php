<section id="services" class="my-6">
    <h2 class="text-2xl font-semibold mb-4">Most Popular Services</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Grooming Services (2 cards) -->
        @php
            // Import DB facade
            use Illuminate\Support\Facades\DB;
            use App\Models\Service;
            use Illuminate\Support\Str;
            
            // Get all appointment data first to check if any appointments exist
            $appointmentCheck = DB::table('appointment_services')
                ->selectRaw('COUNT(*) as total_count')
                ->first();
                
            \Illuminate\Support\Facades\Log::info('Database Check:', [
                'total_appointments_in_pivot' => $appointmentCheck->total_count ?? 0
            ]);
            
            // Get top 2 grooming services with simplified counting
            $groomingServicesData = DB::table('services')
                ->select('services.id', 
                        DB::raw('COALESCE(ac.appointment_count, 0) as appointment_count'))
                ->leftJoin(DB::raw('(
                    SELECT 
                        service_id, 
                        COUNT(*) as appointment_count
                    FROM 
                        appointment_services
                    GROUP BY 
                        service_id
                ) as ac'), 'services.id', '=', 'ac.service_id')
                ->where('services.category', '=', 'grooming')
                ->whereNull('services.deleted_at')
                ->orderBy('appointment_count', 'desc')
                ->take(2)
                ->get();
                
            // Log the raw data
            \Illuminate\Support\Facades\Log::info('Raw Query Results:', [
                'grooming_services_data' => json_decode(json_encode($groomingServicesData), true)
            ]);
                
            // Get the service models
            $topGroomingServices = [];
            $serviceIds = [];
            
            foreach($groomingServicesData as $data) {
                $serviceIds[] = $data->id;
            }
            
            if (!empty($serviceIds)) {
                $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');
                
                // Combine the data
                foreach($groomingServicesData as $data) {
                    if (isset($services[$data->id])) {
                        $service = $services[$data->id];
                        $service->appointment_count = (int)$data->appointment_count;
                        $topGroomingServices[] = $service;
                    }
                }
            }
            
            // Fallback if empty
            if (empty($topGroomingServices)) {
                $topGroomingServices = Service::where('category', 'grooming')
                    ->latest()
                    ->take(2)
                    ->get();
                    
                foreach($topGroomingServices as $service) {
                    $service->appointment_count = 0;
                }
            }
            
            // Convert to collection if needed
            $topGroomingServices = collect($topGroomingServices);
            
            // Log for debugging
            \Illuminate\Support\Facades\Log::info('Top Grooming Services Result:', [
                'services' => $topGroomingServices->toArray()
            ]);
        @endphp
        
        @foreach($topGroomingServices as $index => $service)
        <div class="bg-[#D9D9D9] rounded-lg shadow-md overflow-hidden relative w-full h-[500px] bg-cover bg-center transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl" 
             style="background-image: url('{{ asset('images/dog' . ($index + 1) . '.png') }}');">
            <div class="p-6 h-full flex flex-col bg-black bg-opacity-10 text-white transition duration-300 ease-in-out hover:bg-opacity-10">
                <p class="text-sm mb-2">GROOMING SERVICE</p>
                <h3 class="font-bold text-2xl mb-4">{{ $service->name }}</h3>
                <p class="text-sm mb-2">{{ $service->appointment_count ?? 0 }} appointments</p>
                <p class="mb-4 flex-grow">{{ Str::limit($service->description ?? '', 100) }}</p>
                <a href="/book/{{ $service->shop_id }}?service={{ $service->id }}" 
                   class="block text-center bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full transition duration-300">
                    Book Now
                </a>
            </div>
        </div>
        @endforeach
        
        <!-- Veterinary Services (2 cards) -->
        @php
            // Get top 2 veterinary services with simplified counting
            $vetServicesData = DB::table('services')
                ->select('services.id', 
                        DB::raw('COALESCE(ac.appointment_count, 0) as appointment_count'))
                ->leftJoin(DB::raw('(
                    SELECT 
                        service_id, 
                        COUNT(*) as appointment_count
                    FROM 
                        appointment_services
                    GROUP BY 
                        service_id
                ) as ac'), 'services.id', '=', 'ac.service_id')
                ->where('services.category', '=', 'veterinary')
                ->whereNull('services.deleted_at')
                ->orderBy('appointment_count', 'desc')
                ->take(2)
                ->get();
                
            // Get the service models
            $topVetServices = [];
            $serviceIds = [];
            
            foreach($vetServicesData as $data) {
                $serviceIds[] = $data->id;
            }
            
            if (!empty($serviceIds)) {
                $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');
                
                // Combine the data
                foreach($vetServicesData as $data) {
                    if (isset($services[$data->id])) {
                        $service = $services[$data->id];
                        $service->appointment_count = (int)$data->appointment_count;
                        $topVetServices[] = $service;
                    }
                }
            }
            
            // Fallback if empty
            if (empty($topVetServices)) {
                $topVetServices = Service::where('category', 'veterinary')
                    ->latest()
                    ->take(2)
                    ->get();
                    
                foreach($topVetServices as $service) {
                    $service->appointment_count = 0;
                }
            }
            
            // Convert to collection if needed
            $topVetServices = collect($topVetServices);
            
            // Log for debugging
            \Illuminate\Support\Facades\Log::info('Top Veterinary Services Result:', [
                'services' => $topVetServices->toArray()
            ]);
        @endphp
        
        @foreach($topVetServices as $index => $service)
        <div class="bg-[#D9D9D9] rounded-lg shadow-md overflow-hidden relative w-full h-[500px] bg-cover bg-center transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl" 
             style="background-image: url('{{ asset('images/dog' . ($index + 3) . '.png') }}');">
            <div class="p-6 h-full flex flex-col bg-black bg-opacity-10 text-white transition duration-300 ease-in-out hover:bg-opacity-10">
                <p class="text-sm mb-2">VETERINARY SERVICE</p>
                <h3 class="font-bold text-2xl mb-4">{{ $service->name }}</h3>
                <p class="text-sm mb-2">{{ $service->appointment_count ?? 0 }} appointments</p>
                <p class="mb-4 flex-grow">{{ Str::limit($service->description ?? '', 100) }}</p>
                <a href="/book/{{ $service->shop_id }}?service={{ $service->id }}" 
                   class="block text-center bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-full transition duration-300">
                    Book Now
                </a>
            </div>
        </div>
        @endforeach
    </div>
</section> 

