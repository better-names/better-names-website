<?php

namespace App\Http\Livewire;

use Exception;
use Livewire\Component;

class RenameRepositoryDefaultBranch extends Component
{
    public $branch = "main";
    public $user;
    public $name;
    public $includeDeletePermissions = false;
    public $shouldDelete;

    public $createError;
    public $deleteError;
    public $deleteRepositoryError;
    public $shouldHide = false;

    public function mount($user, $name, $includeDeletePermissions)
    {
        $this->user = $user;
        $this->name = $name;
        $this->includeDeletePermissions = $includeDeletePermissions;
    }

    public function onRename()
    {
        $github = app('github.factory')->make([
            'token' => auth()->user()->github_token,
            'method' => 'token',
        ]);

        try {
            $githubRef = $github->api('gitData')->references()->show($this->user, $this->name, 'heads/master');

            $newReferenceData = ['ref' => "refs/heads/{$this->branch}", 'sha' => $githubRef['object']['sha']];
            $newGithubRef = $github->api('gitData')->references()->create($this->user, $this->name, $newReferenceData);

            if ($this->shouldDelete) {
                try {
                    $github->api('gitData')->references()->remove($this->user, $this->name, 'heads/master');
                    $this->deleteError = null;
                } catch (Exception $e) {
                    $this->deleteError = $e->getMessage();
                }
            }

            $this->createError = null;
        } catch (Exception $e) {
            $this->createError = $e->getMessage();
        }

        if (!$this->createError and !$this->deleteError) {
            $this->shouldHide = true;
        }
    }

    public function onDelete()
    {
        $github = app('github.factory')->make([
            'token' => auth()->user()->github_token,
            'method' => 'token',
        ]);

        try {
            $github->api('repo')->remove($this->user, $this->name);
            $this->deleteRepositoryError = null;
        } catch (Exception $e) {
            $this->deleteRepositoryError = $e->getMessage();
        }

        if (!$this->deleteRepositoryError) {
            $this->shouldHide = true;
        }
    }

    public function render()
    {
        return view('livewire.rename-repository-default-branch');
    }
}
