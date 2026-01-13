<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_rol');
            $table->string('nombre_rol', 50)->unique(); // 'Rector', 'Docente'
            $table->timestamps(); // Opcional: created_at, updated_at
        });

        // Insertar roles iniciales
        DB::table('roles')->insert([
            ['nombre_rol' => 'Rector'],
            ['nombre_rol' => 'Docente'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
