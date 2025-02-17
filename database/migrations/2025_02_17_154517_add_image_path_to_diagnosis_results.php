<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('diagnosis_results', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('final_diagnosis');
        });
    }

    public function down()
    {
        Schema::table('diagnosis_results', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
