@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="relative text-center mb-16">
        <div class="absolute inset-0 bg-cover bg-center opacity-10" style="background-image: url('/images/pets/hero-bg.png')"></div>
        <h1 class="text-5xl font-bold mb-4 mt-10 relative">Comprehensive Pet Care Guide</h1>
        <p class="text-gray-600 text-xl relative">Everything you need to know about keeping your pets healthy and happy</p>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Diet and Nutrition Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 transform hover:scale-102 transition-transform duration-300">
            <div class="flex items-center mb-6">
                <img src="/images/pets/diet-icon.png" alt="Diet Icon" class="w-12 h-12 mr-4">
                <h2 class="text-2xl font-bold text-blue-600">Diet and Nutrition</h2>
            </div>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold mb-3">Basic Nutrition Guidelines</h3>
                    <ul class="list-disc pl-5 space-y-2 text-gray-700">
                        <li>Choose age-appropriate food (puppy/kitten, adult, senior)</li>
                        <li>Follow portion control based on weight and activity level</li>
                        <li>Maintain consistent feeding schedule (2-3 times daily)</li>
                        <li>Always provide fresh, clean water</li>
                        <li>Avoid toxic human foods (chocolate, grapes, onions)</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-3">Special Dietary Considerations</h3>
                    <ul class="list-disc pl-5 space-y-2 text-gray-700">
                        <li>Consult vet for pets with health conditions</li>
                        <li>Consider breed-specific nutritional needs</li>
                        <li>Monitor weight and adjust portions accordingly</li>
                        <li>Use treats moderately (10% of daily calories)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Grooming Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 transform hover:scale-102 transition-transform duration-300">
            <div class="flex items-center mb-6">
                <img src="/images/pets/grooming-icon.png" alt="Grooming Icon" class="w-12 h-12 mr-4">
                <h2 class="text-2xl font-bold text-green-600">Grooming Care</h2>
            </div>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold mb-3">Regular Grooming Routine</h3>
                    <ul class="list-disc pl-5 space-y-2 text-gray-700">
                        <li>Brush coat daily for long-haired pets, weekly for short-haired</li>
                        <li>Bathe monthly or as needed (use pet-specific shampoo)</li>
                        <li>Trim nails every 2-4 weeks</li>
                        <li>Clean ears weekly to prevent infections</li>
                        <li>Brush teeth 2-3 times weekly for dental health</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-3">Seasonal Grooming Tips</h3>
                    <ul class="list-disc pl-5 space-y-2 text-gray-700">
                        <li>Extra brushing during shedding seasons</li>
                        <li>Protect paws from hot/cold surfaces</li>
                        <li>Use pet-safe sunscreen for exposed skin</li>
                        <li>Check for ticks and fleas regularly</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Health Care Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 transform hover:scale-102 transition-transform duration-300">
            <div class="flex items-center mb-6">
                <img src="/images/pets/health-icon.png" alt="Health Icon" class="w-12 h-12 mr-4">
                <h2 class="text-2xl font-bold text-red-600">Health Care</h2>
            </div>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold mb-3">Preventive Care</h3>
                    <ul class="list-disc pl-5 space-y-2 text-gray-700">
                        <li>Schedule regular veterinary check-ups</li>
                        <li>Keep vaccinations up to date</li>
                        <li>Use monthly parasite prevention</li>
                        <li>Spay/neuter your pets</li>
                        <li>Maintain healthy weight through diet and exercise</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-3">Warning Signs to Watch</h3>
                    <ul class="list-disc pl-5 space-y-2 text-gray-700">
                        <li>Changes in appetite or water consumption</li>
                        <li>Unusual lethargy or behavior changes</li>
                        <li>Vomiting or diarrhea</li>
                        <li>Difficulty breathing or coughing</li>
                        <li>Seek immediate vet care for emergencies</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Exercise and Mental Stimulation -->
        <div class="bg-white rounded-lg shadow-lg p-6 transform hover:scale-102 transition-transform duration-300">
            <div class="flex items-center mb-6">
                <img src="/images/pets/exercise-icon.png" alt="Exercise Icon" class="w-12 h-12 mr-4">
                <h2 class="text-2xl font-bold text-purple-600">Exercise and Enrichment</h2>
            </div>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold mb-3">Physical Exercise</h3>
                    <ul class="list-disc pl-5 space-y-2 text-gray-700">
                        <li>Daily walks or play sessions</li>
                        <li>Age and breed-appropriate exercise levels</li>
                        <li>Avoid exercise in extreme weather</li>
                        <li>Include variety in activities</li>
                        <li>Monitor for signs of fatigue</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-3">Mental Stimulation</h3>
                    <ul class="list-disc pl-5 space-y-2 text-gray-700">
                        <li>Use puzzle toys and feeders</li>
                        <li>Training sessions for mental exercise</li>
                        <li>Rotate toys to maintain interest</li>
                        <li>Provide environmental enrichment</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Resources Section -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-8 mb-12">
        <h2 class="text-3xl font-bold mb-6 text-center">Additional Resources</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                <img src="/images/pets/emergency-icon.png" alt="Emergency Care" class="w-16 h-16 mx-auto mb-4">
                <h3 class="text-xl font-semibold mb-3 text-center">Emergency Care</h3>
                <p class="text-gray-600 text-center">Keep emergency vet contacts handy and know the location of 24/7 pet hospitals in your area.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                <img src="/images/pets/insurance-icon.png" alt="Pet Insurance" class="w-16 h-16 mx-auto mb-4">
                <h3 class="text-xl font-semibold mb-3 text-center">Pet Insurance</h3>
                <p class="text-gray-600 text-center">Consider pet insurance to help with unexpected veterinary expenses.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                <img src="/images/pets/community-icon.png" alt="Pet Community" class="w-16 h-16 mx-auto mb-4">
                <h3 class="text-xl font-semibold mb-3 text-center">Pet Community</h3>
                <p class="text-gray-600 text-center">Join local pet owner groups for support and socialization opportunities.</p>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="text-center bg-white rounded-lg shadow-lg p-8 max-w-2xl mx-auto">
        <img src="/images/logo.png" alt="Pet Care Connect" class="h-16 mx-auto mb-8">
        <p class="text-gray-600 text-xl mb-6">Need professional help with your pet's care?</p>
        <a href="{{ route('home') }}" class="inline-block bg-[#4086F4] text-white px-8 py-4 rounded-full hover:bg-blue-600 transition-colors text-lg font-semibold shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300">
            Find Pet Care Services Near You
        </a>
    </div>
</div>
@endsection 