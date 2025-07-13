@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
<div>
    @if(Session::has('success'))
      <div x-data="{show: true}" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="alert-custom show showAlert">
          <span class="fas fa-check-circle ml-2"></span>
          <span class="text-white text-sm ml-5">{{session('success')}}</span>
      </div>
    @endif

    <h3 class="mb-3 text-dark">Dashboard</h3>

    <div class="row">

      <div class="col-sm-12 grid-margin">
        <div class="card" style="background-color: #e6e9ed;">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <h4 class="mb-3 ml-3 text-dark">SALES</h4>

              <select class="form-control text-white mr-3 mb-3 bg-dark" wire:model.live.debounce.200ms="sales_by_date" style="width: 20%;">
                <option value="all">All</option>
                <option value="today">Today</option>
                <option value="weekly">This Week</option> 
                <option value="monthly">This Month</option>
                <option value="yearly">This Year</option>
            </select>
            </div>

            <div class="d-flex d-sm-block d-md-flex">

              <div class="col-sm-4 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h5 class="text-dark">Room Sales</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0 text-dark">&#8369;{{ number_format($roomSales, 2, '.', ',') }}</h2>
                        </div>
                        <h6 class="text-dark font-weight-normal mt-2">Room Sales</h6>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-poll-box text-primary ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-sm-4 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h5 class="text-dark">Product Sales</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0 text-dark">&#8369;{{ number_format($productSales, 2, '.', ',') }}</h2>
                        </div>
                        <h6 class="text-dark font-weight-normal mt-2">Product Sold</h6>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-food text-success ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-sm-4 grid-margin">
                <a href="#" class="card text-decoration-none text-dark" wire:click="redirectToPos"> 
                <div class="card">
                  <div class="card-body">
                    <h5 class="text-dark">Total Sales</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0 text-dark">&#8369;{{ number_format($overallSales, 2, '.', ',') }}</h2>
                        </div>
                        <h6 class="text-dark font-weight-normal mt-2">Total Sales</h6>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-currency-rub text-danger ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
                </a>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

      <div class="row">

        <div class="col-sm-12 grid-margin">
          <div class="card" style="background-color: #e6e9ed;">
            <div class="card-body">
              <div class="stretch-card">
                <h4 class="mb-3 ml-3 text-dark">ROOMS</h4>
              </div>

              <div class="d-flex d-sm-block d-md-flex">

                <div class="col-sm-12 grid-margin stretch-card" style="flex: 0 0 20%;
                max-width: 100%; ">
                  <a href="#" class="card text-decoration-none text-dark" wire:click="redirectToRoom('Reserved')"> 
                    <div class="card">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                              <h3 class="mb-0">{{$reservedRooms}}</h3>
                            </div>
                          </div>
                          <div class="col-3">
                            <div class="icon " style="width: 40px;
                            height: 37px;
                            background: rgba(250, 96, 234, 0.11);
                            border-radius: 7px;
                            color: #FA60EA;">
                              <span class="mdi mdi-seal icon-item"></span>
                            </div>
                          </div>
                        </div>
                        <h6 class="text-dark font-weight-normal">Reserved</h6>
                      </div>
                    </div>
                  </a>
                </div>

                <div class="col-sm-12 grid-margin stretch-card" style="flex: 0 0 20%;
                max-width: 100%; ">
                  <a href="#" class="card text-decoration-none text-dark" wire:click="redirectToRoom('Block')"> 
                    <div class="card">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                              <h3 class="mb-0">{{$blockRooms}}</h3>
                            </div>
                          </div>
                          <div class="col-3">
                            <div class="icon icon-box-danger">
                              <span class="mdi mdi-block-helper icon-item"></span>
                            </div>
                          </div>
                        </div>
                        <h6 class="text-dark font-weight-normal">Block</h6>
                      </div>
                    </div>
                  </a>
                </div>

                <div class="col-sm-12 grid-margin stretch-card" style="flex: 0 0 20%;
                max-width: 100%; ">
                      <a href="#" class="card text-decoration-none text-dark mr-2" wire:click="redirectToRoom('Vacant')"> 
                        <div class="card">
                          <div class="card-body">
                            <div class="row">
                              <div class="col-9">
                                <div class="d-flex align-items-center align-self-start">
                                  <h3 class="mb-0">{{$availableRooms}}</h3>
                                </div>
                              </div>
                              <div class="col-3">
                                <div class="icon icon-box-success">
                                  <span class="mdi mdi-marker-check icon-item"></span>
                                </div>
                              </div>
                            </div>
                            <h6 class="text-dark font-weight-normal">Vacant</h6>
                          </div>
                        </div>
                        </a>
                </div>

                <div class="col-sm-12 grid-margin stretch-card" style="flex: 0 0 20%;
                max-width: 100%; ">
                  <a href="#" class="card text-decoration-none text-dark" wire:click="redirectToRoom('Occupied')"> 
                    <div class="card">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                              <h3 class="mb-0">{{$occupiedRooms}}</h3>
                            </div>
                          </div>
                          <div class="col-3">
                            <div class="icon icon-box-primary">
                              <span class="mdi mdi-lock icon-item"></span>
                            </div>
                          </div>
                        </div>
                        <h6 class="text-dark font-weight-normal">Occupied</h6>
                      </div>
                    </div>
                  </a>
                </div>

                <div class="col-sm-12 grid-margin stretch-card" style="flex: 0 0 20%;
                max-width: 100%; ">
                  <a href="/rooms" class="card text-decoration-none text-dark"> 
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-9">
                          <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{$rooms}}</h3>
                          </div>
                        </div>
                        <div class="col-3">
                          <div class="icon icon-box-primary">
                            <span class="mdi mdi-seat-individual-suite icon-item"></span>
                          </div>
                        </div>
                      </div>
                      <h6 class="text-dark font-weight-normal">Total Rooms</h6>
                    </div>
                  </div>
                  </a>
                </div>

              </div>

            </div>
          </div>
        </div>

      </div>

      <div class="row">

      <div class="col-sm-12 grid-margin">
        <div class="card" style="background-color: #e6e9ed;">
          <div class="card-body">
            <div class="d-flex">
              <h4 class="mb-3 ml-3 text-dark">Number of Expected Guest (Today)</h4>
            </div>

            <div class="d-flex d-sm-block d-md-flex">

              <div class="col-xl-6 col-sm-12 grid-margin stretch-card">
                <a href="#" class="card text-decoration-none text-dark" wire:click="redirectToBooking('Arrival Guest')"> 
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-9">
                          <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{$arrival}}</h3>
                          </div>
                        </div>
                        <div class="col-3">
                          <div class="icon icon-box-primary">
                            <span class="fa-solid fa-person-walking-luggage icon-item" style="transform: scaleX(-1);"></span>
                          </div>
                        </div>
                      </div>
                      <h6 class="text-dark font-weight-normal">Arrival Guest</h6>
                    </div>
                  </div>
                </a>
              </div>

              <div class="col-xl-6 col-sm-12 grid-margin stretch-card">
                <a href="#" class="card text-decoration-none text-dark" wire:click="redirectToBooking('Departing Guest')"> 
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-9">
                          <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{$departure}}</h3>
                          </div>
                        </div>
                        <div class="col-3">
                          <div class="icon icon-box-info">
                            <span class="fa-solid fa-person-walking-arrow-right icon-item"></span>
                          </div>
                        </div>
                      </div>
                      <h6 class="text-dark font-weight-normal">Departing Guest</h6>
                    </div>
                  </div>
                  </a>
              </div>

            </div>
          </div>
        </div>
      </div>

  </div>

      <div class="row">

        <div class="col-sm-12 grid-margin">
          <div class="card" style="background-color: #e6e9ed;">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <h4 class="mb-3 ml-3 text-dark">BOOKING STATUS</h4>

                <select class="form-control text-white mr-3 mb-3 bg-dark" wire:model.live.debounce.200ms="by_date" style="width: 20%;">
                  <option value="all">All</option>
                  <option value="today">Today</option>
                  <option value="weekly">This Week</option> 
                  <option value="monthly">This Month</option>
                  <option value="yearly">This Year</option>
              </select>
              </div>

              <div class="d-flex d-sm-block d-md-flex">

          <div class="col-xl-3 col-sm-12 grid-margin stretch-card">
            <a href="#" class="card text-decoration-none text-dark" wire:click="redirectToBooking('Reserved')"> 
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-9">
                      <div class="d-flex align-items-center align-self-start">
                        <h3 class="mb-0">{{$pendingCount}}</h3>
                      </div>
                    </div>
                    <div class="col-3">
                      <div class="icon icon-box-warning">
                        <span class="mdi mdi-calendar icon-item"></span>
                      </div>
                    </div>
                  </div>
                  <h6 class="text-dark font-weight-normal mt-2">Reserved Guest</h6>
                </div>
              </div>
              </a>
          </div>

          <div class="col-xl-3 col-sm-12 grid-margin stretch-card">
            <a href="#" class="card text-decoration-none text-dark" wire:click="redirectToBooking('In House')"> 
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-9">
                      <div class="d-flex align-items-center align-self-start">
                        <h3 class="mb-0">{{$activeCount}}</h3>
                      </div>
                    </div>
                    <div class="col-3">
                      <div class="icon icon-box-success ">
                        <span class=" mdi mdi-calendar-multiple-check icon-item"></span>
                      </div>
                    </div>
                  </div>
                  <h6 class="text-dark font-weight-normal mt-2">In House</h6>
                </div>
              </div>
              </a>
          </div>

        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
          <a href="#" class="card text-decoration-none text-dark" wire:click="redirectToBooking('Departed Guest')"> 
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-9">
                  <div class="d-flex align-items-center align-self-start">
                    <h3 class="mb-0">{{$inactiveCount}}</h3>
                  </div>
                </div>
                <div class="col-3">
                  <div class="icon icon-box-danger">
                    <span class="mdi mdi-logout-variant icon-item"></span>
                  </div>
                </div>
              </div>
              <h6 class="text-dark font-weight-normal mt-2">Departed Guest</h6>
            </div>
          </div>
          </a>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
          <a href="#" class="card text-decoration-none text-dark" wire:click="redirectToBooking('Cancelled')"> 
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-9">
                  <div class="d-flex align-items-center align-self-start">
                    <h3 class="mb-0">{{$cancelledCount}}</h3>
                  </div>
                </div>
                <div class="col-3">
                  <div class="icon icon-box-dark ">
                    <span class=" mdi mdi-account-minus
                    icon-item"></span>
                  </div>
                </div>
              </div>
              <h6 class="text-dark font-weight-normal mt-2">Cancelled Booking</h6>
            </div>
          </div>
          </a>
        </div>
      </div>

        </div>
      </div>
    </div>
  </div> 
</div>
  
  
  
