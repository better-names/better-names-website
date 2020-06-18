<?php

namespace App\Http\Responders;

class ShowHomeResponder
{
    public function handle()
    {
        return view('show-home');
    }
}
