<div>
    @if($shouldHide)
        Done!
    @else
        <div wire:loading wire:target="onRename">
            Renaming...
        </div>
        <div wire:loading wire:target="onDelete">
            Deleting...
        </div>
        <div wire:loading.remove wire:target="onRename">
            New default branch name: <input type="text" wire:model="branch" class="border-b-2 border-yellow-300 bg-yellow-100 px-2 py-1" /><br>
            Also delete "master" branch: <input type="checkbox" wire:model="shouldDelete" /><br>
            <form wire:submit.stop.prevent="onRename" class="inline">
                <button onclick="return prompt('Type RENAME to continue') === 'RENAME'" class="bg-red-500 text-white rounded-sm px-2 py-1">Rename branch</button>
            </form>
            @if($includeDeletePermissions)
                <form wire:submit.stop.prevent="onDelete" class="inline">
                    <button onclick="return prompt('Type DELETE to continue') === 'DELETE'" class="bg-red-500 text-white rounded-sm px-2 py-1">Delete repository</button>
                </form>
            @endif
        </div>
        @if($createError)
            <div class="text-red-500">{{ $createError }}</div>
        @endif
        @if($deleteError)
            <div class="text-red-500">{{ $deleteError }}</div>
        @endif
        @if($deleteRepositoryError)
            <div class="text-red-500">{{ $deleteRepositoryError }}</div>
        @endif
    @endif
</div>
