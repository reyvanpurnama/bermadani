<?php

namespace App\Http\Middleware;

use App\Models\Member;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $member = Member::where('userId', $request->user()?->id)->first();

        if ($member?->isReadOnly()) {
            return redirect()->route('member.dashboard')
                ->with('info', 'Transfer tidak tersedia untuk akun anggota non-aktif.');
        }

        return $next($request);
    }
}
