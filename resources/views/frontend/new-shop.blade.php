@extends('layouts.app')
<div class="container py-5">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-12">
            <h2 class="text-center mb-4">{{ __('defaultTheme.latest_products') }}</h2>
        </div>
    </div>
    
    <div class="row custom_rowProduct">
        @if (count($products) > 0)
            @foreach ($products as $product)
                <div class="col-xl-4 col-md-6 col-sm-6 d-flex mb-4">
                    <div class="product_widget5 mb_30 style5 w-100 border shadow-sm rounded">
                        <div class="product_thumb_upper position-relative">
                            @php
                                // Determine product thumbnail and price
                                $thumbnail = $product->thumbnail_image ? showImage($product->thumbnail_image) : showImage(themeDefaultImg());
                                $price_qty = getProductDiscountedPrice($product);
                                $showData = [
                                    'name' => $product->product_name,
                                    'url' => singleProductURL($product->seller->slug, $product->slug),
                                    'price' => $price_qty,
                                    'thumbnail' => $thumbnail,
                                ];
                            @endphp
                            <a href="{{ singleProductURL($product->seller->slug, $product->slug) }}" class="thumb">
                                <img src="{{ $thumbnail }}" alt="{{ $product->product_name }}" title="{{ $product->product_name }}" class="img-fluid rounded-top">
                            </a>
                            <div class="product_badge position-absolute top-0 end-0 p-2">
                                @if ($product->hasDeal && $product->hasDeal->discount > 0)
                                    <span class="badge bg-danger">{{ __('defaultTheme.discount') }} -{{ $product->hasDeal->discount }}%</span>
                                @endif
                            </div>
                        </div>
                        <div class="product_meta p-3 text-center">
                            <span class="product_banding d-block mb-2 text-muted">{{ $product->brand->name ?? "Brand" }}</span>
                            <a href="{{ singleProductURL($product->seller->slug, $product->slug) }}" class="text-decoration-none">
                                <h5 class="text-truncate" title="{{ $product->product_name }}">{{ $product->product_name }}</h5>
                            </a>
                            <p class="price">{{ single_price($product->price) }}</p>
                        </div>
                        <div class="product_action d-flex justify-content-center p-2">
                            <a href="#" class="add_to_wishlist btn btn-outline-secondary mx-1" title="{{ __('defaultTheme.add_to_wishlist') }}">
                                <i class="far fa-heart"></i>
                            </a>
                            <a href="#" class="add_to_cart btn btn-primary mx-1" data-prod_info="{{ json_encode($showData) }}" title="{{ __('defaultTheme.add_to_cart') }}">
                                <i class="ti-bag"></i> {{ __('defaultTheme.add_to_cart') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center alert alert-danger">
                    {{ __('defaultTheme.no_product_found') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if ($products->lastPage() > 1)
        <x-pagination-component :items="$products" />
    @endif
</div>
