<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Transaction;
use App\Traits\InteractsWithJson;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use InteractsWithJson;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Organization $organization)
    {
        $result = $organization->transactions()
            ->when($request->get('type'), fn(HasMany $builder) => $builder->where('type', $request->get('type')))
            ->paginate(10);

        return $this->sendJson($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'date' => 'required|string|date',
            'title' => 'required|string',
            'type' => 'required|numeric|in:1,2',
            'amount' => 'required|numeric',
            'category_id' => 'nullable|numeric',
            'user_id' => $request->user()->id,
            'description' => 'nullable|string'
        ]);

        $organization->transactions()->create($validated);

        return $this->sendJson(null, 201, 'Transaksi berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $organization_id, Transaction $transaction)
    {
        return $this->sendJson($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $organization_id, Transaction $transaction)
    {
        $validated = $request->validate([
            'date' => 'required|string|date',
            'title' => 'required|string',
            'type' => 'required|numeric|in:1,2',
            'amount' => 'required|numeric',
            'category_id' => 'nullable|numeric',
            'description' => 'nullable|string'
        ]);

        $transaction->update($validated);

        return $this->sendJson(null, message: 'Transaksi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $organization_id, Transaction $transaction)
    {
        $transaction->delete();

        return $this->sendJson(null, message: 'Transaksi telah dihapus!');
    }
}
