<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $products = DB::table('products')->select('id', 'quantity')->get();
        $now = now();
        foreach ($products as $p) {
            if ((int) ($p->quantity ?? 0) <= 0) {
                continue;
            }

            $exists = DB::table('product_variants')
                ->where('product_id', $p->id)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('product_variants')->insert([
                'product_id' => $p->id,
                'color' => null,
                'size' => null,
                'sku' => null,
                'quantity' => (int) $p->quantity,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // No-op: do not delete backfilled data on rollback
    }
};


