<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration // Asegúrate que la clase se defina así si usas Laravel 8+
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // $table->id(); // Si usaste Breeze, puede que tengas esto
            $table->id('id_usuario_app'); // O tu nombre personalizado
            $table->string('name'); // O 'nombre'
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // --- Asegúrate que esta línea esté ANTES de la línea foreign ---
            $table->unsignedBigInteger('id_rol'); // <--- DEFINICIÓN DE LA COLUMNA

            $table->rememberToken();
            $table->timestamps();

            // --- La llave foránea se define DESPUÉS de la columna ---
            $table->foreign('id_rol')->references('id_rol')->on('roles'); // <--- DEFINICIÓN DE LA LLAVE FORÁNEA
        });

        // Resto de las tablas creadas por Breeze (password_reset_tokens, failed_jobs, etc.)
        // Asegúrate que no haya otra definición de la tabla 'users' más abajo.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
             $table->string('email')->primary();
             $table->string('token');
             $table->timestamp('created_at')->nullable();
         });

         Schema::create('sessions', function (Blueprint $table) {
             $table->string('id')->primary();
             $table->foreignId('user_id')->nullable()->index();
             $table->string('ip_address', 45)->nullable();
             $table->text('user_agent')->nullable();
             $table->longText('payload');
             $table->integer('last_activity')->index();
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};