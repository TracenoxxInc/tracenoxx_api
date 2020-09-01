<?php

use App\Models\Transaction\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $transactionsCount = 100;

            factory(Transaction::class, $transactionsCount)->create();
            $this->command->info("{$transactionsCount} transactions have been created successfully");
        } catch (Exception $e) {
            $this->command->error("Some items violet the unique constraint, those are skiped");
        }
    }
}
