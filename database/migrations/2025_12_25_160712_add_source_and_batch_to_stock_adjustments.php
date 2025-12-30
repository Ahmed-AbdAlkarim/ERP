<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->enum('source', ['single','excel'])->default('single');
            $table->unsignedBigInteger('batch_id')->nullable(); // رقم الجرد الجماعي
        });
    }

    public function down()
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropColumn(['source','batch_id']);
        });
    }
};
