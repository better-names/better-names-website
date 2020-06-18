<div class="my-4">
    @if (!auth()->user() or !auth()->user()->github_token)
        <div class="space-y-4 mb-4">
            Include permissions for fetching organistion repositories: <input type="checkbox" wire:model="includeOrganisationPermissions" /><br>
            Include permissions for deleting unwanted repositories: <input type="checkbox" wire:model="includeDeletePermissions" />
        </div>
        <div wire:loading wire:target="onConnect">
            Connecting...
        </div>
        <button wire:loading.remove wire:click="onConnect" class="bg-gray-500 text-white rounded-sm px-2 py-1">
            Connect with GitHub
        </button>
    @else
        <div wire:loading wire:target="onDisconnect">
            Forgetting...
        </div>
        <div wire:loading wire:target="onFetchRepositories">
            Fetching...
        </div>
        <span wire:loading.remove>
            <button wire:loading.remove wire:click="onDisconnect" class="bg-gray-500 text-white rounded-sm px-2 py-1">
                Forget GitHub token and log out
            </button>
            <button wire:loading.remove wire:click="onFetchRepositories" class="bg-gray-500 text-white rounded-sm px-2 py-1">
                Fetch GitHub repositories
            </button>
        </span>
        @if (count(collect($repositories)->where('default_branch', 'master')))
            <h2 class="text-2xl mt-4">These still have "master" default branch</h2>
            <ol class="list-decimal mt-4 px-8">
                @foreach (collect($repositories)->where('default_branch', 'master') as $repository)
                    <li class="mb-4">
                        <strong>{{ $repository['full_name'] }}</strong>
                        <div>
                            <span
                                class="
                                    inline-flex w-4 h-4
                                    @if($repository['fork'])
                                        text-gray-900
                                    @else
                                        text-gray-200
                                    @endif
                                "
                            >
                                @svg('solid/code-branch')
                            </span>
                            <span
                                class="
                                    inline-flex w-4 h-4
                                    @if($repository['private'])
                                        text-gray-900
                                    @else
                                        text-gray-200
                                    @endif
                                "
                            >
                                @svg('solid/lock')
                            </span>
                        </div>
                        <livewire:rename-repository-default-branch :user="$repository['owner']['login']" :name="$repository['name']" :includeDeletePermissions="$includeDeletePermissions" />
                    </li>
                @endforeach
            </ol>
        @endif
    @endif
</div>
