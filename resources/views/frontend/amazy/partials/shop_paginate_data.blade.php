<div class="product_toolbox">
    <div class="product_toolbox_left">
        <div class="toolbox_item">
            <p>{{ __('common.showing') }} {{ $products->firstItem() }} {{ __('common.to') }} {{ $products->lastItem() }} {{ __('common.of') }} {{ $products->total() }} {{ __('common.results') }}</p>
        </div>
    </div>
    <div class="product_toolbox_right">
        <div class="toolbox_item toolbox_item_sorter">
            <select class="amaz_nice_select" id="product_short_list">
                <option value="new" @if(isset($sort_by) && $sort_by == 'new') selected @endif>{{ __('common.new') }}</option>
                <option value="old" @if(isset($sort_by) && $sort_by == 'old') selected @endif>{{ __('common.old') }}</option>
                <option value="alpha_asc" @if(isset($sort_by) && $sort_by == 'alpha_asc') selected @endif>{{ __('common.name_a_to_z') }}</option>
                <option value="alpha_desc" @if(isset($sort_by) && $sort_by == 'alpha_desc') selected @endif>{{ __('common.name_z_to_a') }}</option>
                <option value="low_to_high" @if(isset($sort_by) && $sort_by == 'low_to_high') selected @endif>{{ __('common.price_low_to_high') }}</option>
                <option value="high_to_low" @if(isset($sort_by) && $sort_by == 'high_to_low') selected @endif>{{ __('common.price_high_to_low') }}</option>
            </select>
        </div>
        <div class="toolbox_item toolbox_item_paginate">
            <select class="amaz_nice_select" id="paginate_by">
                <option value="12" @if(isset($paginate) && $paginate == '12') selected @endif>{{ __('common.show') }} 12</option>
                <option value="16" @if(isset($paginate) && $paginate == '16') selected @endif>{{ __('common.show') }} 16</option>
                <option value="20" @if(isset($paginate) && $paginate == '20') selected @endif>{{ __('common.show') }} 20</option>
                <option value="24" @if(isset($paginate) && $paginate == '24') selected @endif>{{ __('common.show') }} 24</option>
                <option value="50" @if(isset($paginate) && $paginate == '50') selected @endif>{{ __('common.show') }} 50</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    @foreach($products as $product)
    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
        <div class="product_widget5 mb_30">
            <div class="product_thumb">
                <a href="{{ url('/product/'.$product->slug) }}" class="thumb">
                    <img src="{{ showImage($product->thumbnail_image_source) }}" alt="{{ $product->product_name }}">
                </a>
                <div class="product_action">
                    <a href="#" class="add_to_wishlist" data-product_id="{{ $product->id }}">
                        <i class="far fa-heart"></i>
                    </a>
                </div>
            </div>
            <div class="product_content">
                <a href="{{ url('/product/'.$product->slug) }}">
                    <h4>{{ $product->product_name }}</h4>
                </a>
                <div class="product_price d-flex align-items-center">
                    <span class="sale_price">{{ single_price($product->selling_price) }}</span>
                    @if($product->discount > 0)
                    <span class="discount_price">{{ single_price($product->skus->max('price')) }}</span>
                    @endif
                </div>
            </div>
            <div class="product_rating">
                <div class="review_star">
                    @php
                        $rating = 0;
                        $total_review = 0;
                        
                        if(method_exists($product, 'reviews') && $product->reviews->count() > 0){
                            $reviews = $product->reviews->where('status', 1)->pluck('rating');
                            if(count($reviews) > 0){
                                $value = 0;
                                foreach($reviews as $review){
                                    $value += $review;
                                }
                                $rating = $value/count($reviews);
                                $total_review = count($reviews);
                            }
                        }
                    @endphp
                    <!-- Star rating display code -->
                </div>
                <p>{{ $total_review }} {{ __('defaultTheme.review') }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row">
    <div class="col-12">
        <div class="pagination_part">
            <nav aria-label="Page navigation">
                @if ($products->lastPage() > 1)
                    <ul class="pagination">
                        <li class="page-item {{ ($products->currentPage() == 1) ? ' disabled' : '' }}">
                            <a class="page-link" href="{{ $products->url(1) }}" aria-label="Previous">
                                <i class="ti-arrow-left"></i>
                            </a>
                        </li>
                        @for ($i = 1; $i <= $products->lastPage(); $i++)
                            <li class="page-item {{ ($products->currentPage() == $i) ? ' active' : '' }}">
                                <a class="page-link" href="{{ $products->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ ($products->currentPage() == $products->lastPage()) ? ' disabled' : '' }}">
                            <a class="page-link" href="{{ $products->url($products->currentPage()+1) }}" aria-label="Next">
                                <i class="ti-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                @endif
            </nav>
        </div>
    </div>
</div>


