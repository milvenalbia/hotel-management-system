<div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog border-light" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close text-danger mr-1 pt-4" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                Are you sure you want to cancel booking of '{{$firstname}} {{$lastname}}'?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="cancel()" data-dismiss="modal">No</button>
                <button class="btn btn-danger" wire:click="deleteData()">Yes, cancel it!</button>
            </div>
        </div>
    </div>
</div>
