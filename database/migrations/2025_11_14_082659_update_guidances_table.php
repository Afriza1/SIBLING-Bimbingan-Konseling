<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guidances', function (Blueprint $table) {
            $table->string('proof_of_guidance')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('guidances', function (Blueprint $table) {
            $table->binary('proof_of_guidance')->nullable()->change();
        });
    }
};
