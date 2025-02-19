<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Traits\InteractsWithJson;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    use InteractsWithJson;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Organization $organization)
    {
        $result = $organization->transactions()
            ->when(
                $request->get('from') && $request->get('to'),
                function (BUilder $builder) use ($request) {
                    return $builder->whereBetween('date', [$request->get('from'), $request->get('to')]);
                },
                function (Builder $builder) use ($request) {
                    return $builder->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                }
            )
            ->get();

        return $this->sendJson($result);
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
            'category_id' => [
                'nullable',
                'numeric',
                Rule::exists('categories', 'id')
                    ->where('type', $request->type)
                    ->where('organization_id', $organization->id)
            ],
            'description' => 'nullable|string'
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['date'] = Carbon::parse($validated['date']);

        $organization->transactions()->create($validated);

        return $this->sendJson(null, 201, 'Transaksi berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, string $transaction_id)
    {
        $transaction = $organization->transactions()->findOrFail($transaction_id);

        return $this->sendJson($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization, string $transaction_id)
    {
        $validated = $request->validate([
            'date' => 'required|string|date',
            'title' => 'required|string',
            'type' => 'required|numeric|in:1,2',
            'amount' => 'required|numeric',
            'category_id' => 'nullable|numeric',
            'description' => 'nullable|string'
        ]);

        $transaction = $organization->transactions()->findOrFail($transaction_id);
        $transaction->update($validated);

        return $this->sendJson(null, message: 'Transaksi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization, string $transaction_id)
    {
        $transaction = $organization->transactions()->findOrFail($transaction_id);
        $transaction->delete();

        return $this->sendJson(null, message: 'Transaksi telah dihapus!');
    }
}
