<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reader extends Model
{
    // Model reprezentuje čtenáře a automaticky se mapuje na tabulku 'readers' v databázi.

    /**
     * Definice relace 1:N (Jeden čtenář může mít více výpůjček).
     * Metoda vrací všechny záznamy o výpůjčkách, které tento čtenář uskutečnil.
     */
    public function loans() {
        return $this->hasMany(Loan::class);
    }
}
