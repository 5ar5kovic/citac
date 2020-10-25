<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKorisnikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('korisnik', function (Blueprint $table) {
            $table->id();
            $table->string('ime', 100);
            $table->string('prezime', 100);
            $table->string('jmbg', 13)->nullable(true);
            $table->string('sifra_objekta', 100);
            $table->string('broj_vodomera', 100)->nullable(true);
            $table->integer('broj_clanova_domacinstva')->default(1);
            $table->boolean('pausalac')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('korisnik');
    }
}
