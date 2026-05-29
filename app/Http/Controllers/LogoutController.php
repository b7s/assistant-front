<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Service\ApiClient;
use Illuminate\Support\Facades\Auth;

class LogoutController
{
    public function __invoke(ApiClient $api)
    {
        $api->logout();
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }
}
