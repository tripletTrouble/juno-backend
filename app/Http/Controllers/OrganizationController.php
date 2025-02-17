<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Traits\InteractsWithJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\File;

class OrganizationController extends Controller
{
    use InteractsWithJson;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        return $this->sendJson(
            Organization::where('user_id', $user->id)
                ->with([
                    'media' => fn($query) => $query->where('collection_name', 'logo')->limit(1)
                ])
                ->orderBy('name')
                ->paginate(5)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:300',
            'logo' => ['nullable', File::image()->max(1024)]
        ]);

        try {
            DB::beginTransaction();

            $organization = Organization::create([
                ...$data,
                'user_id' => $request->user()->id
            ]);

            if ($request->hasFile('logo')) {
                $organization->addMedia($request->file('logo'))->toMediaCollection('logo');
            }

            $organization->members()->attach($request->user()->id);

            DB::commit();

            return $this->sendJson(null, 201, 'created');
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th->getMessage());

            return $this->sendJson(null, 500, 'Something went wrong!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        return $this->sendJson($organization);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:300',
            'logo' => ['nullable', File::image()->max(1024)]
        ]);

        try {
            DB::beginTransaction();

            $organization->update($data);

            if ($request->hasFile('logo')) {
                $logo = $organization->getFirstMedia('logo');
                $logo->delete();

                $organization->addMedia($request->file('logo'))->toMediaCollection('logo');
            }

            DB::commit();

            return $this->sendJson(null, 200, 'Updated!');
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th->getMessage());

            $this->sendJson(null, 500, 'Something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();

        return $this->sendJson(null, 200, 'Deleted');
    }
}
