<div class="header p-3 flex justify-between items-center 
        fixed top-0 left-64 w-[calc(100%-254px)] z-50 bg-gray-100">
    <div class="logo">
        
    </div>
    <div class="user-profile flex items-center">
        <div class="notification-bell relative mr-4">
            <a href="#" id="notification-bell" class="relative">
                <img src="https://img.icons8.com/ios-filled/50/000000/bell.png" alt="Notification Bell" class="bell-icon w-6 h-6">
            </a>
            <div id="notification-dropdown" class="dropdown-content absolute right-0 mt-2 w-64 bg-white shadow-lg rounded-lg hidden">
                <div class="notification-item p-4 border-b border-gray-200">
                    <p>New appointment request</p>
                    <button onclick="showDetails('appointment')" class="text-blue-500">Details</button>
                </div>
                <div class="notification-item p-4 border-b border-gray-200">
                    <p>New message from client</p>
                    <button onclick="showDetails('message')" class="text-blue-500">Details</button>
                </div>
                <div class="notification-item p-4">
                    <p>Reminder: Pet vaccination</p>
                    <button onclick="showDetails('reminder')" class="text-blue-500">Details</button>
                </div>
            </div>
        </div>
        <a href="{{ route('shopprofile') }}">
            <img src="https://i.pravatar.cc/150?img=1" alt="User Picture" class="user-pic w-10 h-10 rounded-full">
        </a>
    </div>
</div>

<!-- Add this style to ensure the main content is not hidden behind the fixed header -->


<script>
function showDetails(type) {
    alert('Showing details for: ' + type);
}

document.getElementById('notification-bell').addEventListener('click', function(event) {
    event.preventDefault();
    var dropdown = document.getElementById('notification-dropdown');
    dropdown.classList.toggle('hidden');
});

document.addEventListener('click', function(event) {
    var dropdown = document.getElementById('notification-dropdown');
    var bell = document.getElementById('notification-bell');
    if (!dropdown.contains(event.target) && !bell.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>
