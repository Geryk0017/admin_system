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
        Schema::table('application_forms', function (Blueprint $table) {
            $table->string('city');
            $table->string('barangay');
            $table->string('street');
            $table->string('zipcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_forms', function (Blueprint $table) {
            $table->string('city')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            $table->string('zipcode')->nullable();
        });
    }
};
