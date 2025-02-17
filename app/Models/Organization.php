<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Organization extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['name', 'description', 'user_id'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, Member::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function expenses(): HasMany
    {
        return $this->transactions()->where('type', TransactionType::Expense);
    }

    public function incomes(): HasMany
    {
        return $this->transactions()->where('type', TransactionType::Income);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function incomeCategory(): HasMany
    {
        return $this->categories()->where('type', TransactionType::Income);
    }

    public function expenseCategory(): HasMany
    {
        return $this->categories()->where('type', TransactionType::Expense);
    }
}
