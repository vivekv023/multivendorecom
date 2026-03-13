<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'vendor_id', 'min_price', 'max_price']);
        
        $products = $this->productService->getAllProducts($filters);
        $cartCount = $this->productService->getCartCount();

        return view('products.index', compact('products', 'cartCount'));
    }

    public function show(int $product)
    {
        $product = $this->productService->getProductById($product);
        
        if (!$product) {
            abort(404);
        }

        $relatedProducts = $this->productService->getRelatedProducts($product);

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
