<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStanjeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stanje', function (Blueprint $table) {
            $table->boolean('pausalac')->default(false)->after('broj_clanova_domacinstva');
            $table->float('pausalac_kubika', 4, 2)->nullable()->after('broj_clanova_domacinstva');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
