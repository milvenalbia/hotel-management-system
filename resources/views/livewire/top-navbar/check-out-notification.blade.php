<div >
    {{-- <li class="nav-item dropdown">
        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown" title="Check Out Notification">
            <span class="text-small">Check Out</span>
            <i class="mdi mdi-bell"></i>
            @if ($count > 0)
                @foreach ($guestNames as $name)
                        <span class="bg-danger count-number font-weight-bold" style="position: absolute; top: -1px; right: -8px;">
                            <p class="font-weight-light" >{{ $count }}</p>
                        </span>
                @endforeach
            @else
            
            @endif
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
          <h6 class="p-3 mb-0">Notifications</h6>
          @if ($count > 0)
            @foreach ($guestNames as $name)
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item preview-item" href="#" wire:click="goToPage({{$name['id']}})">
                        <div class="preview-thumbnail">
                        <div class="preview-icon bg-dark rounded-circle">
                            <i class="mdi mdi-calendar text-success"></i>
                        </div>
                        </div>
                        <div class="preview-item-content">
                        <p class="preview-subject mb-1">{{$name['first_name']}}&nbsp;{{$name['last_name']}}</p>
                        <p class="text-muted ellipsis mb-0"> Check Out Date:&nbsp;{{$name['check_out']}} </p>
                        </div>
                    </a>
            @endforeach
            @else
                <div class="preview-item-content">
                <p class="text-xsmall mb-1 text-center w-50" style="margin-left: 40px">No Check-Outs today</p>
                </div>
          @endif
          <div class="dropdown-divider"></div>
          <p class="p-3 mb-0 text-center">Scheduled for Check-Out Today: {{$count}}</p>
        </div>
      </li> --}}

      <li class="nav-item menu-items">
        <h5 class="mt-1" style="margin-left: 370px;">{{$currentDateTime}}</h5>
      </li>
      
</div>
