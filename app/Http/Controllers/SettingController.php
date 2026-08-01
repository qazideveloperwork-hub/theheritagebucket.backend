<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.options', ['setting' => Setting::current()]);
    }

    public function update(Request $request): RedirectResponse
    {
        // Digits only, with or without country code (e.g. 923001234567 or 03001234567).
        $phoneRule = ['regex:/^[0-9]{10,15}$/'];

        // Either a plain numeric account number, or a full IBAN (2-letter country code + 2 check digits + BBAN).
        $accountNumberRule = ['regex:/^([0-9]{5,20}|[A-Za-z]{2}[0-9]{2}[A-Za-z0-9]{10,30})$/'];

        $rules = match ($request->input('section')) {
            'whatsapp' => [
                'whatsapp_number' => array_merge(['required', 'string'], $phoneRule),
            ],
            'email' => [
                'contact_email' => ['required', 'email', 'max:255'],
            ],
            'jazzcash' => [
                'jazzcash_number' => array_merge(['required', 'string'], $phoneRule),
                'jazzcash_account_title' => ['required', 'string', 'max:255'],
            ],
            'bank' => [
                'bank_name' => ['required', 'string', 'max:255'],
                'bank_account_number' => array_merge(['required', 'string'], $accountNumberRule),
                'bank_account_title' => ['required', 'string', 'max:255'],
            ],
            'cart' => [
                'max_quantity_per_product' => ['required', 'integer', 'min:1', 'max:100'],
            ],
            default => abort(404),
        };

        $messages = [
            'whatsapp_number.regex' => 'Enter a valid phone number with country code, digits only (e.g. 923001234567).',
            'jazzcash_number.regex' => 'Enter a valid phone number with country code, digits only (e.g. 923001234567).',
            'bank_account_number.regex' => 'Enter a valid account number or IBAN.',
        ];

        $data = $request->validate($rules, $messages);

        Setting::current()->update($data);

        return redirect()->route('settings.options')->with('success', 'Options were updated.');
    }
}
