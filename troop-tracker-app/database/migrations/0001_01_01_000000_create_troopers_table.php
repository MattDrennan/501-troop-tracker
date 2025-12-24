<?php

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
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
        Schema::create('tt_troopers', function (Blueprint $table)
        {
            $table->id();

            $table->string('name', 128);
            $table->string('phone', 32)->nullable();
            $table->string('email', 256)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('username', 128);
            $table->string('password', 256);
            $table->dateTime('last_active_at')->nullable();
            $table->string('membership_status', 16)->default(MembershipStatus::Pending->value);
            $table->string('membership_role', 16)->default(MembershipRole::Member->value);

            $table->boolean('instant_notification')->default(true);
            $table->boolean('attendance_notification')->default(true);
            $table->boolean('command_staff_notification')->default(true);

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tt_password_reset_tokens', function (Blueprint $table)
        {
            $table->string('email')->primary();
            $table->string('token', 256);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('tt_sessions', function (Blueprint $table)
        {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tt_troopers');
        Schema::dropIfExists('tt_password_reset_tokens');
        Schema::dropIfExists('tt_sessions');
    }
};
