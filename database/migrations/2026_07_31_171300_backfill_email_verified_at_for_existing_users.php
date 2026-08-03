<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Hasta ahora ningún registro enviaba correo de verificación (el listener no
 * estaba enganchado), así que cualquier cuenta ya creada tiene
 * email_verified_at en null sin que sea culpa del usuario. Si a partir de acá
 * se empieza a exigir verificación en alguna sección, estas cuentas quedarían
 * bloqueadas sin haber tenido nunca la oportunidad real de verificar. Se las
 * da por verificadas retroactivamente; solo los registros nuevos, que sí
 * reciben el correo, deberán verificar de verdad.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->whereNull('email_verified_at')->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // Irreversible a propósito: no hay forma de saber cuáles estaban
        // realmente sin verificar antes de este backfill.
    }
};
