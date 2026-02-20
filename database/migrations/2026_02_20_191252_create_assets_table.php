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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('asset_tag')->unique()->nullable();
            $table->enum('type', ['laptop', 'desktop', 'monitor', 'printer', 'phone', 'license', 'peripheral', 'other']);
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'in_repair', 'retired', 'disposed', 'in_stock'])->default('in_stock');
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expires_at')->nullable();
            $table->decimal('purchase_cost', 10, 2)->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
