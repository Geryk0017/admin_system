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
        Schema::create('add_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_id')->unique();
            $table->string('title');
            $table->string('description');
            $table->foreignId('developer_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->enum('priority_level', ['low', 'medium', 'high']);
            $table->date('start_date');
            $table->date('due_date');
            $table->string('notes')->nullable();
            $table->integer('progress');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_tasks');
    }
};
