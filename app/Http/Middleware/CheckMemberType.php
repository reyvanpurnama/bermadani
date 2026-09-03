<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Member;
use Symfony\Component\HttpFoundation\Response;

class CheckMemberType
{
    /**
     * Handle an incoming request.
     * Redirects users to the correct portal based on their member type.
     * - Cooperative members (isMemberKoperasi = true) -> /member
     * - Retail members (isMemberKoperasi = false) -> /membership
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $member = Member::where('userId', $user->id)->first();

        // If no member record found, let them through (might be admin)
        if (!$member) {
            return $next($request);
        }

        $currentPath = $request->path();
        $isCoopMember = $member->isMemberKoperasi;

        // Cooperative member trying to access retail portal
        if ($isCoopMember && str_starts_with($currentPath, 'membership')) {
            return redirect()->route('member.dashboard')
                ->with('info', 'Anda dialihkan ke portal anggota koperasi.');
        }

        // Retail member trying to access cooperative portal
        if (!$isCoopMember && str_starts_with($currentPath, 'member') && !str_starts_with($currentPath, 'membership')) {
            return redirect()->route('membership.dashboard')
                ->with('info', 'Anda dialihkan ke portal member retail.');
        }

        return $next($request);
    }
}
