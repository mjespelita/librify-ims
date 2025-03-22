
<!DOCTYPE html>
<html lang='{{ str_replace('_', '-', app()->getLocale()) }}'>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <meta name='csrf-token' content='{{ csrf_token() }}'>
        <meta name='author' content='Mark Jason Penote Espelita'>
        <meta name='keywords' content='Inventory Management System, IMS, ISP'>
        <meta name='description' content='Efficient inventory management system for Librify IT Solutions, designed to streamline operations, track network equipment, and optimize resource allocation to ensure seamless service delivery and reduce operational costs.'>

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link href='{{ url('assets/bootstrap/bootstrap.min.css') }}' rel='stylesheet'>
        <!-- FontAwesome for icons -->
        <link href='{{ url('assets/font-awesome/css/all.min.css') }}' rel='stylesheet'>
        <link rel='stylesheet' href='{{ url('assets/custom/style.css') }}'>
        <link rel='icon' href='{{ url('assets/logo.png') }}'>
    </head>
    <body class='font-sans antialiased'>

        <!-- Sidebar for Desktop View -->
        <div class='sidebar' id='mobileSidebar'>
            <div class='logo'>
                <div class="p-3">
                    <img src='{{ url('assets/logo.png') }}' alt=''> <br>
                </div>
                <div>
                    <b>Inventory Management System</b>
                </div>
            </div>
            <a href='{{ url('dashboard') }}' class='{{ request()->is('dashboard', 'admin-dashboard', 'technician-dashboard') ? 'active' : '' }}'>
                <i class='fas fa-tachometer-alt'></i> Dashboard
            </a>
            
            @if (Auth::user()->role === 'admin')
                <a href='{{ url('items') }}' class='{{ request()->is('items', 'trash-items', 'create-items', 'show-items/*', 'edit-items/*', 'delete-items/*', 'view-add-item-quantity-logs/*', 'create-add-item-quantity/*', 'items-search*') ? 'active' : '' }}'>
                    <i class='fas fa-box'></i> Items
                </a>

                <a href='{{ url('sites') }}' class='{{ request()->is('sites', 'trash-sites', 'create-sites', 'show-sites/*', 'edit-sites/*', 'delete-sites/*', 'sites-search*') ? 'active' : '' }}'>
                    <i class='fas fa-house'></i> Sites
                </a>
                
                <a href='{{ url('types') }}' class='{{ request()->is('types', 'trash-types', 'create-types', 'show-types/*', 'edit-types/*', 'delete-types/*', 'types-search*') ? 'active' : '' }}'>
                    <i class='fas fa-cogs'></i> Types
                </a>

                <a href='{{ url('technicians') }}' class='{{ request()->is('technicians', 'trash-technicians', 'create-technicians', 'show-technicians/*', 'edit-technicians/*', 'delete-technicians/*', 'technicians-search*') ? 'active' : '' }}'>
                    <i class='fas fa-users'></i> Technicians
                </a>

                <a href='{{ url('onsites') }}' class='{{ request()->is('onsites', 'view-technician-onsite-items/*', 'trash-onsites', 'create-onsites', 'show-onsites/*', 'edit-onsites/*', 'delete-onsites/*', 'onsites-search*') ? 'active' : '' }}'>
                    <i class='fas fa-house'></i> On Site Items
                </a>

                <a href='{{ url('damages') }}' class='{{ request()->is('damages', 'view-technician-damage-items/*', 'trash-damages', 'create-damages', 'show-damages/*', 'edit-damages/*', 'delete-damages/*', 'damages-search*') ? 'active' : '' }}'>
                    <i class='fas fa-exclamation-triangle'></i> Damaged Items
                </a>

                <a href='{{ url('itemlogs') }}' class='{{ request()->is('itemlogs', 'trash-itemlogs', 'create-itemlogs', 'show-itemlogs/*', 'edit-itemlogs/*', 'delete-itemlogs/*', 'itemlogs-search*') ? 'active' : '' }}'>
                    <i class='fas fa-bars'></i> Item Logs
                </a>
                
                <a href='{{ url('logs') }}' class='{{ request()->is('logs', 'create-logs', 'show-logs/*', 'edit-logs/*', 'delete-logs/*', 'logs-search*') ? 'active' : '' }}'>
                    <i class='fas fa-clipboard-list'></i> Logs
                </a>
            @endif

            @if (Auth::user()->role === 'technician')

                <a href='{{ url('my-sites') }}' class='{{ request()->is('my-sites', 'trash-sites', 'create-sites', 'show-sites/*', 'edit-sites/*', 'delete-sites/*', 'sites-search*') ? 'active' : '' }}'>
                    <i class='fas fa-house'></i> My Sites
                </a>

                <a href='{{ url('my-onsite-items/'.Auth::user()->id) }}' class='{{ request()->is('my-onsite-items/*', 'view-my-onsite-items-on-site/*', 'trash-onsites', 'create-my-onsite-items', 'show-my-onsite-items/*', 'edit-my-onsite-items/*', 'delete-my-onsite-items/*', 'my-onsite-items-search*') ? 'active' : '' }}'>
                    <i class='fas fa-house'></i> My On Site Items
                </a>

                <a href='{{ url('my-damaged-items/'.Auth::user()->id) }}' class='{{ request()->is('my-damaged-items/*', 'view-my-damaged-items-on-site/*', 'trash-damages', 'create-damages', 'show-damages/*', 'edit-damages/*', 'delete-damages/*', 'damages-search*') ? 'active' : '' }}'>
                    <i class='fas fa-exclamation-triangle'></i> My Damaged Items
                </a>

            @endif  
            
            <a href='{{ url('user/profile') }}'><i class='fas fa-user'></i> {{ Auth::user()->name }}</a>
        </div>

        <!-- Top Navbar -->
        <nav class='navbar navbar-expand-lg navbar-dark'>
            <div class='container-fluid'>
                <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarNav'
                    aria-controls='navbarNav' aria-expanded='false' aria-label='Toggle navigation' onclick='toggleSidebar()'>
                    <i class='fas fa-bars'></i>
                </button>
            </div>
        </nav>

        <x-main-notification />

        <div class='content'>
            @yield('content')
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        {{-- apex charts --}}

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <!-- Bootstrap JS and dependencies -->
        <script src='{{ url('assets/bootstrap/bootstrap.bundle.min.js') }}'></script>

        <!-- Custom JavaScript -->
        <script src="{{ url('assets/custom/script.js') }}"></script>
        <script>
            function toggleSidebar() {
                document.getElementById('mobileSidebar').classList.toggle('active');
                document.getElementById('sidebar').classList.toggle('active');
            }
        </script>
    </body>
</html>
