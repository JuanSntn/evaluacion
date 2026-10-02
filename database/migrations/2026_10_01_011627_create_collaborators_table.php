<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('nombre');
            $table->text('correo');
            $table->char('correo_hash', 64);
            $table->text('rfc');
            $table->char('rfc_hash', 64);
            $table->text('domicilio_fiscal');
            $table->text('curp');
            $table->char('curp_hash', 64);
            $table->text('numero_seguridad_social');
            $table->char('nss_hash', 64);
            $table->date('fecha_inicio_laboral');
            $table->text('tipo_contrato');
            $table->text('departamento');
            $table->text('puesto');
            $table->text('salario_diario');
            $table->text('salario');
            $table->foreignId('state_id')->constrained('states')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'correo_hash']);
            $table->unique(['user_id', 'rfc_hash']);
            $table->unique('curp_hash');
            $table->unique('nss_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborators');
    }
};
