<nav class="sidebar sidebar-offcanvas" id="sidebar" style="background-color: #000000; border-right: 1px solid #000000;">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top" style="background-color: #000000; border-right: 1px solid #000000;">
      <a class="sidebar-brand brand-logo" href="/dashboard"><img src="{{ Storage::url(auth()->user()->logo) }}" alt="logo" /></a>
      <a class="sidebar-brand brand-logo-mini" href="/dashboard">
        <img src={{asset("admin/assets/images/icon-hotel.png")}} alt="logo"/>
      </a>
    </div>
    
    <ul class="nav">
      <li class="nav-item profile">
        <div class="profile-desc">
          <div class="profile-pic">
            <div class="count-indicator">
              <img class="img-xs rounded-circle" src="{{ Storage::url(auth()->user()->profile_image) }}" alt="">
              <span class="count bg-success"></span>
            </div>
            <div class="profile-name">
              <h5 class="mb-0 font-weight-normal">{{auth()->user()->name}}</h5>
              <span>{{auth()->user()->role}}</span>
            </div>
          </div>
        </div>
      </li>
      <li class="nav-item nav-category">
        <span class="nav-link">Navigation</span>
      </li>
      <li class="nav-item menu-items">
        <a class="nav-link" href="/dashboard">
          <span class="menu-icon">
            <i class="mdi mdi-speedometer" ></i>
          </span>
          <span class="menu-title">Dashboard</span>
        </a>
      </li>
      <li class="nav-item nav-category">
        <span class="nav-link">Front Desk Manager</span>
      </li>
      <li class="nav-item menu-items">
        <a class="nav-link" href="/booking">
          <span class="menu-icon">
            <i class="mdi mdi-clipboard-text"></i>
          </span>
          <span class="menu-title">Reservations</span>
        </a>
      </li>
      <li class="nav-item menu-items">
        <a class="nav-link" href="/guest-record">
          <span class="menu-icon">
            <i class="mdi mdi-book-open-page-variant"></i>
          </span>
          <span class="menu-title">Guest List</span>
        </a>
      </li>
      @if(request()->routeIs('manage-order*'))
      <li class="nav-item menu-items">
        <a class="nav-link disabled" href="{{ route('manage-order', ['bookId']) }}">
          <span class="menu-icon">
            <i class="mdi mdi-food "></i>
          </span>
          <span class="menu-title">New Guest</span>
        </a>
      </li>
      @elseif(request()->is('walk-in-check-in'))  
      <li class="nav-item menu-items">
        <a class="nav-link" href="/walk-in-check-in">
          <span class="menu-icon">
            <i class="mdi mdi-calendar-check"></i>
          </span>
          <span class="menu-title">New Guest</span>
        </a>
      </li>
      @else
      <li class="nav-item menu-items">
        <a class="nav-link" href="/checkInGuest">
          <span class="menu-icon">
            <i class="mdi mdi-calendar-check"></i>
          </span>
          <span class="menu-title">New Guest</span>
        </a>
      </li>
      @endif
      <li class="nav-item menu-items">
        <a class="nav-link" href="/roomtype">
          <span class="menu-icon">
            <i class="mdi mdi-table-edit"></i>
          </span>
          <span class="menu-title">Roomtype</span>
        </a>
      </li>
      <li class="nav-item menu-items">
        <a class="nav-link" href="/rooms">
          <span class="menu-icon">
            <i class="mdi mdi-seat-individual-suite"></i>
          </span>
          <span class="menu-title">Room Management</span>
        </a>
      </li>
      <li class="nav-item menu-items">
        <a class="nav-link" href="/product">
          <span class="menu-icon">
            <i class="mdi mdi-food"></i>
          </span>
          <span class="menu-title">Products</span>
        </a>
      </li>
      @if(request()->is('pos-reports'))
      <li class="nav-item menu-items">
        <a class="nav-link" href="/pos-reports">
          <span class="menu-icon">
            <i class="mdi mdi-printer"></i>
          </span>
          <span class="menu-title">Generate Reports</span>
        </a>
      </li>
      @elseif(request()->is('order-reports'))
      <li class="nav-item menu-items">
        <a class="nav-link" href="/order-reports">
          <span class="menu-icon">
            <i class="mdi mdi-printer"></i>
          </span>
          <span class="menu-title">Generate Reports</span>
        </a>
      </li>
      @else
      <li class="nav-item menu-items">
        <a class="nav-link" href="/transaction">
          <span class="menu-icon">
            <i class="mdi mdi-printer"></i>
          </span>
          <span class="menu-title">Generate Reports</span>
        </a>
      </li>
      @endif
      @if(auth()->user()->role === 'Admin')
      <li class="nav-item menu-items">
        <a class="nav-link" href="/manage-user">
          <span class="menu-icon">
            <i class="mdi mdi-account-box-outline"></i>
          </span>
          <span class="menu-title">Manage Users</span>
        </a>
      </li>

      <li class="nav-item menu-items">
        <a class="nav-link" href="/training-videos">
          <span class="menu-icon">
            <i class="mdi mdi-play-box-outline"></i>
          </span>
          <span class="menu-title">Training Videos</span>
        </a>
      </li>
      @endif
    </ul>
  </nav>
