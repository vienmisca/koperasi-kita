<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ManualPasswordResetController extends Controller
{
    /**
     * Display the password reset request view.
     */
    public function create()
    {
        return view('auth.manual-forgot-password');
    }

    /**
     * Handle an incoming password reset request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Akun dengan email ini tidak ditemukan.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Password konfirmasi tidak sama.',
            'password.min' => 'Password harus minimal 8 karakter.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user is admin
        if ($user->role === 'admin') {
             return back()->withErrors(['email' => 'Admin tidak dapat mereset password melalui metode ini.']);
        }

        $user->pending_password = Hash::make($request->password);
        $user->reset_requested_at = now();
        $user->save();

        return back()->with('status', 'Permintaan reset password telah dikirim. Silakan hubungi Admin untuk konfirmasi.');
    }
    
    /**
     * Approve a password reset request (Admin only).
     */
    public function approve(User $user)
    {
        if (!$user->pending_password) {
            return back()->with('error', 'Tidak ada permintaan reset password untuk user ini.');
        }

        $user->password = $user->pending_password;
        $user->pending_password = null;
        $user->reset_requested_at = null;
        $user->save();

        return back()->with('success', 'Password user berhasil diperbarui.');
    }

    /**
     * Reject a password reset request (Admin only).
     */
    public function reject(User $user)
    {
        $user->pending_password = null;
        $user->reset_requested_at = null;
        $user->save();

        return back()->with('success', 'Permintaan reset password ditolak.');
    }
}
