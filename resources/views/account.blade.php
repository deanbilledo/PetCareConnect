<x-layout>
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <div class="flex items-center mb-6">
            <img src="https://i.pravatar.cc/150?img=1" alt="Profile Image" class="w-24 h-24 rounded-full mr-4">
            <div>
                <h1 class="text-3xl font-bold">Artoria Pendragon</h1>
                <p class="text-gray-600">Manage your account settings</p>
            </div>
        </div>

        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="John Doe" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="john.doe@example.com" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <small class="text-gray-600">Leave blank to keep current password</small>
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Account</button>
        </form>
    </div>
</x-layout>