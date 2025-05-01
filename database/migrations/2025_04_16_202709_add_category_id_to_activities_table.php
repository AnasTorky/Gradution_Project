<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('activities', function (Blueprint $table) {
        // Add the category_id column if it doesn't exist
        $table->unsignedBigInteger('category_id')->after('id');

        // Add the foreign key constraint
        $table->foreign('category_id')
              ->references('id')
              ->on('categories')
              ->onDelete('cascade'); // Optional: cascade delete
    });
}

public function down()
{
    Schema::table('activities', function (Blueprint $table) {
        // Drop the foreign key first
        $table->dropForeign(['category_id']);

        // Then drop the column
        $table->dropColumn('category_id');
    });
}
};
