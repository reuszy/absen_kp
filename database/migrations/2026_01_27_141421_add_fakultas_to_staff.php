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
        Schema::dropIfExists('dosens');

        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('nama_fakultas');
            $table->timestamps();
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('unit_kerja');
            $table->foreignId('faculty_id')->nullable()->after('nama')->constrained('faculties')->onDelete('set null');
        });
    }


    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            //
        });
    }
};
