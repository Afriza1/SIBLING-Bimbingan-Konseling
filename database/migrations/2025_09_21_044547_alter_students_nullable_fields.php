<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('nisn', 20)->nullable()->change();
            $table->string('gender', 20)->nullable()->change();
            $table->string('place_of_birth', 50)->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('religion', 30)->nullable()->change();
            $table->string('phone_number', 20)->nullable()->change();
            $table->string('address', 255)->nullable()->change();
            $table->date('admission_date')->nullable()->change();
            $table->string('guardian_name', 50)->nullable()->change();
            $table->string('guardian_phone_number', 20)->nullable()->change();
            $table->unsignedBigInteger('status_id')->nullable()->change();
            $table->string('email', 255)->nullable()->change();
            $table->string('password', 255)->nullable()->change();
        });
    }

    public function down(): void
    {

    }
};
