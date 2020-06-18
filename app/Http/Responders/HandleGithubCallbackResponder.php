<?php

namespace App\Http\Responders;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class HandleGithubCallbackResponder
{
    public function handle()
    {
        $githubUser = Socialite::driver('github')
            ->user();

        $user = User::updateOrCreate(
            ['github_email' => $githubUser->email],
            ['github_token' => $githubUser->token]
        );

        auth()->login($user, $remember = true);

        return redirect()->route('show-home');
    }
}
