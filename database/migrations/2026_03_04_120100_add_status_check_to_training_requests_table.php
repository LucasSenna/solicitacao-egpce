<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('training_requests')
            ->whereNotIn('status', ['nao_iniciado', 'em_andamento', 'realizado', 'nao_realizado'])
            ->update(['status' => 'nao_iniciado']);

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(<<<'SQL'
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conname = 'training_requests_status_check'
    ) THEN
        ALTER TABLE training_requests
        ADD CONSTRAINT training_requests_status_check
        CHECK (status IN ('nao_iniciado', 'em_andamento', 'realizado', 'nao_realizado'));
    END IF;
END $$;
SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE training_requests DROP CONSTRAINT IF EXISTS training_requests_status_check');
    }
};
