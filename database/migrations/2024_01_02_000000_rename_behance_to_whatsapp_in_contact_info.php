<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_info', function (Blueprint $table) {
            $table->renameColumn('behance', 'whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('contact_info', function (Blueprint $table) {
            $table->renameColumn('whatsapp', 'behance');
        });
    }
};
