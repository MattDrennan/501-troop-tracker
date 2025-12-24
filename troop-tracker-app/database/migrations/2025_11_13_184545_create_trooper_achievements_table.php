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
        Schema::create('tt_trooper_achievements', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('trooper_id')
                ->constrained('tt_troopers')
                ->cascadeOnDelete();

            // $table->dateTime('member_since');
            $table->integer('trooper_rank')->nullable();

            // Squad completion
            $table->boolean('trooped_all_squads')->default(false);

            // First troop
            $table->boolean('first_troop_completed')->default(false);

            // Troop count milestones
            $table->boolean('trooped_10')->default(false);
            $table->boolean('trooped_25')->default(false);
            $table->boolean('trooped_50')->default(false);
            $table->boolean('trooped_75')->default(false);
            $table->boolean('trooped_100')->default(false);
            $table->boolean('trooped_150')->default(false);
            $table->boolean('trooped_200')->default(false);
            $table->boolean('trooped_250')->default(false);
            $table->boolean('trooped_300')->default(false);
            $table->boolean('trooped_400')->default(false);
            $table->boolean('trooped_500')->default(false);
            $table->boolean('trooped_501')->default(false);

            $table->float('volunteer_hours')->default(0);
            $table->float('direct_funds')->default(0);
            $table->float('indirect_funds')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate entries
            $table->unique(columns: ['trooper_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tt_trooper_achievements');
    }
};
