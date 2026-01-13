<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrosAccesoTable extends Migration
{
    public function up()
    {
        Schema::create('registros_acceso', function (Blueprint $table) {
            $table->id('id_registro');
            // Quién accedió (ID del biométrico, puede ser null si es apertura remota sin ID específico)
            $table->string('id_usuario_biometrico', 50)->nullable()->index();
            // Quién realizó la acción en la app (si aplica)
            $table->unsignedBigInteger('id_usuario_app_accion')->nullable();
            $table->timestamp('timestamp_evento');
            $table->string('tipo_evento', 100); // Ej: 'Acceso Concedido (Huella)', 'Puerta Abierta (WebApp Rector)', 'Acceso Denegado'
            $table->string('origen', 50); // Ej: 'Biometrico', 'WebApp Rector', 'WebApp Docente'
            $table->text('detalles_adicionales')->nullable(); // Para info extra del evento ISAPI
            $table->timestamps();

            $table->foreign('id_usuario_app_accion')->references('id_usuario_app')->on('users')->onDelete('set null');
            // Nota: No hay foreign key directa a personal_en_biometrico por id_usuario_biometrico
            // porque no todos los eventos podrían venir de usuarios registrados en nuestra app
            // (ej. una tarjeta no registrada o una huella fallida).
        });
    }

    public function down()
    {
        Schema::dropIfExists('registros_acceso');
    }
}