<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalation_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level')->default(1);
            $table->string('assigned_team')->nullable();
            $table->integer('sla_minutes')->nullable();
            $table->json('notification_channels')->nullable();
            $table->timestamps();
            
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalation_tiers');
    }
};
