<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Organization;
use App\Traits\InteractsWithJson;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use InteractsWithJson;

    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization, Request $request)
    {
        $result = $organization->categories()
            ->when($request->get('type'), fn(HasMany $builder) => $builder->where('type', $request->get('type')))
            ->paginate(10);

        return $this->sendJson($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|numeric|in:1,2',
            'color' => 'nullable|string|hex_color',
            'description' => 'nullable|string'
        ]);

        $organization->categories()->create($validated);

        return $this->sendJson(null, 201, 'Kategori telah dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $organization_id, Category $category)
    {
        return $this->sendJson($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $organization_id, Category $category)
    {
        $validated = $request->validate([
            'type' => 'required|numeric|in:1,2',
            'name' => 'required|string',
            'color' => 'nullable|string|hex_color',
            'description' => 'nullable|string'
        ]);

        $category->update($validated);

        return $this->sendJson(null, message: 'Kategori telah diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $organization_id, Category $category)
    {
        $category->delete();

        return $this->sendJson(null, message: 'Kategori telah dihapus');
    }
}
