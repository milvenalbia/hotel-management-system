<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="admin/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="admin/assets/vendors/css/vendor.bundle.base.css">
    <!-- Layout styles -->
    <link rel="stylesheet" href="admin/assets/css/style.css">
    <!-- End layout styles -->

    <link rel="shortcut icon" href="admin/assets/images/shorcut-icon.png" />

    <link rel="stylesheet" href={{asset("admin/assets/vendors/mdi/css/materialdesignicons.min.css")}}>

  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="row w-100 m-0">
          <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
            <div class="card col-lg-3 mx-auto text-dark">
              <div class="card-body px-3 py-5">
                @if(Session::has('success'))
                        <div x-data="{show: true}" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="alert alert-dark alert-dismissible fade show text-sm text-center rounded-pill mb-3" role="alert">
                            <i class="mdi mdi-check-circle mr-3"></i>{{session('success')}}
                        </div>
                @endif
                @if(Session::has('error'))
                        <div x-data="{show: true}" x-init="setTimeout(() => show = false, 3000)" x-show="show" class="alert alert-danger alert-dismissible fade show text-sm  text-center rounded-pill mb-3" role="alert">
                            <i class="mdi mdi-alert-circle mr-3"></i>{{session('error')}}
                        </div>
                @endif
                <h3 class="card-title text-left mb-3 text-dark">Login</h3>

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                  <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control text-large">
                    @error('username')
                    <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                    @error('password')
                    <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="text-center" style="margin-top: 20px">
                    <button type="submit" class="btn btn-primary btn-block enter-btn rounded-pill">Login</button>
                  </div>
                </form>

              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
        </div>
        <!-- row ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="admin/assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- inject:js -->
    <script src="admin/assets/js/off-canvas.js"></script>
    <script src="admin/assets/js/hoverable-collapse.js"></script>
    <script src="admin/assets/js/misc.js"></script>
    <script src="admin/assets/js/settings.js"></script>
    <script src="admin/assets/js/todolist.js"></script>
    <script src="alpine-js/alpine.js"></script>
    
    {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}
    <!-- endinject -->
  </body>
</html>