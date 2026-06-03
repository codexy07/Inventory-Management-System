<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created, updated, deleted
            $table->string('model_type'); // e.g. Product, Category, Supplier, StockIn, StockOut, User
            $table->unsignedBigInteger('model_id');
            $table->string('description');
            $table->json('changes')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};
