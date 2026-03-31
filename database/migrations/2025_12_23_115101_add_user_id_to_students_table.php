<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Tambah kolom user_id setelah kolom id
            $table->unsignedBigInteger('user_id')->nullable()->after('id');

            // Buat foreign key ke tabel users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Tambah index untuk performa query
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
