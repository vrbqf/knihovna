<?php

use App\Http\Controllers\LibraryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LibraryController::class, 'index'])->name('books.index');
Route::post('/books', [LibraryController::class, 'storeBook'])->name('books.store');
Route::get('/books/{id}', [LibraryController::class, 'showBook'])->name('books.show');

Route::post('/loans', [LibraryController::class, 'storeLoan'])->name('loans.store');
Route::post('/loans/{id}/return', [LibraryController::class, 'returnLoan'])->name('loans.return');
Route::get('/overdue', [LibraryController::class, 'overdueLoans'])->name('loans.overdue');
