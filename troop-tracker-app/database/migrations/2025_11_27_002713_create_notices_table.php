<?php

use App\Enums\NoticeType;
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
        Schema::create('tt_notices', function (Blueprint $table)
        {
            $table->id();

            // Organization assignment
            $table->foreignId('organization_id')
                ->nullable()
                ->constrained('tt_organizations')
                ->cascadeOnDelete();

            // Time window
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();

            // Core fields
            $table->string('title', 128);
            $table->string('type', 16)->default(NoticeType::Info);
            $table->text('message');

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
        Schema::dropIfExists('tt_notices');
    }
};
