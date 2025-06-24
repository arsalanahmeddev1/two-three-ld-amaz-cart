<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Brand;
use Modules\Product\Entities\Attribute;
use Modules\Product\Entities\AttributeValue;
use Modules\Product\Entities\ProductVariations;
use Modules\Seller\Entities\SellerProduct;
use Modules\Seller\Entities\SellerProductSKU;
use App\Repositories\FilterRepository;

class ShopController extends Controller
{
    protected $filterRepo;
    
    public function __construct(FilterRepository $filterRepo)
    {
        $this->filterRepo = $filterRepo;
    }
    
    public function index(Request $request)
    {
        $sort_by = null;
        $paginate = 20;
        
        if ($request->has('sort_by')) {
            $sort_by = $request->sort_by;
            $data['sort_by'] = $request->sort_by;
        }
        if ($request->has('paginate')) {
            $paginate = $request->paginate;
            $data['paginate'] = $request->paginate;
        }
        
        // Get products
        $products = Product::with(['variations', 'skus', 'tags'])
            ->where('status', 1)
            ->whereHas('seller', function($q){
                $q->where('status', 1);
            });
            
        if ($sort_by != null) {
            if ($sort_by == 'new') {
                $products = $products->latest();
            } else if ($sort_by == 'old') {
                $products = $products->oldest();
            } else if ($sort_by == 'alpha_asc') {
                $products = $products->orderBy('product_name', 'ASC');
            } else if ($sort_by == 'alpha_desc') {
                $products = $products->orderBy('product_name', 'DESC');
            } else if ($sort_by == 'low_to_high') {
                $products = $products->orderBy('min_sell_price', 'ASC');
            } else if ($sort_by == 'high_to_low') {
                $products = $products->orderBy('max_sell_price', 'DESC');
            }
        } else {
            $products = $products->latest();
        }
        
        $data['products'] = $products->paginate($paginate);
        $data['seller'] = SellerProduct::where('status', 1)->first();
        // Get categories
        $data['CategoryList'] = Category::where('status', 1)
            ->where('parent_id', 0)
            ->withCount('products')
            ->latest()
            ->get();
        $data['categories'] = Category::where('status', 1)
            ->where('parent_id', 0)
            ->withCount('products')
            ->latest()
            ->get();     
        // Get brands
        $data['brands'] = Brand::where('status', 1)
            ->latest()
            ->get();
            
        // Get attributes
        $data['attributeLists'] = Attribute::with('values')->where('status', 1)->get();
        
        // Get color attribute
        $data['color'] = Attribute::with('values')->where('name', 'Color')->first();
        
        // Get min and max prices using FilterRepository
        $min_price_lowest = $this->filterRepo->filterProductMinPrice();
        $max_price_highest = $this->filterRepo->filterProductMaxPrice();
        
        if($min_price_lowest == null) {
            $min_price_lowest = 0;
        }
        if($max_price_highest == null) {
            $max_price_highest = 0;
        }
        
        $data['min_price_lowest'] = $min_price_lowest;
        $data['max_price_highest'] = $max_price_highest;
        //return $data['categories'];
        return view(theme('pages.shop'), $data);
    }
    
    public function fetchPagenateData(Request $request)
    {
        $sort_by = null;
        $paginate = 20;
        
        if ($request->has('sort_by')) {
            $sort_by = $request->sort_by;
            $data['sort_by'] = $request->sort_by;
        }
        if ($request->has('paginate')) {
            $paginate = $request->paginate;
            $data['paginate'] = $request->paginate;
        }
        
        // Get products
        $products = Product::with(['variations', 'skus', 'tags'])
            ->where('status', 1)
            ->whereHas('seller', function($q){
                $q->where('status', 1);
            });
        
        // Apply sorting
        if ($sort_by != null) {
            if ($sort_by == 'new') {
                $products = $products->latest();
            } else if ($sort_by == 'old') {
                $products = $products->oldest();
            } else if ($sort_by == 'alpha_asc') {
                $products = $products->orderBy('product_name', 'ASC');
            } else if ($sort_by == 'alpha_desc') {
                $products = $products->orderBy('product_name', 'DESC');
            } else if ($sort_by == 'low_to_high') {
                $products = $products->orderBy('min_sell_price', 'ASC');
            } else if ($sort_by == 'high_to_low') {
                $products = $products->orderBy('max_sell_price', 'DESC');
            }
        } else {
            $products = $products->latest();
        }
        
        // Paginate the results
        $products = $products->paginate($paginate);
        
        return view(theme('partials.shop_paginate_data'), compact('products', 'sort_by', 'paginate'));
    }
    
    public function filter(Request $request)
    {
        $filterType = $request->filterType;
        
        // Start with a base query
        $products = SellerProduct::with('product', 'skus')
            ->activeSeller()
            ->select('seller_products.*')
            ->join('products', function ($query) {
                $query->on('products.id', '=', 'seller_products.product_id')
                    ->where('products.status', 1);
            })
            ->distinct('seller_products.id');
        
        // Apply filters
        foreach ($filterType as $filter) {
            if ($filter['id'] == 'cat') {
                $products = $products->whereHas('product', function ($query) use ($filter) {
                    $query->whereHas('categories', function ($q) use ($filter) {
                        $q->whereIn('category_id', $filter['value']);
                    });
                });
            }
            
            if ($filter['id'] == 'brand') {
                $products = $products->whereHas('product', function ($query) use ($filter) {
                    $query->whereIn('brand_id', $filter['value']);
                });
            }
            
            if ($filter['id'] == 'price_range') {
                $min_price = $filter['value'][0];
                $max_price = $filter['value'][1];
                
                $products = $products->where(function ($query) use ($min_price, $max_price) {
                    $query->whereBetween('min_sell_price', [$min_price, $max_price])
                        ->orWhereBetween('max_sell_price', [$min_price, $max_price]);
                });
            }
            
            if (strpos($filter['id'], 'attribute_') !== false) {
                $attribute_id = str_replace('attribute_', '', $filter['id']);
                $products = $products->whereHas('product', function ($query) use ($attribute_id, $filter) {
                    $query->whereHas('variations', function ($q) use ($attribute_id, $filter) {
                        $q->where('attribute_id', $attribute_id)
                          ->whereIn('attribute_value_id', $filter['value']);
                    });
                });
            }
            
            if ($filter['id'] == 'rating') {
                $rating = $filter['value'][0];
                $products = $products->where('avg_rating', '>=', $rating);
            }
        }
        
        // Sort and paginate
        $sort_by = $request->sort_by ?? 'new';
        $paginate_by = $request->paginate ?? 9;
        
        $products = $this->filterRepo->sortAndPaginate($products, $sort_by, $paginate_by);
        
        return view(theme('partials.shop_paginate_data'), compact('products'));
    }
}



