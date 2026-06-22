<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ContactRequestController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ContactRequests/Index', [
            'contactRequests' => ContactRequest::latest()
                ->paginate(15)
                ->through(fn ($request) => [
                    'id' => $request->id,
                    'full_name' => $request->full_name,
                    'business_name' => $request->business_name,
                    'phone' => $request->phone,
                    'business_type' => $request->business_type,
                    'status' => $request->status,
                    'message' => $request->message,
                    'created_at' => $request->created_at->format('Y-m-d H:i'),
                ]),
        ]);
    }

    public function updateStatus( Request $request, ContactRequest $contactRequest)
    {
        $request->validate([
            'status' => [
                'required',
                'in:new,in_progress,converted,closed',
            ],
        ]);

        $contactRequest->update([
            'status' => $request->status,
        ]);

        return back();
    }
}