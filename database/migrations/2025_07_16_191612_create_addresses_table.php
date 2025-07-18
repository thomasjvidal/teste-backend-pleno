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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_contact')->constrained('contacts')->onDelete('cascade');
            $table->string('zip_code', 10)->nullable(false);
            $table->string('country', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('street_address', 255)->nullable();
            $table->string('address_number', 20)->nullable(false);
            $table->string('city', 100)->nullable();
            $table->string('address_line', 255)->nullable();
            $table->string('neighborhood', 100)->nullable();
            $table->timestamps();
            
            // Índices para performance
            $table->index('zip_code');
            $table->index('city');
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
