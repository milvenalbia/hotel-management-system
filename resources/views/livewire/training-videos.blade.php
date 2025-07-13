@section('css')
    <link rel="stylesheet" href={{asset("custom-css/training-videos.css")}}>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endsection

<div>

    @if(Session::has('success'))
        <div x-data="{show: true}" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="alert-custom show showAlert">
            <span class="fas fa-check-circle ml-2"></span>
            <span class="text-white text-sm ml-5">{{session('success')}}</span>
        </div>
    @endif

    <div class="card" style="background-color: #e6e9ed;">
        <div class="card-body">
            <div class="row">
                <div class="col-12 grid-margin stretch-card justify-content-between">
                  <div class="card bg-dark">
                    <div class="card-body py-0 px-0 px-sm-3">
                      <div class="row align-items-center">
                        <div class="col-lg-2 col-sm-2 col-xl-2">
                          <img src={{ asset("admin/assets/images/dashboard/Group126@2x.png")}} class="img-fluid" alt="">
                        </div>
                        <div class="col-lg-6 col-sm-4 col-xl-6 pr-2 mt-2">
                          <h3 class="mb-1 mb-sm-0">Tutorial Videos</h3>
                          <p class="text-small">Watch videos to understand how to operate the system. You got this! Enjoy Watching! 😃 </p>
                        </div>
                        <div class="col-lg-4 col-sm-4 col-xl-4 pl-0">
                            <input wire:model.live.debounce.200ms="search" class="form-control text-dark bg-white border-0 shadow rounded-pill" placeholder="Search...">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>

            @if($videos)
            <div class="row">
                
                @foreach($videos as $d => $video)
                    <div class="col-xl-4 col-md-4 col-sm-6 mb-4">
                        <a href="{{$video->link}}" target="_blank">
                            
                            <div class="card" id="card-id" style="border: 0;">
                                    {{-- <h4 class="preview-subject text-center" id="overlay-text">{{$roomtype->roomtype}}</h4> --}}
                                <div id="youtube"></div>
                                <h3 id="card-text"><i class="fa-brands fa-youtube"></i> Watch Now</h3>
                                <div class="card-body">
                                    <div class="container-fluid">
                                        <img src="{{ Storage::url($video->video_path) }}" alt="Room Image" class="img-fluid rounded pr-3">
                                        <div class="d-flex">
                                            <h6 class="text-dark mt-2">{{$d+1}}. {{$video->description}}</h6>
                                        </div>
                                        <p class="text-dark">Duration: {{$video->duration}}</p>
                                    </div>
                                    
                                </div>

                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            @endif
            @if ($search && strlen($search) > 2)
                {{-- leave it empty --}}
            @else
                <div class="d-flex justify-content-end">
                    {{ $videos->links() }}
                </div>
            @endif



        </div>

    </div>

    {{-- Temporary Form to Submit Another Video --}}

    {{-- <div class="card mt-5">
        <div class="card-body">
            <form wire:submit.prevent="submit">
                <h3 class="text-dark">Temporary Form</h3>

                @if ($image)
                    <div class="container-fluid mb-2 text-center">
                        <img src="{{ $image->temporaryUrl() }}" alt="Image Preview" class="img-md">
                    </div>
                @else
                    <div class="container-fluid mb-2 text-center">
                        <img src="{{asset("admin/assets/images/img-photo.png")}}" alt="Image Preview" class="img-md border border-dark">
                    </div>
                @endif
                <div class="form-group">
                    <input wire:model="image" type="file" accept="image/png, image/jpeg, image/jpg" class="file-upload-default bg-dark">
                    <div class="input-group col-xs-12">
                        <input type="text" class="form-control file-upload-info bg-dark" disabled placeholder="Upload Hotel Logo">
                        <span class="input-group-append">
                            <button class="file-upload-browse btn btn-primary" type="button">Upload Logo</button>
                        </span>
                    </div>
                    @error('image') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <input type="text" wire:model.live="title" class="form-control" placeholder="Enter Title">
                </div>
                @error('title') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror

                <div class="form-group">
                    <input type="text" wire:model.live="link" class="form-control" placeholder="Enter Url">
                    
                </div>
                @error('link') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror

                <div class="form-group">
                    <input type="text" wire:model.live="duration" class="form-control" placeholder="Enter Duration">
                    
                </div>
                @error('duration') <span class="text-danger" style="font-size: 12px">{{ $message }}</span> @enderror

                <button type="submit" class="btn btn-primary rounded-pill w-100">Submit</button>

            </form>
        </div>
    </div> --}}

</div>