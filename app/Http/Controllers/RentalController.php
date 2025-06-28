<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::with(['user', 'rentalItems.console', 'rentalItems.game'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('rentals.index', compact('rentals'));
    }

    public function show(Rental $rental)
    {
        $rental->load(['user', 'rentalItems.console', 'rentalItems.game']);
        return view('rentals.show', compact('rental'));
    }

    public function updateStatus(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,active,completed,cancelled'
        ]);

        $rental->update($validated);

        return redirect()->route('rentals.index')
            ->with('success', 'Status penyewaan berhasil diperbarui.');
    }

    public function create(Request $request)
    {
        $consoleId = $request->query('console');
        $console = null;
        if ($consoleId) {
            $console = \App\Models\Console::find($consoleId);
        }
        return view('rentals.create', compact('console'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'console_id' => 'required|exists:consoles,id_console',
            'rental_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:rental_date',
            'qty' => 'required|integer|min:1',
        ]);

        $console = \App\Models\Console::findOrFail($validated['console_id']);
        $user = auth()->user() ?? \App\Models\User::first(); // fallback user
        $days = (new \Carbon\Carbon($validated['rental_date']))->diffInDays(new \Carbon\Carbon($validated['return_date']));
        $total = $console->daily_rate * $validated['qty'] * $days;

        $rental = \App\Models\Rental::create([
            'user_id' => $user->id,
            'rental_date' => $validated['rental_date'],
            'return_date' => $validated['return_date'],
            'total_price' => $total,
            'status' => 'pending',
        ]);

        \App\Models\RentalItem::create([
            'rental_id' => $rental->id,
            'id_console' => $console->id_console,
            'qty' => $validated['qty'],
            'price' => $console->daily_rate * $days * $validated['qty'],
        ]);

        return redirect()->route('rentals.index')->with('success', 'Penyewaan berhasil dibuat!');
    }
} 