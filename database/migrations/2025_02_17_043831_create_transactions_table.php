<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->index();
            $table->foreignId('organization_id')->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('type');
            $table->string('title');
            $table->decimal('amount', 20, 2);
            $table->string('description', 300)->nullable();
            $table->foreignId('category_id')->index()->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->index()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
