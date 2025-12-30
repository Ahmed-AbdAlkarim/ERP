<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('stock_adjustment_batches', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('file_name');
        });
    }

    public function down()
    {
        Schema::table('stock_adjustment_batches', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
};
