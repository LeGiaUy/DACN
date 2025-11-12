<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $cartCount = 0;
        
        if ($user) {
            $cartCount = \App\Models\CartItem::where('user_id', $user->id)->sum('quantity');
        }

        // Share categories for navigation menu
        $categories = \App\Models\Category::orderBy('name')->limit(10)->get();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'cartCount' => $cartCount,
            'categories' => $categories,
        ];
    }
}
