<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('cashbox_id')
                ->nullable()
                ->after('amount')
                ->constrained('cashboxes')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['cashbox_id']);
            $table->dropColumn('cashbox_id');
        });
    }

};
