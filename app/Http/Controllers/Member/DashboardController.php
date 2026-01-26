<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = $user->member;

        // Fallback if no member record found (shouldn't happen if logged in via member portal logic)
        if (!$member) {
            abort(403, 'Unauthorized access: No member profile found.');
        }

        // Check for dummy data (Quick Add artifact)
        $isProfileIncomplete = $this->checkProfileCompleteness($member);

        return view('member.dashboard', [
            'user' => $user,
            'member' => $member,
            'isProfileIncomplete' => $isProfileIncomplete,
        ]);
    }

    private function checkProfileCompleteness($member)
    {
        // Logic: If phone starts with '000' (dummy pattern) OR address is '-'
        if (str_starts_with($member->phone, '000')) {
            return true;
        }
        if ($member->address === '-' || empty($member->address)) {
            return true;
        }
        return false;
    }
}
