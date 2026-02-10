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
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('department_id')
                ->nullable()
                ->after('sex')
                ->constrained('departments')
                ->nullOnDelete();
            
            // Remove the old department text column
            $table->dropColumn('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            
            // Restore the old department text column
            $table->string('department')->nullable()->after('sex');
        });
    }
};
