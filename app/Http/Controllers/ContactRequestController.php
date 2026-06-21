<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use Illuminate\Http\Request;

class ContactRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'business_type' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string'],
        ]);

        ContactRequest::create([
            ...$validated,
            'locale' => app()->getLocale(),
            'status' => 'new',
        ]);
        return back()->with('success', 'Your request has been submitted successfully.');
    }
}