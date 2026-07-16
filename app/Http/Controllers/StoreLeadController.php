<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreLeadController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'in:1-10,11-30,31-50,50+'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        Lead::create([
            ...$data,
            'source' => 'Landing',
            'status' => LeadStatus::New,
        ]);

        return back()->with('lead_sent', true);
    }
}
