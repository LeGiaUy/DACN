<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    /**
     * Display the user's account management page.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $user->load(['orders' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }, 'cartItems']);
        
        return Inertia::render('User/Account/Index', [
            'user' => $user,
        ]);
    }
}

