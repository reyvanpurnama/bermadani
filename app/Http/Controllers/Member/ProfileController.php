<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            abort(403, 'No member profile found.');
        }

        return view('member.profile', [
            'user' => $user,
            'member' => $member,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        // Normalize phone
        $phone = preg_replace('/\D/', '', $validated['phone']);
        if (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }
        if (str_starts_with($phone, '8')) {
            $phone = '0' . $phone;
        }

        // Check if phone is already used by another member
        $existingMember = \App\Models\Member::where('phone', $phone)
            ->where('id', '!=', $member->id)
            ->first();

        if ($existingMember) {
            return back()->withErrors(['phone' => 'Nomor HP sudah digunakan member lain.']);
        }

        // Update member
        $member->update([
            'name' => $validated['name'],
            'phone' => $phone,
            'address' => $validated['address'] ?? $member->address,
        ]);

        // Also update user name
        $user->update(['name' => $validated['name']]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
            'mustChangePassword' => false,
            'passwordChangedAt' => now(),
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}
