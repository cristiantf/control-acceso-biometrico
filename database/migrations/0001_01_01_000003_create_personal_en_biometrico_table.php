<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalEnBiometricoTable extends Migration
{
    public function up()
    {
        Schema::create('personal_en_biometrico', function (Blueprint $table) {
            $table->id('id_personal');
            // Relación con el usuario de la aplicación web
            $table->unsignedBigInteger('id_usuario_app')->unique();
            // ID que usa el dispositivo Hikvision (puede ser string o int según el dispositivo)
            $table->string('id_usuario_biometrico', 50)->unique();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamps(); // Opcional: created_at, updated_at

            $table->foreign('id_usuario_app')->references('id_usuario_app')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('personal_en_biometrico');
    }
}