<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Brand;
use Modules\Product\Entities\Attribute;
use Modules\Product\Entities\ProductVariations;
use Modules\Seller\Entities\SellerProduct;
use Modules\Seller\Entities\SellerProductSKU;
use App\Repositories\FilterRepository;
use App\Services\FilterService;
use Modules\Product\Repositories\CategoryRepository;
use Modules\Product\Repositories\BrandRepository;
use Modules\Product\Entities\CategoryProduct;
use Modules\Product\Entities\ProductTag;
use \Modules\Product\Repositories\AttributeRepository;
use Modules\Setup\Entities\Tag;
use Modules\GiftCard\Entities\DigitalGiftCard;
use Modules\GiftCard\Entities\GiftCard;
use App\Models\SearchTerm;
use App\Models\User;





class ShopController extends Controller
{
    
    protected $filterService;
    protected $filterRepo;

    public function __construct(FilterRepository $filterRepo, FilterService $filterService)
    {
        $this->filterService = $filterService;
        $this->filterRepo = $filterRepo;
    }

    public function index(Request $request)
    {
        $slug = "all";
        // $request->validate([
        //     'item' => 'required'
        // ]);
        $category_id = 0;
        $sort_by = null;
        $paginate = 9;
        $data = [];
        
        if ($request->has('sort_by')) {
            $sort_by = $request->sort_by;
            $data['sort_by'] = $request->sort_by;
        }
        if ($request->has('paginate')) {
            
            $paginate = $request->paginate;
            $data['paginate'] = $request->paginate;
        }
        $item = $request->item;
        if ($item == 'category') {
            
            $catRepo = new CategoryRepository(new Category());
            $category = $catRepo->findBySlug($slug);
            if ($category) {
                $category_id = $category->id;
                $data['CategoryList'] = $catRepo->subcategory($category_id);
                $data['filter_name'] = $catRepo->show($category_id);
                $category_ids = $catRepo->getAllSubSubCategoryID($category_id);
                $category_ids[] = $category_id;
                $data['brandList'] = $this->filterService->filterBrandCategoryWise($category_id, $category_ids);
                $data['products'] = $this->filterService->filterProductCategoryWise($category_id, $category_ids, $sort_by, $paginate);
                $product_min_price = $this->filterService->filterProductMinPrice($data['products']->pluck('id')->toArray());
                $product_max_price = $this->filterService->filterProductMaxPrice($data['products']->pluck('id')->toArray());
                $product_min_price = $this->filterService->getConvertedMin($product_min_price);
                $product_max_price = $this->filterService->getConvertedMax($product_max_price);
                $data['min_price_lowest'] = $product_min_price;
                $data['max_price_highest'] = $product_max_price;
                $attributeRepo = new AttributeRepository;
                $data['attributeLists'] = $attributeRepo->getAttributeForSpecificCategory($category_id, $category_ids);
                $data['category_id'] = $category_id;
                $data['color'] = $attributeRepo->getColorAttributeForSpecificCategory($category_id, $category_ids);
            } else {
                return abort(404);
            }
        }

        if ($item == 'brand') {
            
            $brandRepo = new BrandRepository(new Brand());
            $brand = $brandRepo->findBySlug($slug);
            if ($brand) {
                $brand_id = $brand->id;
                $data['filter_name'] = $brandRepo->find($brand_id);
                $data['brand_id'] = $brand_id;
                $data['products'] = $this->filterService->filterProductBrandWise($brand_id, $sort_by, $paginate);
                $product_min_price = $this->filterService->filterProductMinPrice($data['products']->pluck('id')->toArray());
                $product_max_price = $this->filterService->filterProductMaxPrice($data['products']->pluck('id')->toArray());
                $product_min_price = $this->filterService->getConvertedMin($product_min_price);
                $product_max_price = $this->filterService->getConvertedMax($product_max_price);
                $data['min_price_lowest'] = $product_min_price;
                $data['max_price_highest'] = $product_max_price;
                $data['CategoryList'] = $this->filterService->filterCategoryBrandWise($brand_id);
                $attributeRepo = new AttributeRepository;
                $data['attributeLists'] = $attributeRepo->getAttributeForSpecificBrand($brand_id);
                $data['color'] = $attributeRepo->getColorAttributeForSpecificBrand($brand_id);
            } else {
                return abort(404);
            }
        }

        if ($item == 'product') {
            

            $result = $this->filterService->getSectionProducts($slug);
            $products = $result['products'];
            $section = $result['section'];
            $section_product_ids = $products->pluck('id')->toArray();
            $data['tag'] = $section->title;
            $data['item'] = $request->item;
            $data['section_name'] = $slug;
            $mainProducts = Product::where('products.status', 1)->select(['products.id', 'products.brand_id'])->join('seller_products', function ($q) use ($section_product_ids) {
                return $q->on('products.id', '=', 'seller_products.product_id')->whereRaw("seller_products.id in ('" . implode("','", $section_product_ids) . "')");
            });
            $main_product_ids = $mainProducts->pluck('id')->toArray();
            $brand_ids = $mainProducts->distinct('brand_id')->pluck('brand_id')->toArray();
            $category_ids = CategoryProduct::whereRaw("product_id in ('" . implode("','", $main_product_ids) . "')")->distinct()->pluck('category_id')->toArray();
            $data['CategoryList'] = Category::whereRaw("id in ('" . implode("','", $category_ids) . "')")->where('parent_id', 0)->where('status', 1)->take(10)->get();
            $data['brandList'] = Brand::whereRaw("id in ('" . implode("','", $brand_ids) . "')")->where('status', 1)->take(10)->get();
            $attribute_ids = ProductVariations::whereRaw("product_id in ('" . implode("','", $main_product_ids) . "')")->distinct()->pluck('attribute_id')->toArray();
            $data['attributeLists'] =  Attribute::with('values')->whereRaw("id in ('" . implode("','", $attribute_ids) . "')")->where('id', '>', 1)->where('status', 1)->take(1)->get();
            $data['color'] = Attribute::with('values')->whereRaw("id in ('" . implode("','", $attribute_ids) . "')")->where('id', 1)->where('status', 1)->first();
            $data['products'] = $this->filterService->sortAndPaginate($products, $sort_by, $paginate);
            $product_min_price = $this->filterService->filterProductMinPrice($products->pluck('id')->toArray());
            $product_max_price = $this->filterService->filterProductMaxPrice($products->pluck('id')->toArray());
            $product_min_price = $this->filterService->getConvertedMin($product_min_price);
            $product_max_price = $this->filterService->getConvertedMax($product_max_price);
            $data['min_price_lowest'] = $product_min_price;
            $data['max_price_highest'] = $product_max_price;
        }

        if ($item == 'tag') {
            
            $tag = Tag::where('name', $slug)->first();
            $mainProducts = ProductTag::where('tag_id', $tag->id);
            $main_product_ids = $mainProducts->pluck('product_id')->toArray();
            $brand_ids = Product::whereRaw("id in ('" . implode("','", $main_product_ids) . "')")->distinct('brand_id')->pluck('brand_id')->toArray();
            $data['tag'] = $tag->name;
            $data['tag_id'] = $tag->name;
            $category_ids = CategoryProduct::whereRaw("product_id in ('" . implode("','", $main_product_ids) . "')")->distinct()->pluck('category_id')->toArray();
            $data['CategoryList'] = Category::whereRaw("id in ('" . implode("','", $category_ids) . "')")->where('status', 1)->take(20)->get();
            $data['brandList'] = Brand::whereRaw("id in ('" . implode("','", $brand_ids) . "')")->where('status', 1)->take(20)->get();
            $attribute_ids = ProductVariations::whereRaw("product_id in ('" . implode("','", $main_product_ids) . "')")->distinct()->pluck('attribute_id')->toArray();
            $data['attributeLists'] =  Attribute::with('values')->whereRaw("id in ('" . implode("','", $attribute_ids) . "')")->where('id', '>', 1)->where('status', 1)->take(2)->get();
            $data['color'] = Attribute::with('values')->whereRaw("id in ('" . implode("','", $attribute_ids) . "')")->where('status', 1)->first();
            $products = SellerProduct::with('product')->whereRaw("product_id in ('" . implode("','", $main_product_ids) . "')")->activeSeller()->get();
            $giftCards = GiftCard::where('status', 1)->whereHas('tags', function ($q) use ($tag) {
                return $q->where('tag_id', $tag->id);
            })->select(['*', 'name as product_name', 'sku as slug'])->where('status', 1)->get();
            $product_min_price = $this->filterService->filterProductMinPrice($products->pluck('id')->toArray());
            $product_max_price = $this->filterService->filterProductMaxPrice($products->pluck('id')->toArray());
            $giftcard_min_price = $giftCards->min('selling_price') ?? 0;
            $giftcard_max_price = $giftCards->max('selling_price') ?? 0;
            $products = $products->merge($giftCards);
            $min_price = $this->filterService->getConvertedMin(min($product_min_price, $giftcard_min_price));
            $max_price = $this->filterService->getConvertedMax(max($product_max_price, $giftcard_max_price));
            $data['min_price_lowest'] = $min_price;
            $data['max_price_highest'] = $max_price;
            $data['products'] = $this->filterService->sortAndPaginate($products, $sort_by, $paginate);
        }

        if ($item == 'search') {
           
            $searchTerm = SearchTerm::where('keyword', $slug)->first();
            if ($searchTerm) {
                $count = $searchTerm->count;
                $searchTerm->count = 1 + $count;
                $searchTerm->save();
            } else {
                SearchTerm::create(['keyword' => $slug, 'count' => 1]);
            }
            $data['filter_name'] = "Search Query : " . "\" " . $slug . " \" ";
            $slugs = explode(' ', $slug);
            $mainProducts = Product::whereHas('tags', function ($q) use ($slugs) {
                return $q->where(function ($q) use ($slugs) {
                    foreach ($slugs as $slug) {
                        $q = $q->orWhere('name', 'LIKE', "%{$slug}%");
                    }
                    return $q;
                });
            });
            $main_product_ids = $mainProducts->pluck('id')->toArray();
            $brand_ids = $mainProducts->distinct('brand_id')->pluck('brand_id')->toArray();
            $giftCards = GiftCard::where('status', 1)->whereHas('tags', function ($q) use ($slugs) {
                return $q->where(function ($q) use ($slugs) {
                    foreach ($slugs as $slug) {
                        $q = $q->orWhere('name', 'LIKE', "%{$slug}%");
                    }
                    return $q;
                });
            })->select(['*', 'name as product_name', 'sku as slug'])->get();
            // $digitalgiftCards = DigitalGiftCard::Where('gift_name', 'LIKE', "%{$slug}%")->select(['*', 'gift_name as product_name'])->get();
            $category_ids = CategoryProduct::whereRaw("product_id in ('" . implode("','", $main_product_ids) . "')")->distinct()->pluck('category_id')->toArray();
            $data['CategoryList'] = Category::whereRaw("id in ('" . implode("','", $category_ids) . "')")->where('status', 1)->take(20)->get();
            $products = SellerProduct::activeSeller()->with('product')->select('seller_products.*')->join('products', function ($q) use ($main_product_ids, $slug) {
                $q->on('seller_products.product_id', '=', 'products.id');
            })->whereRaw("seller_products.product_id in ('" . implode("','", $main_product_ids) . "')")->orWhere('products.product_name', 'LIKE', "%{$slug}%")->where('seller_products.status', 1)->activeSeller()->orWhere('seller_products.product_name', 'LIKE', "%{$slug}%")->activeSeller()->where('seller_products.status', 1)->take(100)->get();
            $data['brandList'] = Brand::whereRaw("id in ('" . implode("','", $main_product_ids) . "')")->where('status', 1)->take(10)->get();
            $attribute_ids = ProductVariations::whereRaw("product_id in ('" . implode("','", $main_product_ids) . "')")->distinct()->pluck('attribute_id')->toArray();
            $data['attributeLists'] =  Attribute::with('values')->whereRaw("id in ('" . implode("','", $attribute_ids) . "')")->where('id', '>', 1)->where('status', 1)->take(2)->get();
            $data['color'] = Attribute::with('values')->whereRaw("id in ('" . implode("','", $attribute_ids) . "')")->where('status', 1)->first();
            $product_min_price = $this->filterService->filterProductMinPrice($products->pluck('id')->toArray());
            $product_max_price = $this->filterService->filterProductMaxPrice($products->pluck('id')->toArray());
            $giftcard_min_price = $giftCards->min('selling_price') ?? 0;
            $giftcard_max_price = $giftCards->max('selling_price') ?? 0;
            $min_price = $this->filterService->getConvertedMin(min($product_min_price, $giftcard_min_price));
            $max_price = $this->filterService->getConvertedMax(max($product_max_price, $giftcard_max_price));
            $data['min_price_lowest'] = $min_price;
            $data['max_price_highest'] = $max_price;
            $products = $products->merge($giftCards);
            // $products = $products->merge($digitalgiftCards);
            $data['keyword'] = $slug;
            $data['products'] = $this->filterService->sortAndPaginate($products, $sort_by, $paginate);
        }else{
            $searchTerm = SearchTerm::where('keyword', $slug)->first();
            /* if ($searchTerm) {
                $count = $searchTerm->count;
                $searchTerm->count = 1 + $count;
                $searchTerm->save();
            } else {
                SearchTerm::create(['keyword' => $slug, 'count' => 1]);
            }
            */    
            $data['filter_name'] = "Search Query : " . "\" " . $slug . " \" ";
            $slugs = explode(' ', $slug);
           /*$mainProducts = Product::whereHas('tags', function ($q) use ($slugs) {
                return $q->where(function ($q) use ($slugs) {
                    foreach ($slugs as $slug) {
                        $q = $q->orWhere('name', 'LIKE', "%{$slug}%");
                    }
                    return $q;
                });
            });*/
            $mainProducts = Product::query(); // or ->all() if you want all results immediately

            $main_product_ids = $mainProducts->pluck('id')->toArray();
            $brand_ids = $mainProducts->distinct('brand_id')->pluck('brand_id')->toArray();
            $giftCards = GiftCard::where('status', 1)->whereHas('tags', function ($q) use ($slugs) {
                return $q->where(function ($q) use ($slugs) {
                    foreach ($slugs as $slug) {
                        $q = $q->orWhere('name', 'LIKE', "%{$slug}%");
                    }
                    return $q;
                });
            })->select(['*', 'name as product_name', 'sku as slug'])->get();
            // $digitalgiftCards = DigitalGiftCard::Where('gift_name', 'LIKE', "%{$slug}%")->select(['*', 'gift_name as product_name'])->get();
            $category_ids = CategoryProduct::whereRaw("product_id in ('" . implode("','", $main_product_ids) . "')")->distinct()->pluck('category_id')->toArray();
            $data['CategoryList'] = Category::whereRaw("id in ('" . implode("','", $category_ids) . "')")->where('status', 1)->take(20)->get();
            $products = SellerProduct::activeSeller()->with('product')->select('seller_products.*')->join('products', function ($q) use ($main_product_ids, $slug) {
                $q->on('seller_products.product_id', '=', 'products.id');
            })->whereRaw("seller_products.product_id in ('" . implode("','", $main_product_ids) . "')")->orWhere('products.product_name', 'LIKE', "%{$slug}%")->where('seller_products.status', 1)->activeSeller()->orWhere('seller_products.product_name', 'LIKE', "%{$slug}%")->activeSeller()->where('seller_products.status', 1)->take(100)->get();
            $data['brandList'] = Brand::whereRaw("id in ('" . implode("','", $main_product_ids) . "')")->where('status', 1)->take(10)->get();
            $attribute_ids = ProductVariations::whereRaw("product_id in ('" . implode("','", $main_product_ids) . "')")->distinct()->pluck('attribute_id')->toArray();
            $data['attributeLists'] =  Attribute::with('values')->whereRaw("id in ('" . implode("','", $attribute_ids) . "')")->where('id', '>', 1)->where('status', 1)->take(2)->get();
            $data['color'] = Attribute::with('values')->whereRaw("id in ('" . implode("','", $attribute_ids) . "')")->where('status', 1)->first();
            $product_min_price = $this->filterService->filterProductMinPrice($products->pluck('id')->toArray());
            $product_max_price = $this->filterService->filterProductMaxPrice($products->pluck('id')->toArray());
            $giftcard_min_price = $giftCards->min('selling_price') ?? 0;
            $giftcard_max_price = $giftCards->max('selling_price') ?? 0;
            $min_price = $this->filterService->getConvertedMin(min($product_min_price, $giftcard_min_price));
            $max_price = $this->filterService->getConvertedMax(max($product_max_price, $giftcard_max_price));
            $data['min_price_lowest'] = $min_price;
            $data['max_price_highest'] = $max_price;
            $products = $products->merge($giftCards);
            // $products = $products->merge($digitalgiftCards);
            $data['keyword'] = $slug;
            $data['products'] = $this->filterService->sortAndPaginate($products, $sort_by, $paginate);
        }

        
        $data['seller'] = User::where('role_id', 5)
        
        ->where('is_active', 1)
        ->with(['SellerAccount', 'SellerBusinessInformation'])
        ->first();
        if (!$request->has('page')) {

            if (isset($data['products'])) {
                $data['products']->appends($request->except('page'));
            }

            if (session()->has('filterDataFromCat')) {
                session()->forget('filterDataFromCat');
            }

            return view(theme('pages.shop'), $data);
        } else {
            return view(theme('pages.shop'), $data);
        }
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

        // Get products using SellerProduct for consistency
        $products = SellerProduct::with(['product', 'skus', 'reviews'])
            ->activeSeller()
            ->select('seller_products.*')
            ->join('products', function ($query) {
                $query->on('products.id', '=', 'seller_products.product_id')
                    ->where('products.status', 1);
            })
            ->distinct('seller_products.id');

        // Use FilterRepository for consistent sorting and pagination
        $products = $this->filterRepo->sortAndPaginate($products, $sort_by, $paginate);

        $data = [
            'products' => $products,
            'sort_by' => $sort_by,
            'paginate' => $paginate
        ];

        return view(theme('partials.shop_paginate_data'), $data);
    }

