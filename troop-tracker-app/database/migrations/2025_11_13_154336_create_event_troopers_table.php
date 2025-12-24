<?php

use App\Enums\TrooperEventStatus;
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
        Schema::create('tt_event_troopers', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('event_id')
                ->constrained('tt_events')
                ->cascadeOnDelete();
            $table->foreignId('trooper_id')
                ->constrained('tt_troopers')
                ->cascadeOnDelete();

            $table->foreignId('costume_id')
                ->nullable()
                ->constrained('tt_costumes')
                ->cascadeOnDelete();
            $table->foreignId('backup_costume_id')
                ->nullable()
                ->constrained('tt_costumes')
                ->cascadeOnDelete();

            $table->foreignId('added_by_trooper_id')
                ->nullable()
                ->constrained('tt_troopers')
                ->cascadeOnDelete();

            $table->string('status', 16)->default(TrooperEventStatus::None->value)->index();

            // $table->string('note')->default('');

            $table->timestamps();
            $table->softDeletes();
            $table->trooperstamps();

            // Prevent duplicate entries
            $table->unique(columns: ['event_id', 'trooper_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tt_event_troopers');
    }
};
