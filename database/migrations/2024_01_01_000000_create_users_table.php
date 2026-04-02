<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('full_name', 100)->default('');
            $table->text('bio')->nullable();
            $table->string('profile_photo', 255)->nullable();
            $table->string('badge', 100)->default('Digital Marketing Specialist');
            $table->string('headline', 255)->default('Meningkatkan Brand Awareness & Konversi Anda.');
            $table->text('description')->nullable();
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
