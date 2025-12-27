<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Mary\Traits\Toast;

class ForceChangePassword extends Component
{
    use Toast;

    public $password;

    public $password_confirmation;

    protected function passwordRules(): array
    {
        return [
            'string',
            Password::default(),  // Keeps your strong password requirements (min 8, etc.)
            'confirmed',
        ];
    }

    public function save()
    {
        $validated = $this->validate([
            'password' => ['required', 'confirmed', ...$this->passwordRules()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        // Regenerate session to prevent session fixation
        session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.force-change-password');
    }
}
