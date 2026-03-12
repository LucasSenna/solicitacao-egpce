<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $stateScope = 'state';
        $municipalityScope = 'municipality';

        if (! Schema::hasColumn('training_requests', 'request_scope')) {
            Schema::table('training_requests', function (Blueprint $table): void {
                $table->string('request_scope', 20)
                    ->default('state')
                    ->after('protocol');

                $table->index('request_scope');
            });
        }

        DB::table('training_requests')
            ->whereNotIn('request_scope', [$stateScope, $municipalityScope])
            ->orWhereNull('request_scope')
            ->update(['request_scope' => $stateScope]);

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(<<<'SQL'
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conname = 'training_requests_request_scope_check'
    ) THEN
        ALTER TABLE training_requests
        ADD CONSTRAINT training_requests_request_scope_check
        CHECK (request_scope IN ('state', 'municipality'));
    END IF;
END $$;
SQL);
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE training_requests DROP CONSTRAINT IF EXISTS training_requests_request_scope_check');
        }

        if (Schema::hasColumn('training_requests', 'request_scope')) {
            Schema::table('training_requests', function (Blueprint $table): void {
                $table->dropIndex('training_requests_request_scope_index');
                $table->dropColumn('request_scope');
            });
        }
    }
};
