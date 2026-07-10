<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

trait HasGuestBooking
{
    public $guest_name;
    public $guest_phone;
    public $guest_divisi;
    public $guest_email;

    public $website = '';

    public function mountGuestBooking(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->guest_name = $user->name;
            $this->guest_phone = $user->phone ?? '';
            $this->guest_divisi = $user->position ?? '';
            $this->guest_email = $user->email;
        }
    }

    public function guestRules(): array
    {
        return [
            'guest_name' => 'required|string|max:255',
            'guest_phone' => ['required', 'string', 'max:20', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,11}$/'],
            'guest_divisi' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
        ];
    }

    public function guestValidationAttributes(): array
    {
        return [
            'guest_name' => 'nama',
            'guest_phone' => 'nomor HP',
            'guest_divisi' => 'divisi',
            'guest_email' => 'email',
        ];
    }

    protected function checkHoneypot(string $prefix): bool
    {
        if (!empty($this->website)) {
            $this->showSuccess = true;
            $this->successBookingCode = $prefix . '-SPAM-' . now()->format('His');
            return true;
        }

        return false;
    }

    protected function checkRateLimit(string $prefix, int $maxAttempts = 3, int $decaySeconds = 600): bool
    {
        $key = $prefix . ':' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $this->notifyError('Terlalu banyak percobaan booking. Silakan coba lagi dalam beberapa menit.');
            return true;
        }

        RateLimiter::hit($key, $decaySeconds);

        return false;
    }

    protected function logBookingError(string $type, \Throwable $e): void
    {
        Log::error("{$type} booking error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    }

    protected function guestBookingData(): array
    {
        return [
            'guest_name' => $this->guest_name,
            'guest_phone' => $this->guest_phone,
            'guest_divisi' => $this->guest_divisi,
            'guest_email' => $this->guest_email,
            'guest_ip' => request()->ip(),
        ];
    }

    protected function resetGuestFields(): void
    {
        $this->reset(['guest_name', 'guest_phone', 'guest_divisi', 'guest_email', 'website']);
    }
}
