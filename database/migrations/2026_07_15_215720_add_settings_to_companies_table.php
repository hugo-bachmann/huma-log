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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('legal_name')->nullable()->after('name');
            $table->string('siret')->nullable()->after('legal_name');
            $table->string('ape_code')->nullable()->after('siret');
            $table->string('address')->nullable()->after('ape_code');
            $table->string('phone')->nullable()->after('address');
            $table->string('timezone')->default('Europe/Paris')->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['legal_name', 'siret', 'ape_code', 'address', 'phone', 'timezone']);
        });
    }
};
