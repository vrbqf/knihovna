<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Výpůjčky po termínu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
<p><a href="{{ route('books.index') }}" class="btn btn-secondary">&larr; Zpět na úvod</a></p>
<h1>Výpůjčky po termínu</h1>

@if($overdue->isEmpty())
    <p class="text-success">Žádné výpůjčky po termínu.</p>
@else
    <table class="table table-danger">
        <thead>
        <tr><th>Kniha</th><th>Čtenář</th><th>Vypůjčeno</th><th>Měl vrátit</th></tr>
        </thead>
        <tbody>
        @foreach($overdue as $loan)
            <tr>
                <td>{{ $loan->book->title }}</td>
                <td>{{ $loan->reader->name }}</td>
                <td>{{ $loan->borrowed_at }}</td>
                <td>{{ $loan->due_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
</body>
</html>
