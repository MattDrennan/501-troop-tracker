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
        Schema::create('tt_settings', function (Blueprint $table)
        {
            $table->string('key', 32)->primary();         // Unique setting key
            $table->string('value', 256);        // Raw value (string, number, boolean, etc.)

            // $table->integer('lastidtrooper')->default(0);
            // $table->integer('lastidevent')->default(0);
            // $table->integer('lastidlink')->default(0);
            // $table->boolean('site_closed')->default(1);
            // $table->integer('signupclosed')->default(0);
            // $table->integer('lastnotification')->default(0);
            // $table->integer('support_goal')->default(0);
            // $table->integer('notifyevent')->default(0);
            // $table->dateTime('syncdate')->useCurrent();
            // $table->dateTime('syncdaterebels')->useCurrent();
            // $table->text('sitemessage')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->trooperstamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tt_settings');
    }
};
