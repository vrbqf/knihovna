<!DOCTYPE html> <!-- Definice typu dokumentu a HTML5 standardu -->
<html lang="cs"> <!-- Otevření HTML elementu s nastavením českého jazyka -->
<head> <!-- Začátek hlavičky HTML dokumentu -->
    <meta charset="UTF-8"> <!-- Nastavení kódování znaků na UTF-8 pro podporu diakritiky -->
    <title>Detail knihy: {{ $book->title }}</title> <!-- Dynamický titulek stránky obsahující název konkrétní knihy -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Načtení CSS frameworku Bootstrap 5 přes CDN -->
</head> <!-- Konec hlavičky HTML dokumentu -->
<body class="container py-4"> <!-- Otevření těla stránky s Bootstrap třídami pro kontejner a vertikální odsazení -->

<!-- VÝPIS ÚSPĚŠNÝCH A CHYBOVÝCH ZPRÁV -->
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
    @endregion
    <!-- KONEC VÝPISU ZPRÁV -->

    <p><a href="{{ route('books.index') }}" class="btn btn-secondary">&larr; Zpět na seznam</a></p><p><a href="{{ route('books.index') }}" class="btn btn-secondary">&larr; Zpět na seznam</a></p> <!-- Odstavec s tlačítkem pro návrat na hlavní stránku se seznamem knih -->
<h1>{{ $book->title }}</h1> <!-- Hlavní nadpis 1. úrovně zobrazující název aktuální knihy -->
<p><strong>Autor:</strong> {{ $book->author }} | <strong>ISBN:</strong> {{ $book->isbn }} | <strong>Dostupné výtisky:</strong> {{ $book->available_copies }}</p> <!-- Informační odstavec s detailem autora, ISBN a počtu dostupných výtisků -->

<hr> <!-- Vodorovná oddělovací čára pro lepší přehlednost stránky -->

<h3>Založit výpůjčku</h3> <!-- Podnadpis sekce pro vytvoření nové výpůjčky -->
<form action="{{ route('loans.store') }}" method="POST" class="card p-3 bg-light mb-4"> <!-- Otevření POST formuláře pro odeslání výpůjčky zabaleného do Bootstrap karty -->
    @csrf <!-- Bezpečnostní Laravel direktiva generující povinný CSRF token -->
    <input type="hidden" name="book_id" value="{{ $book->id }}"> <!-- Skryté pole obsahující ID právě zobrazené knihy -->
    <div class="mb-2"> <!-- Obalující div pro vstupní pole ID čtenáře s dolním odsazením -->
        <label>ID čtenáře:</label> <!-- Popisek vstupního pole pro ID čtenáře -->
        <input type="number" name="reader_id" class="form-control" placeholder="Zadejte ID čtenáře z DB" required> <!-- Číselné vstupní pole pro ID čtenáře (povinné) -->
    </div> <!-- Konec obalu pro ID čtenáře -->
    <div class="mb-2"> <!-- Obalující div pro datum výpůjčky s dolním odsazením -->
        <label>Datum výpůjčky:</label> <!-- Popisek pro datum výpůjčky -->
        <input type="datetime-local" name="borrowed_at" class="form-control" required> <!-- Vstupní pole pro datum a čas vypůjčení (povinné) -->
    </div> <!-- Konec obalu pro datum výpůjčky -->
    <div class="mb-2"> <!-- Obalující div pro termín vrácení s dolním odsazením -->
        <label>Termín vrácení (due_at):</label> <!-- Popisek pro termín vrácení -->
        <input type="datetime-local" name="due_at" class="form-control" required> <!-- Vstupní pole pro datum a čas vrácení (povinné) -->
    </div> <!-- Konec obalu pro termín vrácení -->
    <button type="submit" class="btn btn-primary">Půjčit knihu</button> <!-- Tlačítko pro odeslání formuláře a založení výpůjčky -->
</form> <!-- Konec formuláře pro výpůjčku -->

<h3>Historie výpůjček této knihy</h3> <!-- Podnadpis sekce s historií výpůjček -->
<table class="table table-striped"> <!-- Otevření HTML tabulky se střídavě podbarvenými řádky (Bootstrap) -->
    <thead> <!-- Otevření hlavičky tabulky -->
    <tr><th>Čtenář</th><th>Vypůjčeno</th><th>Má vrátit</th><th>Vráceno</th><th>Akce</th></tr> <!-- Hlavičkový řádek definující sloupce tabulky výpůjček -->
    </thead> <!-- Konec hlavičky tabulky -->
    <tbody> <!-- Otevření těla tabulky pro dynamické výpůjčky -->
    @foreach($book->loans as $loan) <!-- Blade cyklus procházející všechny výpůjčky spojené s touto knihou -->
    <tr> <!-- Otevření řádku tabulky pro aktuální výpůjčku -->
        <td>{{ $loan->reader->name ?? 'Neznámý' }}</td> <!-- Buňka s celým jménem čtenáře nebo textem 'Neznámý', pokud čtenář neexistuje -->
        <td>{{ $loan->borrowed_at }}</td> <!-- Buňka zobrazující datum a čas vypůjčení -->
        <td>{{ $loan->due_at }}</td> <!-- Buňka zobrazující termín, do kdy má být kniha vrácena -->
        <td>{{ $loan->returned_at ?? 'Zatím nevráceno' }}</td> <!-- Buňka zobrazující datum vrácení nebo text, že kniha ještě vrácena nebyla -->
        <td> <!-- Otevření buňky pro akce (tlačítko vrácení) -->
            @if(!$loan->returned_at) <!-- Blade podmínka: zobrazit formulář pro vrácení pouze v případě, že kniha ještě nebyla vrácena -->
            <form action="{{ route('loans.return', $loan->id) }}" method="POST"> <!-- POST formulář směřující na akci vrácení konkrétní výpůjčky -->
                @csrf <!-- Bezpečnostní Laravel direktiva pro CSRF token -->
                <button type="submit" class="btn btn-sm btn-danger">Vrátit knihu</button> <!-- Červené tlačítko pro potvrzení vrácení knihy -->
            </form> <!-- Konec formuláře pro vrácení -->
            @endif <!-- Konec podmínky pro vrácení -->
        </td> <!-- Konec buňky pro akce -->
    </tr> <!-- Konec řádku tabulky -->
    @endforeach <!-- Konec smyčky cyklu foreach -->
    </tbody> <!-- Konec těla tabulky -->
</table> <!-- Konec HTML tabulky -->
</body> <!-- Konec těla stránky -->
</html> <!-- Konec celého HTML dokumentu -->
