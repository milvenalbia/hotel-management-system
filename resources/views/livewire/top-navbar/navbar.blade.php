<div>
    <nav class="navbar p-0 fixed-top d-flex flex-row bg-success" style="border-right: 1px solid #000000; shadow: none;">
        <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
          <a class="navbar-brand brand-logo-mini" href="/dashboard"><img src={{asset("assets/images/logo-mini.svg")}} alt="logo" /></a>
        </div>
        <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
          <button class="navbar-toggler navbar-toggler align-self-center text-light" type="button" data-toggle="minimize" title="Minimize Side Menu">
            <span class="mdi mdi-menu icon-md mt-1"></span>
          </button>
          <ul class="navbar-nav w-100 justify-content-between">
            <li class="nav-item menu-items">
                <h4 class="text-uppercase mt-1">Hotel Management System Simulator</h4>
            </li>
            <li class="nav-item menu-items align-items-end">
              <h5 class="mt-1">{{$currentDateTime}}</h5>
            </li>
          </ul>
          <ul class="navbar-nav navbar-nav-right">
            {{-- Lougout --}}
    
            <li class="nav-item dropdown">
              <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
                <div class="navbar-profile">
                    <img class="img-xs rounded-circle" src="{{ Storage::url(auth()->user()->profile_image) }}" alt="">
                  <p class="mb-0 d-none d-sm-block navbar-profile-name text-center">
                      {{auth()->user()->name}} <br> <span class="text-xs">Logout Here</span>
                  </p>
                  <i class="mdi mdi-arrow-down-bold-circle text-dark ml-1 d-none d-sm-block"></i>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
                <h6 class="p-3 mb-0">{{auth()->user()->role}}</h6>
                <div class="dropdown-divider"></div>
                
                <a class="dropdown-item preview-item" wire:click="openModal">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-dark rounded-circle">
                      <i class="mdi mdi-logout text-danger"></i>
                    </div>
                  </div>
                  <div class="preview-item-content">
                    <p class="preview-subject mb-1">Log out</p>
                  </div>
                </a>
                
              </div>
            </li>
            {{-- End of Logout --}}
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-format-line-spacing"></span>
          </button>
        </div>
      </nav>
    
      <div wire:ignore.self class="modal fade" id="logoutModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog border-light" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Logout Confirmation</h5>
                    <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cancellation" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="logouts">Logout</button>
                </div>
            </div>
        </div>
    </div>
    
     </div>

  @section('nav-scripts')
  @yield('scripts')

  {{-- <script>
    document.addEventListener('livewire:init', function () {
        setInterval(() => {
            @this.call('updateTime');
        }, 1000);
    });
  </script> --}}
  @endsection
