<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyResultColumnInResultsTable extends Migration
{
    public function up()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->string('result')->change();
        });
    }

    public function down()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->integer('result')->change();
        });
    }
}

