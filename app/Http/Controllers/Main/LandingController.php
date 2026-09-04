<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    /**
     * The premium landing page with the product catalog.
     */
    public function index(): Response
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with('prices')
            ->get();

        return Inertia::render('Main/Landing', [
            'products' => ProductResource::collection($products)->resolve(),
        ]);
    }
}
