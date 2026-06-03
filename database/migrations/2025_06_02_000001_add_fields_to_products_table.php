<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->unique()->default('')->after('name');
            $table->foreignId('supplier_id')->nullable()->after('category_id')->constrained('suppliers')->nullOnDelete();
            $table->string('warehouse_location')->nullable()->after('price');
            $table->string('image')->nullable()->after('warehouse_location');
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active')->after('image');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['sku', 'supplier_id', 'warehouse_location', 'image', 'status']);
        });
    }
};
