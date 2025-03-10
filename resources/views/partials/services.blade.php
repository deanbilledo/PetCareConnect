<section id="services" class="my-6">
    <h2 class="text-2xl font-semibold mb-4">Most Popular Services</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Grooming Services (2 cards) -->
        @php
            // Get top 2 grooming services
            // Using a raw query to count appointments through the pivot table
            $topGroomingServices = App\Models\Service::where('category', 'grooming')
                ->select('services.*')
                ->selectRaw('(SELECT COUNT(*) FROM appointment_services WHERE services.id = appointment_services.service_id) as appointment_count')
                ->orderBy('appointment_count', 'desc')
                ->take(2)
                ->get();
            
            // Fallback to latest if no appointments exist
            if ($topGroomingServices->isEmpty()) {
                $topGroomingServices = App\Models\Service::where('category', 'grooming')
                    ->latest()
                    ->take(2)
                    ->get();
            }
        @endphp
        
        @foreach($topGroomingServices as $index => $service)
        <div class="bg-[#D9D9D9] rounded-lg shadow-md overflow-hidden relative w-full h-[500px] bg-cover bg-center transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl" 
             style="background-image: url('{{ asset('images/dog' . ($index + 1) . '.png') }}');">
            <div class="p-6 h-full flex flex-col bg-black bg-opacity-10 text-white transition duration-300 ease-in-out hover:bg-opacity-10">
                <p class="text-sm mb-2">GROOMING SERVICE</p>
                <h3 class="font-bold text-2xl mb-4">{{ $service->name }}</h3>
                <p class="text-sm mb-2">{{ $service->appointment_count ?? 0 }} appointments</p>
                <p class="mb-4 flex-grow">{{ Str::limit($service->description, 100) }}</p>
                <a href="/book/{{ $service->shop_id }}?service={{ $service->id }}" 
                   class="block text-center bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full transition duration-300">
                    Book Now
                </a>
            </div>
        </div>
        @endforeach
        
        <!-- Veterinary Services (2 cards) -->
        @php
            // Get top 2 veterinary services
            // Using a raw query to count appointments through the pivot table
            $topVetServices = App\Models\Service::where('category', 'veterinary')
                ->select('services.*')
                ->selectRaw('(SELECT COUNT(*) FROM appointment_services WHERE services.id = appointment_services.service_id) as appointment_count')
                ->orderBy('appointment_count', 'desc')
                ->take(2)
                ->get();
                
            // Fallback to latest if no appointments exist
            if ($topVetServices->isEmpty()) {
                $topVetServices = App\Models\Service::where('category', 'veterinary')
                    ->latest()
                    ->take(2)
                    ->get();
            }
        @endphp
        
        @foreach($topVetServices as $index => $service)
        <div class="bg-[#D9D9D9] rounded-lg shadow-md overflow-hidden relative w-full h-[500px] bg-cover bg-center transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl" 
             style="background-image: url('{{ asset('images/dog' . ($index + 3) . '.png') }}');">
            <div class="p-6 h-full flex flex-col bg-black bg-opacity-10 text-white transition duration-300 ease-in-out hover:bg-opacity-10">
                <p class="text-sm mb-2">VETERINARY SERVICE</p>
                <h3 class="font-bold text-2xl mb-4">{{ $service->name }}</h3>
                <p class="text-sm mb-2">{{ $service->appointment_count ?? 0 }} appointments</p>
                <p class="mb-4 flex-grow">{{ Str::limit($service->description, 100) }}</p>
                <a href="/book/{{ $service->shop_id }}?service={{ $service->id }}" 
                   class="block text-center bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-full transition duration-300">
                    Book Now
                </a>
            </div>
        </div>
        @endforeach
    </div>
</section> 

