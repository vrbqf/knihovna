<?php

namespace App\Http\Controllers;

use App\Models\Book; // Import modelu pro práci s knihami v databázi
use App\Models\Reader; // Import modelu pro práci se čtenáři
use App\Models\Loan; // Import modelu pro správu výpůjček
use Illuminate\Http\Request; // Import třídy pro zachycení HTTP požadavků
// Správná třída pro Laravel kontroler je Controller, nikoliv Response
use App\Http\Controllers\Controller;

class LibraryController extends Controller
{
    /**
     * Zobrazení seznamu všech knih s možností filtrování podle názvu nebo autora.
     */
    public function index(Request $request)
    {
        // Získání vyhledávacího řetězce 'search' z GET požadavku z URL
        $search = $request->input('search');

        // Načtení knih z databáze s bezpečně obaleným filtrujícím blokem v uzávěře
        $books = Book::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                // Vyhledání shody v názvu knihy (case-insensitive v SQLite)
                $q->where('title', 'like', "%{$search}%")
                    // Nebo ve jménu autora knihy
                    ->orWhere('author', 'like', "%{$search}%");
            });
        })->get(); // Provedení SQL dotazu a získání kolekce výsledků

        // Vrácení Blade šablony 'books.index' a předání nalezených knih i hledaného výrazu
        return view('books.index', compact('books', 'search'));
    }

    /**
     * Uložení nové knihy do databáze ručně (bez použití Book::create a bez nutnosti $fillable).
     */
    public function storeBook(Request $request)
    {
        // Validační pravidla (ISBN musí mít přesně 13 znaků a být unikátní v tabulce books, počty kopií nesmí být záporné)
        $validated = $request->validate([
            'title' => 'required|string|max:255', // Název je povinný řetězec do 255 znaků
            'author' => 'required|string|max:255', // Autor je povinný řetězec do 255 znaků
            'isbn' => 'required|string|size:13|unique:books,isbn', // ISBN musí mít 13 znaků a nesmí se opakovat
            'available_copies' => 'required|integer|min:0', // Počet kopií musí být celé číslo větší nebo rovno 0
        ]);

        // Ruční vytvoření nové prázdné instance modelu Book
        $book = new Book();
        // Přiřazení validovaného názvu do sloupce title
        $book->title = $validated['title'];
        // Přiřazení validovaného autora do sloupce author
        $book->author = $validated['author'];
        // Přiřazení validovaného ISBN do sloupce isbn
        $book->isbn = $validated['isbn'];
        // Přiřazení počtu dostupných kopií
        $book->available_copies = $validated['available_copies'];
        $book->save(); // Uložení nové knihy do databáze

        // Přesměrování zpět na předchozí stránku s úspěšnou flash zprávou
        return back()->with('success', 'Kniha byla úspěšně přidána.');
    }

    /**
     * Zobrazení detailu konkrétní knihy včetně historie jejích výpůjček.
     */
    public function showBook($id)
    {
        // Načtení konkrétní knihy podle ID včetně vnořené relace výpůjček a čtenářů, nebo vyhození chyby 404
        $book = Book::with('loans.reader')->findOrFail($id);

        // Vrácení Blade šablony pro detail knihy
        return view('books.show', compact('book'));
    }

    /**
     * Uložení nového čtenáře do databáze ručně (bez použití Reader::create a bez nutnosti $fillable).
     */
    public function storeReader(Request $request)
    {
        // Validace vstupních dat pro čtenáře (jméno a unikátní email)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:readers,email',
        ]);

        // Ruční vytvoření instance nového čtenáře
        $reader = new Reader();
        $reader->name = $validated['name'];
        $reader->email = $validated['email'];
        $reader->save(); // Uložení čtenáře do databáze

        // Návrat zpět s potvrzující zprávou
        return back()->with('success', 'Čtenář byl úspěšně zaregistrován.');
    }

    /**
     * Založení nové výpůjčky s kontrolou dostupnosti, duplicit a termínů.
     */
    public function storeLoan(Request $request)
    {
        // Validace vstupů včetně kontroly, že data nejsou v minulosti
        $request->validate([
            'book_id' => 'required|exists:books,id', // Kniha musí existovat
            'reader_id' => 'required|exists:readers,id', // Čtenář musí existovat

            // 'after_or_equal:today' zaručí, že datum vypůjčení nemůže být v minulosti (musí být dnes nebo později)
            'borrowed_at' => 'required|date|after_or_equal:today',

            // 'after:borrowed_at' zajistí, že vrácení je až po vypůjčení
            'due_at' => 'required|date|after:borrowed_at',
        ], [
            // Vlastní chybové hlášky v češtině, které se uživateli zobrazí v červeném boxu:
            'borrowed_at.after_or_equal' => 'Datum vypůjčení nemůže být v minulosti.',
            'due_at.after' => 'Datum vrácení musí být až po datu vypůjčení.',
        ]);

        // Zbytek vašeho kódu pro kontrolu dostupnosti a uložení...
        $book = Book::findOrFail($request->book_id);

        if ($book->available_copies <= 0) {
            return back()->withErrors(['error' => 'Kniha nemá žádné dostupné výtisky k výpůjčce.']);
        }

        $activeLoan = Loan::where('book_id', $request->book_id)
            ->where('reader_id', $request->reader_id)
            ->whereNull('returned_at')
            ->exists();

        if ($activeLoan) {
            return back()->withErrors(['error' => 'Tento čtenář má již tuto knihu vypůjčenou a nevrátil ji.']);
        }

        \DB::transaction(function () use ($request, $book) {
            $loan = new Loan();
            $loan->book_id = $request->book_id;
            $loan->reader_id = $request->reader_id;
            $loan->borrowed_at = $request->borrowed_at;
            $loan->due_at = $request->due_at;
            $loan->save();

            $book->decrement('available_copies');
        });

        return back()->with('success', 'Výpůjčka byla úspěšně založena.');
    }

    /**
     * Vrácení vypůjčené knihy.
     */
    public function returnLoan($id)
    {
        // Vyhledání konkrétní výpůjčky podle ID nebo vyhození chyby 404
        $loan = Loan::findOrFail($id);

        // Kontrola, zda už kniha nebyla vrácena v minulosti
        if ($loan->returned_at) {
            return back()->withErrors(['error' => 'Tato kniha již byla vrácena.']);
        }

        // Databázová transakce pro bezpečné zaznamenání vrácení a přičtení výtisku zpět
        \DB::transaction(function () use ($loan) {
            // Nastavení aktuálního data a času jako okamžiku vrácení knihy
            $loan->update(['returned_at' => now()]);

            // Zvýšení počtu dostupných výtisků knihy zpět o 1
            $loan->book()->increment('available_copies');
        });

        // Návrat zpět s informací o úspěšném vrácení
        return back()->with('success', 'Kniha byla vrácena.');
    }

    /**
     * Zobrazení přehledu všech výpůjček, u kterých vypršel termín vrácení a nebyly vráceny.
     */
    public function overdueLoans()
    {
        // Načtení výpůjček včetně relací na knihu a čtenáře
        $overdue = Loan::with(['book', 'reader'])
            ->whereNull('returned_at') // Filtruje knihy, které ještě nebyly vráceny
            ->where('due_at', '<', now()) // Filtruje výpůjčky, jejichž termín vrácení je již v minulosti
            ->get(); // Získání výsledků

        // Vrácení Blade šablony pro přehled pošlých/zpožděných výpůjček
        return view('loans.overdue', compact('overdue'));
    }
}
