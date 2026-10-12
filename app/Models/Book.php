<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    // Model reprezentuje knihu a automaticky se mapuje na tabulku 'books' v databázi.

    /**
     * Definice relace 1:N (Jedna kniha může mít více výpůjček).
     * Metoda vrací všechny záznamy o výpůjčkách, které se vážou k této knize.
     */
    public function loans() {
        return $this->hasMany(Loan::class);
    }
}
