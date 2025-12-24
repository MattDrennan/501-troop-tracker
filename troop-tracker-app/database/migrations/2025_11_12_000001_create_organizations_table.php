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
        Schema::create('tt_organizations', function (Blueprint $table)
        {
            $table->id();

            // Self-referencing parent_id
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('tt_organizations')
                ->cascadeOnDelete();

            $table->string('name', 64);
            $table->string('type', 16);
            $table->integer('depth')->default(0);
            $table->integer('sequence')->default(0);
            $table->string('node_path', 128)->default('');
            $table->string('identifier_display', 64)->nullable();
            $table->string('identifier_validation', 64)->nullable();
            $table->string('image_path_lg', 128)->nullable();
            $table->string('image_path_sm', 128)->nullable();

            $table->string('description', 512)->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->trooperstamps();

            // Prevent duplicate entries
            $table->unique(['parent_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tt_organizations');
    }
};
