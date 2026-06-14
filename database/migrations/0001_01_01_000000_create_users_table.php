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
        Schema::create('users', function (Blueprint $table) {

            $table->id();

            $table->string('username', 50)->unique();

            $table->string('email', 100)->unique();

            $table->string('full_name', 100)->nullable();

            $table->string('phone_number', 20)->nullable();

            $table->string('avatar_url', 255)->nullable();

            $table->boolean('is_using_custom_avatar')
                ->default(false);

            $table->string('password');

            $table->enum('role', [
                'user',
                'mentor',
                'admin'
            ])->default('user');

            $table->timestamp('last_login')
                ->nullable();

            $table->timestamp('email_verified_at')
                ->nullable();

            $table->rememberToken();

            $table->timestamp('created_at')
                ->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
