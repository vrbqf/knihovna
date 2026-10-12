<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\Book;
use App\Models\Reader;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    // Určuje, pro který model tato továrna (factory) generuje data
    protected $model = Loan::class;

    /**
     * Definice výchozích hodnot pro generování testovací výpůjčky.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Automaticky přiřadí existující knihu (nebo vytvoří novou, pokud žádná není)
            'book_id' => Book::factory(),

            // Automaticky přiřadí existujícího čtenáře (nebo vytvoří nového)
            'reader_id' => Reader::factory(),

            // Vygeneruje náhodné datum výpůjčky za poslední měsíc
            'borrowed_at' => $this->faker->dateTimeBetween('-1 month', 'now'),

            // Termín vrácení o 14 dní později
            'due_at' => $this->faker->dateTimeBetween('now', '+2 weeks'),

            // Skutečné datum vrácení (v 50 % případů null, jinak náhodné datum)
            'returned_at' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
