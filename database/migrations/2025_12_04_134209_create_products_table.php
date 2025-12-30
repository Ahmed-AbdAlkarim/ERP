<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // اسم الصنف
            $table->enum('type',['laptop','mobile','accessory','spare','service'])->default('accessory'); // النوع
            $table->string('model')->nullable();             // الموديل
            $table->string('sku')->unique();                 // باركود/كود داخلي
            $table->decimal('purchase_price', 15, 2)->default(0); // سعر الشراء
            $table->decimal('selling_price', 15, 2)->default(0);  // سعر البيع
            $table->decimal('min_allowed_price', 15, 2)->nullable(); // أقل سعر مسموح
            $table->string('warranty_type')->nullable();    // نوع الضمان (سنوات/نوع)
            $table->integer('warranty_period_days')->nullable(); // مدة الضمان بالأيام
            $table->enum('condition',['new','used','imported'])->default('new');
            $table->string('image')->nullable();            // مسار الصورة
            $table->integer('stock')->default(0);           // المخزون الحالي
            $table->integer('reorder_level')->default(5);   // مستوى التنبيه عند النفاد
            $table->boolean('is_service')->default(false);  // لو الخدمة وليس منتج مادي
            $table->text('notes')->nullable();
            $table->decimal('avg_cost', 15, 2)->default(0); // متوسط تكلفة (يُحدَّث من فواتير الشراء)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
