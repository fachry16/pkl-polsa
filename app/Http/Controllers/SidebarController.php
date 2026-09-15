<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SidebarController extends Controller
{
    public function toggle(Request $request, string $role)
    {
        $user = $request->user();

        abort_unless(in_array($role, ['kaprodi', 'direktur'], true), 422);
        abort_unless($user && $user->isDosen(), 403);
        abort_unless($role === 'kaprodi' ? $user->isKaprodi() : $user->isDirektur(), 403);

        $key = "sidebar_show_{$role}";
        $request->session()->put($key, ! (bool) $request->session()->get($key));

        return redirect()->back();
    }
}
