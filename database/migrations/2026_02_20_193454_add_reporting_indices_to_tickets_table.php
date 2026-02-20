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
        Schema::table('tickets', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
            $table->index(['agent_id', 'resolved_at']);
            $table->index(['category_id', 'created_at']);
            $table->index(['priority', 'sla_due_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['agent_id', 'resolved_at']);
            $table->dropIndex(['category_id', 'created_at']);
            $table->dropIndex(['priority', 'sla_due_at']);
        });
    }
};
