<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{
    function index(Request $request)
    {
        $cars = Car::with('images')
            ->where('status', Car::STATUS_AVAILABLE)
            ->latest()
            ->get();
        return Inertia::render('Frontend/HomePage', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'cars' => $cars,
        ]);
    }
}