    public function filter(Request $request)
    {
        try {
            $filterType = $request->filterType;

            // Log the incoming filter data for debugging
            Log::info('Shop Filter Request', [
                'filterType' => $filterType,
                'sort_by' => $request->sort_by,
                'paginate' => $request->paginate
            ]);

            // Start with a base query
            $products = SellerProduct::with(['product', 'skus', 'reviews'])
                ->activeSeller()
                ->select('seller_products.*')
                ->join('products', function ($query) {
                    $query->on('products.id', '=', 'seller_products.product_id')
                        ->where('products.status', 1);
                })
                ->distinct('seller_products.id');

            // Apply filters if they exist
            if ($filterType && is_array($filterType)) {
                foreach ($filterType as $filter) {
                    if (!isset($filter['id']) || !isset($filter['value'])) {
                        continue;
                    }

                    if ($filter['id'] == 'cat' && !empty($filter['value'])) {
                        $products = $products->whereHas('product', function ($query) use ($filter) {
                            $query->whereHas('categories', function ($q) use ($filter) {
                                $q->whereIn('category_id', $filter['value']);
                            });
                        });
                    }

                    if ($filter['id'] == 'brand' && !empty($filter['value'])) {
                        $products = $products->whereHas('product', function ($query) use ($filter) {
                            $query->whereIn('brand_id', $filter['value']);
                        });
                    }

                    if ($filter['id'] == 'price_range' && !empty($filter['value']) && count($filter['value']) >= 2) {
                        $min_price = $filter['value'][0];
                        $max_price = $filter['value'][1];

                        $products = $products->where(function ($query) use ($min_price, $max_price) {
                            $query->whereBetween('min_sell_price', [$min_price, $max_price])
                                ->orWhereBetween('max_sell_price', [$min_price, $max_price])
                                ->orWhere(function ($q) use ($min_price, $max_price) {
                                    $q->where('min_sell_price', '<=', $min_price)
                                        ->where('max_sell_price', '>=', $max_price);
                                });
                        });
                    }

                    if (strpos($filter['id'], 'attribute_') !== false && !empty($filter['value'])) {
                        $attribute_id = str_replace('attribute_', '', $filter['id']);
                        $products = $products->whereHas('product', function ($query) use ($attribute_id, $filter) {
                            $query->whereHas('variations', function ($q) use ($attribute_id, $filter) {
                                $q->where('attribute_id', $attribute_id)
                                    ->whereIn('attribute_value_id', $filter['value']);
                            });
                        });
                    }

                    if ($filter['id'] == 'rating' && !empty($filter['value'])) {
                        $rating = $filter['value'][0];
                        $products = $products->where('avg_rating', '>=', $rating);
                    }
                }
            }

            // Sort and paginate
            $sort_by = $request->sort_by ?? 'new';
            $paginate_by = $request->paginate ?? 12;

            $products = $this->filterRepo->sortAndPaginate($products, $sort_by, $paginate_by);

            // Pass additional data for the view
            $data = [
                'products' => $products,
                'sort_by' => $sort_by,
                'paginate' => $paginate_by
            ];

            return view(theme('partials.shop_paginate_data'), $data);
        } catch (\Exception $e) {
            Log::error('Shop Filter Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            // Return empty result on error
            $products = SellerProduct::where('id', 0)->paginate(12);
            return view(theme('partials.shop_paginate_data'), compact('products'));
        }
    }



    public function fetchFilterPagenateData(Request $request)
    {
        // This method can be used for pagination within filtered results
        return $this->filter($request);
    }
}
