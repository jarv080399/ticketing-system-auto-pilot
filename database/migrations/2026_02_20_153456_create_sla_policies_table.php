<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('priority', ['low', 'medium', 'high', 'critical']);
            $table->integer('response_time_minutes');
            $table->integer('resolution_time_minutes');
            $table->boolean('business_hours_only')->default(true);
            $table->timestamps();
            
            $table->unique('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_policies');
    }
};
