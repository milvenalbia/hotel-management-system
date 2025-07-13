<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        @include('components.layouts.css')

        @yield('css')

        @yield('style')

        <title>{{ $title ?? 'Hotel Management System' }}</title>
    </head>
    <body>
        <div class="container-scroller">

            {{-- Side Navbar --}}
            @include('components.layouts.sidebar')
            {{-- End of Side Navbar --}}

        <div class="container-fluid page-body-wrapper">

            {{-- Top Navbar --}}
            @livewire('navbar')
            {{-- End of Top Navbar --}}

          <div class="main-panel">
            <div class="content-wrapper" style="background-color: #ffffff;">


              {{$slot}}

          </div>

          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">&copy; 2023 {{auth()->user()->hotel_name}} & Resort. All rights reserved.</span>
            </div>
          </footer>
          
            </div>

        </div>

    </div>



    @include('components.layouts.scripts')

    <script>
        window.addEventListener('cancellation', event =>{
            $('#logoutModal').modal('hide');
        });
      
        window.addEventListener('open-modal', event =>{
            $('#logoutModal').modal('show');
        });
      </script>

      @yield('nav-scripts')

    </body>
</html>
