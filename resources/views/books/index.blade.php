<!DOCTYPE html> <!-- Definice typu dokumentu a HTML5 standardu -->
<html lang="cs"> <!-- Otevření HTML elementu s nastavením českého jazyka -->
<head> <!-- Začátek hlavičky HTML dokumentu obsahující metadata -->
    <meta charset="UTF-8"> <!-- Nastavení kódování znaků na UTF-8 pro podporu diakritiky -->
    <title>Evidování knihovny</title> <!-- Titulek stránky zobrazený v záložce prohlížeče -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Načtení externího CSS frameworku Bootstrap 5 přes CDN -->
</head> <!-- Konec hlavičky HTML dokumentu -->
<body class="container py-4"> <!-- Otevření těla stránky s Bootstrap třídami pro kontejner a vertikální odsazení -->

<h1>Knihovna</h1> <!-- Hlavní nadpis 1. úrovně zobrazující název aplikace -->

@if(session('success')) <!-- Blade podmínka: pokud existuje v session flash zpráva klíče 'success' -->
<div class="alert alert-success">{{ session('success') }}</div> <!-- Vykreslení zeleného Bootstrap boxu s textem úspěšné zprávy -->
@endif <!-- Konec podmínky pro úspěšnou zprávu -->
@if($errors->any()) <!-- Blade podmínka: pokud validace selhala a existují nějaké chyby -->
<div class="alert alert-danger"> <!-- Otevření červeného Bootstrap boxu pro chybová hlášení -->
    @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach <!-- Cyklus vypisující každou jednotlivou chybovou hlášku na nový řádek -->
</div> <!-- Konec chybového boxu -->
@endif <!-- Konec podmínky pro chybová hlášení -->

<p><a href="{{ route('loans.overdue') }}" class="btn btn-warning">Zobrazit výpůjčky po termínu</a></p> <!-- Odstavec s tlačítkem odkazujícím na přehled zpožděných výpůjček -->

<!-- Filtr -->
<form method="GET" action="{{ route('books.index') }}" class="input-group mb-4"> <!-- Otevření GET formuláře směřujícího na index pro filtrování -->
    <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Hledat podle názvu nebo autora..."> <!-- Vstupní textové pole pro vyhledávání zachovávající zadanou hodnotu -->
    <button class="btn btn-outline-primary" type="submit">Filtrovat</button> <!-- Tlačítko pro odeslání filtračního formuláře -->
    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">Reset</a> <!-- Tlačítko pro zrušení filtru a návrat na čistý seznam -->
</form> <!-- Konec formuláře pro filtr -->

<!-- Formulář pro přidání knihy -->
<div class="card mb-4 p-3 bg-light"> <!-- Otevření Bootstrap karty s odsazením a světlým pozadím -->
    <h3>Přidat novou knihu</h3> <!-- Podnadpis sekce pro přidání knihy -->
    <form action="{{ route('books.store') }}" method="POST"> <!-- Otevření POST formuláře pro odeslání dat nové knihy -->
        @csrf <!-- Bezpečnostní Laravel direktiva generující CSRF token -->
        <div class="mb-2"><input type="text" name="title" class="form-control" placeholder="Název knihy" required></div> <!-- Vstupní pole pro název knihy (povinné) -->
        <div class="mb-2"><input type="text" name="author" class="form-control" placeholder="Autor" required></div> <!-- Vstupní pole pro autora knihy (povinné) -->
        <div class="mb-2"><input type="text" name="isbn" class="form-control" placeholder="ISBN (přesně 13 znaků)" maxlength="13" required></div> <!-- Vstupní pole pro ISBN omezené na 13 znaků (povinné) -->
        <div class="mb-2"><input type="number" name="available_copies" class="form-control" placeholder="Dostupné výtisky" min="0" required></div> <!-- Číselné pole pro počet dostupných výtisků s minimem 0 (povinné) -->
        <button type="submit" class="btn btn-success">Uložit knihu</button> <!-- Tlačítko pro odeslání formuláře a uložení knihy -->
    </form> <!-- Konec formuláře pro uložení knihy -->
</div> <!-- Konec Bootstrap karty -->

<!-- Seznam knih -->
<h3>Seznam knih</h3> <!-- Podnadpis sekce seznamu -->
<table class="table table-bordered"> <!-- Otevření HTML tabulky s ohraničením podle Bootstrapu -->
    <thead> <!-- Otevření hlavičky tabulky -->
    <tr><th>Název</th><th>Autor</th><th>ISBN</th><th>Dostupné</th><th>Akce</th></tr> <!-- Hlavičkový řádek definující sloupce tabulky -->
    </thead> <!-- Konec hlavičky tabulky -->
    <tbody> <!-- Otevření těla tabulky pro dynamická data -->
    @foreach($books as $book) <!-- Blade cyklus procházející kolekci knih předanou z kontroleru -->
    <tr> <!-- Otevření řádku tabulky pro aktuální knihu -->
        <td>{{ $book->title }}</td> <!-- Bunka obsahující název knihy -->
        <td>{{ $book->author }}</td> <!-- Bunka obsahující autora knihy -->
        <td>{{ $book->isbn }}</td> <!-- Bunka obsahující ISBN knihy -->
        <td>{{ $book->available_copies }}</td> <!-- Bunka obsahující počet dostupných výtisků -->
        <td><a href="{{ route('books.show', $book->id) }}" class="btn btn-sm btn-info">Detail & Výpůjčky</a></td> <!-- Bunka s tlačítkem vedoucím na detail a správu výpůjček dané knihy -->
    </tr> <!-- Konec řádku tabulky -->
    @endforeach <!-- Konec smyčky cyklu foreach -->
    </tbody> <!-- Konec těla tabulky -->
</table> <!-- Konec HTML tabulky -->
</body> <!-- Konec těla stránky -->
</html> <!-- Konec celého HTML dokumentu -->
