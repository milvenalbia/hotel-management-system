<?php

namespace App\Livewire;

use App\Models\Videos;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class TraininVideos extends Component
{

    use WithFileUploads;
    use WithPagination;

    public $image;
    public $title;
    public $link;
    public $duration;

    public $search = '';
    
    public function render()
    {

        if ($this->search && strlen($this->search) > 2) {
            $videos = Videos::where(function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%');
            })
            ->get();
        } else {
            $videos = Videos::paginate(9);
        }
        
        

        return view('livewire.training-videos', compact('videos'));
    }

    public function updated($propertyName){
        $this->validateOnly($propertyName,[
            'title' => 'required',
            'image' => 'required',
            'duration' => 'required',
            'link' => 'required'
            ]);
    }


    public function submit(){

        $validatedData = $this->validate([
            'title' => 'required',
            'image' => 'required',
            'duration' => 'required',
            'link' => 'required'
            ]);

        if($this->image){
            $validatedData['video_path'] = $this->image->store('public/photos');
        }

        Videos::create([
            'video_path' => $validatedData['video_path'],
            'description' => $this->title,
            'duration' => $this->duration,
            'link' => $this->link
        ]);

        session()->flash('success', 'Saved Successfully');

        $this->reset();
    }
    
}
