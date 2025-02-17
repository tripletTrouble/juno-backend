<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Traits\InteractsWithJson;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    use InteractsWithJson;
    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization)
    {
        return $this->sendJson($organization->members()->latest()->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'users' => 'required|list'
        ]);
        
        $organization->members()->syncWithoutDetaching($validated['users']);
        
        return $this->sendJson(null, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'users' => 'required|list'
        ]);

        $organization->members()->detach($validated['users']);
    }
}
