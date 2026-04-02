<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('cta_title', 200)->default('Mari Berkolaborasi!');
            $table->text('cta_description')->nullable();
            $table->string('email', 100)->default('');
            $table->string('linkedin', 255)->default('');
            $table->string('instagram', 255)->default('');
            $table->string('behance', 255)->default('');
            $table->string('tiktok', 255)->default('');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_info');
    }
};
