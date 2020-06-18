<?php

namespace App\Http\Livewire;

use Github\ResultPager;
use Laravel\Socialite\Facades\Socialite;
use Livewire\Component;

class ShowGithubRepositories extends Component
{
    public $includeDeletePermissions = false;
    public $includeOrganisationPermissions = false;

    public $repositories = [];

    public function mount()
    {
        $this->includeDeletePermissions = (bool) session()->get('includeDeletePermissions');
        $this->includeOrganisationPermissions = (bool) session()->get('includeOrganisationPermissions');
    }

    public function render()
    {
        return view('livewire.show-github-repositories');
    }

    public function onConnect()
    {
        $scopes = ['user', 'repo'];

        if ($this->includeDeletePermissions) {
            array_push($scopes, 'delete_repo');
            session()->put('includeDeletePermissions', 1);
        } else {
            session()->forget('includeDeletePermissions');
        }

        if ($this->includeOrganisationPermissions) {
            array_push($scopes, 'read:org');
            session()->put('includeOrganisationPermissions', 1);
        } else {
            session()->forget('includeOrganisationPermissions');
        }

        $response = Socialite::driver('github')
            ->setScopes($scopes)
            ->redirect();

        $this->redirect($response->getTargetUrl());
    }

    public function onDisconnect()
    {
        auth()->user()->update([
            'github_token' => null,
            'remember_token' => null,
        ]);

        auth()->logout();

        $this->redirect(route('show-home'));
    }

    public function onFetchRepositories()
    {
        $github = app('github.factory')->make([
            'token' => auth()->user()->github_token,
            'method' => 'token',
        ]);

        $pager = new ResultPager($github);
        $parameters = ['affiliation' => 'owner'];

        // add the first page for this user
        $repositories = $pager->fetch($github->api('current_user'), 'repositories', $parameters);

        // add the rest of the user repo pages
        while ($pager->hasNext()) {
            $repositories = array_merge($repositories, $pager->fetchNext());
        }

        if ($this->includeOrganisationPermissions) {
            $organizations = $pager->fetch($github->api('current_user'), 'organizations');

            foreach ($organizations as $organization) {
                $pager = new ResultPager($github);
    
                $organisationParameters = [$organization['login'], 'sources'];
                $organisationRepositories = $pager->fetch($github->api('organization'), 'repositories', $organisationParameters);

                // add the first page for this org
                $repositories = array_merge($repositories, $organisationRepositories);

                // add the rest of the org repo pages
                while ($pager->hasNext()) {
                    $repositories = array_merge($repositories, $pager->fetchNext());
                }
            }
        }
        
        $this->repositories = $repositories;
    }
}
