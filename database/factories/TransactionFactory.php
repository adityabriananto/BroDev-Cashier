<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(5000, 500000);
        $tax = (int) ($subtotal * 0.11);
        $total = $subtotal + $tax;

        return [
            'transaction_code' => 'TXS-'.strtoupper(Str::random(8)),
            'payment_method' => fake()->randomElement(['Cash', 'QRIS', 'Bank Transfer']),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ];
    }
}
