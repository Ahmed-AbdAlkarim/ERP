<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_adjustment_batches', function (Blueprint $table) {
            $table->id();
            $table->string('file_name'); // اسم ملف الاكسيل
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->unsignedBigInteger('created_by'); // اللي رفع الملف
            $table->unsignedBigInteger('approved_by')->nullable(); // الادمن اللي اعتمد
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_adjustment_batches');
    }
};
