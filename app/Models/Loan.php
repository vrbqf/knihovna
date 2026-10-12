<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory; // Umožňuje používat Eloquent Factories pro generování testovacích dat

    // Ochrana hromadného přiřazení (Mass Assignment) – tato pole lze bezpečně uložit najednou
    protected $fillable = [
        'book_id',     // ID vypůjčené knihy
        'reader_id',   // ID čtenáře, který si knihu půjčil
        'borrowed_at', // Datum vypůjčení
        'due_at',      // Termín vrácení
        'returned_at', // Skutečné datum vrácení (může být prázdné/null)
    ];

    /**
     * Definice inverzní relace N:1 (Každá výpůjčka patří právě jedné knize).
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Definice inverzní relace N:1 (Každá výpůjčka patří právě jednomu čtenáři).
     */
    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }
}


