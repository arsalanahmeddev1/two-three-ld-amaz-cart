@extends('frontend.amazy.layouts.app')
@section('title')
    {{ __('common.shop') }}
@endsection
@section('content')
<!-- brand_banner::start  -->
<div class="brand_banner d-flex align-items-center">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="branding_text">{{ __('common.shop') }}</h3>
            </div>
        </div>
    </div>
</div>
<!-- brand_banner::end  -->
<!-- prodcuts_area ::start  -->
<div class="prodcuts_area ">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-4 col-xl-3">
                <div class="amaz_filter_sidebar mb_30">
                    <div class="accordion" id="accordionExample">
                        <!-- Categories -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    {{ __('common.categories') }}
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ul class="amaz_check_box">
                                        @foreach($categories as $category)
                                        <li>
                                            <label class="primary_checkbox d-flex">
                                                <input type="checkbox" class="category_checkbox getProductByChoice" data-id="cat" data-value="{{ $category->id }}">
                                                <span class="checkmark mr_10"></span>
                                                <span class="label_name">{{ $category->name }}</span>
                                                <span class="count_num">{{ $category->products_count }}</span>
                                            </label>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Brands -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    {{ __('common.brands') }}
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ul class="amaz_check_box">
                                        @foreach($brands as $brand)
                                        <li>
                                            <label class="primary_checkbox d-flex">
                                                <input type="checkbox" class="brand_checkbox getProductByChoice" data-id="brand" data-value="{{ $brand->id }}">
                                                <span class="checkmark mr_10"></span>
                                                <span class="label_name">{{ $brand->name }}</span>
                                            </label>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    {{ __('common.price_range') }}
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="filter_wrapper">
                                        <input type="hidden" id="min_price" value="{{ $min_price_lowest }}" />
                                        <input type="hidden" id="max_price" value="{{ $max_price_highest }}" />
                                        <div id="slider-range"></div>
                                        <div class="d-flex align-items-center prise_line">
                                            <button class="home10_primary_btn2 mr_20 mb-0 small_btn js-range-slider-0">{{ __('common.filter') }}</button>
                                            <span>{{ __('common.price') }}: </span> <input type="text" id="amount" readonly >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reset Button -->
                        <div class="accordion-item">
                            <div class="accordion-body">
                                <button type="button" class="amaz_primary_btn min_height_40 text-uppercase small_btn2 w-100" id="refresh_btn">{{ __('amazy.refresh') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Products -->
            <div id="dataWithPaginate" class="col-lg-8 col-xl-9">
                <div class="product_toolbox">
                    <div class="product_toolbox_left">
                        <div class="toolbox_item">
                            <select name="paginate_by" class="small_select" id="paginate_by">
                                <option value="12" @if(isset($paginate) && $paginate == "12") selected @endif>{{ __('common.show') }} 12</option>
                                <option value="16" @if(isset($paginate) && $paginate == "16") selected @endif>{{ __('common.show') }} 16</option>
                                <option value="20" @if(isset($paginate) && $paginate == "20") selected @endif>{{ __('common.show') }} 20</option>
                                <option value="24" @if(isset($paginate) && $paginate == "24") selected @endif>{{ __('common.show') }} 24</option>
                                <option value="28" @if(isset($paginate) && $paginate == "28") selected @endif>{{ __('common.show') }} 28</option>
                            </select>
                        </div>
                        <div class="toolbox_item">
                            <select name="sort_by" class="small_select" id="product_short_list">
                                <option value="new" @if(isset($sort_by) && $sort_by == "new") selected @endif>{{ __('common.new') }}</option>
                                <option value="old" @if(isset($sort_by) && $sort_by == "old") selected @endif>{{ __('common.old') }}</option>
                                <option value="alpha_asc" @if(isset($sort_by) && $sort_by == "alpha_asc") selected @endif>{{ __('defaultTheme.name_a_to_z') }}</option>
                                <option value="alpha_desc" @if(isset($sort_by) && $sort_by == "alpha_desc") selected @endif>{{ __('defaultTheme.name_z_to_a') }}</option>
                                <option value="low_to_high" @if(isset($sort_by) && $sort_by == "low_to_high") selected @endif>{{ __('defaultTheme.price_low_to_high') }}</option>
                                <option value="high_to_low" @if(isset($sort_by) && $sort_by == "high_to_low") selected @endif>{{ __('defaultTheme.price_high_to_low') }}</option>
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
                                    <span class="discount">-{{ $product->discount }}%</span>
                                    @endif
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
                                        @if($rating == 0)
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        @elseif($rating < 1 && $rating > 0)
                                            <i class="fas fa-star-half-alt"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        @elseif($rating <= 1 && $rating > 0)
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        @elseif($rating < 2 && $rating > 1)
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        @elseif($rating <= 2 && $rating > 1)
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        @elseif($rating < 3 && $rating > 2)
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        @elseif($rating <= 3 && $rating > 2)
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        @elseif($rating < 4 && $rating > 3)
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                            <i class="far fa-star"></i>
                                        @elseif($rating <= 4 && $rating > 3)
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        @elseif($rating < 5 && $rating > 4)
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        @endif
                                    </div>
                                    <p>{{ $total_review }} {{ __('defaultTheme.review') }}</p>
                                </div>
                                <div class="add_to_cart">
                                    <a href="#" class="add_to_cart_btn" data-product_id="{{ $product->id }}" data-seller_id="{{ $product->user_id }}" data-shipping_method="1" data-price="{{ $product->selling_price }}" data-tax="{{ $product->tax }}" data-discount="{{ $product->discount }}" data-product_type="{{ $product->product_type }}" data-shipping_cost="{{ $product->shipping_cost }}">
                                        {{ __('common.add_to_cart') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="pagination_part">
                            @if($products->lastPage() > 1)
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item {{ $products->currentPage() == 1 ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $products->url(1) }}" aria-label="Previous">
                                                <i class="ti-arrow-left"></i>
                                            </a>
                                        </li>
                                        @for ($i = 1; $i <= $products->lastPage(); $i++)
                                            <li class="page-item {{ $products->currentPage() == $i ? 'active' : '' }}">
                                                <a class="page-link" href="{{ $products->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endfor
                                        <li class="page-item {{ $products->currentPage() == $products->lastPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $products->url($products->currentPage()+1) }}" aria-label="Next">
                                                <i class="ti-arrow-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="add-product-to-cart-using-modal">
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function($){
        "use strict";
        
        $(document).ready(function(){
            // Product short
            $('#product_short_list').on('change', function(){
                $('#pre-loader').show();
                var value = $('#product_short_list').val();
                var paginate = $('#paginate_by').val();
                $.get("{{ route('frontend.shop.filter-data') }}", {sort_by:value, paginate:paginate}, function(data){
                    $('#dataWithPaginate').html(data);
                    $('#product_short_list').niceSelect();
                    $('#paginate_by').niceSelect();
                    $('#pre-loader').hide();
                });
            });
            
            // Paginate
            $('#paginate_by').on('change', function(){
                $('#pre-loader').show();
                var value = $('#product_short_list').val();
                var paginate = $('#paginate_by').val();
                $.get("{{ route('frontend.shop.filter-data') }}", {sort_by:value, paginate:paginate}, function(data){
                    $('#dataWithPaginate').html(data);
                    $('#product_short_list').niceSelect();
                    $('#paginate_by').niceSelect();
                    $('#pre-loader').hide();
                });
            });
            
            // Filter
            $(document).on('click', '.getProductByChoice', function(event){
                event.preventDefault();
                let type = $(this).data('id');
                let el = $(this).data('value');
                getFilteredProduct(type, el);
            });
            
            function getFilteredProduct(type, el){
                $('#pre-loader').show();
                var filterType = [];
                
                // Category filter
                if(type == 'cat'){
                    filterType.push({
                        id: 'cat',
                        value: getCheckedBoxes('category_checkbox')
                    });
                }
                
                // Brand filter
                if(type == 'brand'){
                    filterType.push({
                        id: 'brand',
                        value: getCheckedBoxes('brand_checkbox')
                    });
                }
                
                // Price range filter
                if(type == 'price_range'){
                    filterType.push({
                        id: 'price_range',
                        value: [parseInt($('#min_price').val()), parseInt($('#max_price').val())]
                    });
                }
                
                // Attribute filter
                if(type == 'attribute'){
                    $('.attribute_checkbox:checked').each(function() {
                        var attr_id = $(this).data('id');
                        var attr_val = $(this).val();
                        
                        // Check if attribute already exists in filterType
                        var existingAttr = filterType.find(item => item.id === 'attribute_' + attr_id);
                        
                        if(existingAttr){
                            existingAttr.value.push(attr_val);
                        } else {
                            filterType.push({
                                id: 'attribute_' + attr_id,
                                value: [attr_val]
                            });
                        }
                    });
                }
                
                // Rating filter
                if(type == 'rating'){
                    filterType.push({
                        id: 'rating',
                        value: [el]
                    });
                }
                
                $.post('/shop/filter', {_token:'{{ csrf_token() }}', filterType:filterType}, function(data){
                    $('#dataWithPaginate').html(data);
                    $('#product_short_list').niceSelect();
                    $('#paginate_by').niceSelect();
                    $('#pre-loader').hide();
                });
            }
            
            function getCheckedBoxes(checkboxClass){
                var checkboxes = document.getElementsByClassName(checkboxClass);
                var checkboxesChecked = [];
                for (var i=0; i<checkboxes.length; i++) {
                    if (checkboxes[i].checked) {
                        checkboxesChecked.push(checkboxes[i].value);
                    }
                }
                return checkboxesChecked;
            }
            
            // Price slider
            $(function () {
                var minVal = parseInt($('#min_price').val());
                var maxVal = parseInt($('#max_price').val());
                $("#slider-range").slider({
                    range: true,
                    min: minVal,
                    max: maxVal,
                    values: [minVal, maxVal],
                    slide: function (event, ui) {
                        $("#amount").val(ui.values[0] + " - " + ui.values[1]);
                        $("#amount").data('value', ui.values[0] + "-" + ui.values[1]);
                    },
                });
                $("#amount").val(
                    $("#slider-range").slider("values", 0) + " - " + $("#slider-range").slider("values", 1)
                );
                $("#amount").data('value',
                    $("#slider-range").slider("values", 0) + "-" + $("#slider-range").slider("values", 1)
                );
            });
            
            // Price filter button
            $(document).on('click', '.js-range-slider-0', function(event){
                var price_range = $("#amount").data('value').split('-');
                $('#min_price').val(price_range[0]);
                $('#max_price').val(price_range[1]);
                getFilteredProduct("price_range", price_range);
            });
            
            // Refresh button
            $('#refresh_btn').on('click', function(){
                location.reload();
            });
        });
    })(jQuery);
</script>
@endpush
@include(theme('partials.add_to_cart_script'))
@include(theme('partials.add_to_compare_script'))

@push('styles')
    <style>
        .member_info_iner{
            --userProfile: 150px;
        }
        .member_info .member_info_iner {
            margin-top: -50px;
            z-index: 2;
            position: relative;
        }
        .profile_content{
            max-width: calc(100% - var(--userProfile));
            flex: 0 0 100%;
        }
        .profile_img_div {
            height: var(--userProfile);
            width: var(--userProfile);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f7f9;
            padding: 10px;
        }
        .profile_img_div img {
            max-width: 150px;
            max-height: 150px;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .member_info .member_info_text {
            padding-left: 30px;
            margin-top: 87px;
        }
        html[dir=rtl] .member_info .member_info_text {
            padding-right: 30px;
        }
        .member_info .member_info_text .member_info_details {
            margin-bottom: 12px;
            align-items: center;
        }
        .member_info .member_info_text .member_info_details h4 {
            font-size: 24px;
            margin-bottom: 0;
        }
        .member_info .member_info_text .member_info_details span {
            margin: 0 15px;
        }
        .member_info .member_info_text .member_info_details {
            margin-bottom: 12px;
            align-items: center;
        }
        @media only screen and (max-width: 991px){
            .member_info .member_info_iner {
                flex-wrap: wrap;
            }

            .profile_content {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .member_info .member_info_text {
                margin-top: 30px;
                padding-left: 0px;
            }
            .member_info_iner {
                --userProfile: 120px;
            }
        }
        @media only screen and (max-width: 767px){
            .member_info_iner {
                --userProfile: 100px;
            }
            .member_info .member_info_text {
                margin-top: 20px;
            }
        }
        .filter-by-rating-one {
            margin-left: 46% !important;
        }
        @media only screen and (max-width: 1800px){
            .filter-by-rating-one {
                margin-left: 50% !important;
            }
            .filter-by-ratings {
                margin-left: 19%;
            }
        }
        @media only screen and (max-width: 1400px){
            .filter-by-rating-one {
                margin-left: 46% !important;
            }

        }
    </style>
@endpush
@section('content')
<div class="new_user_section section_spacing6 pt-0">
    <div class="container">
        <div class="prodcuts_area ">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-xl-3">
                        <div id="product_category_chose" class="product_category_chose mb_30 mt_15">
                            <div class="course_title mb_15 d-flex align-items-center">
                                <svg  width="19.5" height="13" viewBox="0 0 19.5 13">
                                    <g id="filter-icon" transform="translate(28)">
                                        <rect id="Rectangle_1" data-name="Rectangle 1" width="19.5" height="2" rx="1" transform="translate(-28)" fill="#fd4949"/>
                                        <rect id="Rectangle_2" data-name="Rectangle 2" width="15.5" height="2" rx="1" transform="translate(-26 5.5)" fill="#fd4949"/>
                                        <rect id="Rectangle_3" data-name="Rectangle 3" width="5" height="2" rx="1" transform="translate(-20.75 11)" fill="#fd4949"/>
                                    </g>
                                </svg>
                                <h5 class="font_16 f_w_700 mb-0 ">{{__('amazy.Filter Products')}}</h5>
                                <div class="catgory_sidebar_closeIcon flex-fill justify-content-end d-flex d-lg-none">
                                    <button id="catgory_sidebar_closeIcon" class="home10_primary_btn2 gj-cursor-pointer mb-0 small_btn">{{__('amazy.close')}}</button>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-light text-dark refresh_btn" id="refresh_btn">{{__('amazy.refresh')}}</button>
                            </div>
                            <div class="course_category_inner">
                                @if(is_countable($CategoryList) && count($CategoryList) > 0)
                                    @foreach($CategoryList as $key => $category)
                                    <div class="single_pro_categry">
                                        <h4 class="font_18 f_w_700 getProductByChoice cursor_pointer" data-id="parent_cat"
                                        data-value="{{ $category->id }}">
                                            {{$category->name}}
                                        </h4>
                                        <ul class="Check_sidebar mb_35">
                                            @if (count($category->subCategories) > 0)
                                                @foreach($category->subCategories as $key => $subCategory)
                                                <li>
                                                    <label class="primary_checkbox d-flex">
                                                        <input type="checkbox" class="getProductByChoice attr_checkbox" data-id="cat"
                                                        data-value="{{ $subCategory->id }}">
                                                        <span class="checkmark mr_10"></span>
                                                        <span class="label_name">{{$subCategory->name}}</span>
                                                    </label>
                                                </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                    @endforeach
                                @endif
                                @isset ($brandList)
                                    @if(count($brandList) > 0)
                                        <div class="single_pro_categry">
                                            <h4 class="font_18 f_w_700">
                                            {{__('common.filter_by')}} {{__('product.brand')}}
                                            </h4>
                                            <ul class="Check_sidebar mb_35">
                                                @foreach($brandList as $key => $brand)
                                                    <li>
                                                        <label class="primary_checkbox d-flex">
                                                            <input type="checkbox" class="getProductByChoice" data-id="brand" data-value="{{ $brand->id }}">
                                                            <span class="checkmark mr_10"></span>
                                                            <span class="label_name">{{$brand->name}}</span>
                                                        </label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                @endisset
                                <div class="single_pro_categry">
                                    <h4 class="font_18 f_w_700">
                                    {{__('common.filter_by_rating')}}
                                    </h4>
                                    <ul class="rating_lists mb_35">
                                        <li>
                                            <div class="ratings">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <label class="primary_checkbox d-flex filter-by-rating-one">
                                                    <input type="checkbox" name="attr_value[]" class="getProductByChoice attr_checkbox" data-id="rating" data-value="5" id="attr_value">
                                                    <span class="checkmark mr_10"></span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="ratings">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star unrated"></i>
                                                <span class="text-nowrap">{{__('defaultTheme.and_up')}}</span>
                                                <label class="primary_checkbox d-flex filter-by-ratings">
                                                    <input type="checkbox" name="attr_value[]" class="getProductByChoice attr_checkbox" data-id="rating" data-value="4" id="attr_value">
                                                    <span class="checkmark mr_10"></span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="ratings">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star unrated"></i>
                                                <i class="fas fa-star unrated"></i>
                                                <span class="text-nowrap">{{__('defaultTheme.and_up')}}</span>
                                                <label class="primary_checkbox d-flex filter-by-ratings">
                                                    <input type="checkbox" name="attr_value[]" class="getProductByChoice attr_checkbox" data-id="rating" data-value="3" id="attr_value">
                                                    <span class="checkmark mr_10"></span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="ratings">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star unrated"></i>
                                                <i class="fas fa-star unrated"></i>
                                                <i class="fas fa-star unrated"></i>
                                                <span class="text-nowrap">{{__('defaultTheme.and_up')}}</span>
                                                <label class="primary_checkbox d-flex filter-by-ratings">
                                                    <input type="checkbox" name="attr_value[]" class="getProductByChoice attr_checkbox" data-id="rating" data-value="2" id="attr_value">
                                                    <span class="checkmark mr_10"></span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="ratings">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star unrated"></i>
                                                <i class="fas fa-star unrated"></i>
                                                <i class="fas fa-star unrated"></i>
                                                <i class="fas fa-star unrated"></i>
                                                <span class="text-nowrap">{{__('defaultTheme.and_up')}}</span>
                                                <label class="primary_checkbox d-flex filter-by-ratings">
                                                    <input type="checkbox" name="attr_value[]" class="getProductByChoice attr_checkbox" data-id="rating" data-value="1" id="attr_value">
                                                    <span class="checkmark mr_10"></span>
                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div id="price_range_div" class="single_pro_categry">
                                    <h4 class="font_18 f_w_700">
                                    {{__('common.filter_by_price')}}
                                    </h4>
                                    <div class="filter_wrapper">
                                        <input type="hidden" id="min_price" value="{{ $min_price_lowest }}" />
                                        <input type="hidden" id="max_price" value="{{ $max_price_highest }}" />
                                        <div id="slider-range"></div>
                                        <div class="d-flex align-items-center prise_line">
                                            <button class="home10_primary_btn2 mr_20 mb-0 small_btn js-range-slider-0">{{__('common.filter')}}</button>
                                            <span>{{__('common.price')}}: </span> <input type="text" id="amount" readonly >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="seller_id" name="seller_id" value="{{ $seller->id }}">
                    <div id="productShow" class="col-lg-8 col-xl-9">
                        @include('frontend.amazy.partials.merchant_page_paginate_data')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script >
    (function($){
        "use strict";
        $(document).ready(function() {
            var filterType = [];
            initRange ()
            $(document).on('click', '#refresh_btn', function(event){
                event.preventDefault();
                filterType = [];
                fetch_data(1);
                $('.attr_checkbox').prop('checked', false);
                $('.color_checkbox').removeClass('selected_btn');
                $('.category_checkbox').prop('checked', false);
                $('.brandDiv').html('');
                $('.colorDiv').html('');
                $('.attributeDiv').html('');
                $('.sub-menu').find('ul').css('display', 'none');
                $('.plus_btn_div').removeClass('ti-minus');
                $('.plus_btn_div').addClass('ti-plus');
                $('#price_range_div').html(
                    `<h4 class="font_18 f_w_700">
                        Filter by Price
                    </h4>
                    <div class="filter_wrapper">
                        <input type="hidden" id="min_price" value="{{ $min_price_lowest }}" />
                        <input type="hidden" id="max_price" value="{{ $max_price_highest }}" />
                        <div id="slider-range"></div>
                        <div class="d-flex align-items-center prise_line">
                            <button class="home10_primary_btn2 mr_20 mb-0 small_btn js-range-slider-0">Filter</button>
                            <span>Price: </span> <input type="text" id="amount" readonly >
                        </div>
                    </div>`
                );
                initRange ();
            });
            $(document).on('click', '.getProductByChoice', function(event){
                let type = $(this).data('id');
                let el = $(this).data('value');
                getProductByChoice(type, el);
            });
            $(document).on('click', '.attr_clr', function(event){
                if ($(this).is(':checked')) {
                    $(this).addClass('selected_btn');
                }else {
                    $(this).removeClass('selected_btn');
                }
            });
            $(document).on('change', '.getFilterUpdateByIndex', function(event){
                var paginate = $('#paginate_by').val();
                var prev_stat = $('.filterCatCol').val();
                var sort_by = $('#product_short_list').val();
                var seller_id = $('#seller_id').val();
                $('#pre-loader').show();
                $.get('{{ route('frontend.seller.sort_product_filter_by_type') }}', {seller_id:seller_id, sort_by:sort_by, paginate:paginate}, function(data){
                    $('#productShow').html(data);
                    $('#product_short_list').niceSelect();
                    $('#paginate_by').niceSelect();
                    $('#pre-loader').hide();
                    $('.filterCatCol').val(prev_stat);
                });
            });
            $(document).on('click', '.page_link', function(event) {
                event.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                var filterStatus = $('.filterCatCol').val();
                if (filterStatus == 0) {
                    fetch_data(page);
                }
                else {
                    fetch_filter_data(page);
                }
            });
            function fetch_data(page) {
                $('#pre-loader').show();
                var paginate = $('#paginate_by').val();
                var sort_by = $('#product_short_list').val();
                if (sort_by != null && paginate != null) {
                    var url = "{{route('frontend.seller.fetch-data',base64_encode($seller->id))}}"+'?sort_by='+sort_by+'&paginate='+paginate+'&page='+page;
                }else if (sort_by == null && paginate != null) {
                    var url ="{{route('frontend.seller.fetch-data',base64_encode($seller->id))}}"+'?paginate='+paginate+'&page='+page;
                }else {
                    var url = "{{route('frontend.seller.fetch-data',base64_encode($seller->id))}}" + '?page=' + page;
                }
                if (page != 'undefined') {
                    $.ajax({
                        url: url,
                        success: function(data) {
                            $('#productShow').html(data);
                            $('#product_short_list').niceSelect();
                            $('#paginate_by').niceSelect();
                            $('#pre-loader').hide();
                            activeTab();
                            initLazyload();
                        }
                    });
                } else {
                    toastr.error("{{__('common.error_message')}}", "{{__('common.error')}}");
                }
            }
            function fetch_filter_data(page){
                $('#pre-loader').show();
                var paginate = $('#paginate_by').val();
                var sort_by = $('#product_short_list').val();
                var seller_id = $('#seller_id').val();
                if (sort_by != null && paginate != null) {
                    var url = "{{route('frontend.seller.sort_product_filter_by_type')}}"+'?seller_id='+seller_id+'&sort_by='+sort_by+'&paginate='+paginate+'&page='+page;
                }else if (sort_by == null && paginate != null) {
                    var url = "{{route('frontend.seller.sort_product_filter_by_type')}}"+'?seller_id='+seller_id+'&paginate='+paginate+'&page='+page;
                }else {
                    var url = "{{route('frontend.seller.sort_product_filter_by_type')}}"+'?seller_id='+seller_id+'&page='+page;
                }
                if(page != 'undefined'){
                    $.ajax({
                        url:url,
                        success:function(data)
                        {
                            $('#productShow').html(data);
                            $('#product_short_list').niceSelect();
                            $('#paginate_by').niceSelect();
                            $('.filterCatCol').val(1);
                            $('#pre-loader').hide();
                            activeTab();
                            initLazyload();
                        }
                    });
                }else{
                    toastr.error("{{__('common.error_message')}}", "{{__('common.error')}}");
                }
            }
            let minimum_price = 0;
            let maximum_price = 0;
            let price_range_gloval = 0;
            $(document).on('click', '.js-range-slider-0', function(event){
                var price_range = $("#amount").val().split('-');
                minimum_price = price_range[0];
                maximum_price = price_range[1];
                price_range_gloval = price_range;
                myEfficientFn();
            });
            var myEfficientFn = debounce(function() {
                $('#min_price').val(minimum_price);
                $('#max_price').val(maximum_price);
                getProductByChoice("price_range",price_range_gloval);
            }, 500);
            function debounce(func, wait, immediate) {
                var timeout;
                return function() {
                    var context = this, args = arguments;
                    var later = function() {
                        timeout = null;
                        if (!immediate) func.apply(context, args);
                    };
                    var callNow = immediate && !timeout;
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                    if (callNow) func.apply(context, args);
                };
            };
            function initRange (){
                var minVal = parseInt($('#min_price').val());
                var maxVal = parseInt($('#max_price').val());
                $("#slider-range").slider({
                    range: true,
                    min: minVal,
                    max: maxVal,
                    values: [minVal, maxVal],
                    slide: function (event, ui) {
                        $("#amount").val(ui.values[0]+"-"+ui.values[1]);
                    },
                });
                $("#amount").val(
                    $("#slider-range").slider("values", 0)+"-"+$("#slider-range").slider("values", 1)
                );
            }
            function getProductByChoice(type,el)
            {
                var objNew = {filterTypeId:type, filterTypeValue:[el]};
                var objExistIndex = filterType.findIndex((objData) => objData.filterTypeId === type );
                var seller_id = $('#seller_id').val();
                if (type == "cat" || type =="brand") {
                    $.post('{{ route('frontend.seller.get_colors_by_type') }}', {_token:'{{ csrf_token() }}', id:el, type:type}, function(data){
                        $('.colorDiv').html(data);
                    });
                    $.post('{{ route('frontend.seller.get_attribute_by_type') }}', {_token:'{{ csrf_token() }}', id:el, type:type}, function(data){
                        $('.attributeDiv').html(data);
                    });
                }
                if (objExistIndex < 0) {
                    filterType.push(objNew);
                }else {
                    var objExist = filterType[objExistIndex];
                    if (objExist && objExist.filterTypeId == "price_range") {
                        objExist.filterTypeValue.pop(el);
                    }
                    if (objExist && objExist.filterTypeId == "rating") {
                        objExist.filterTypeValue.pop(el);
                    }
                    if (objExist.filterTypeValue.includes(el)) {
                        objExist.filterTypeValue.pop(el);
                    }else {
                        objExist.filterTypeValue.push(el);
                    }
                }
                $('#pre-loader').show();
                $.post('{{ route('frontend.seller.product_filter_by_type') }}', {_token:'{{ csrf_token() }}', filterType:filterType, seller_id:seller_id}, function(data){
                    $('#productShow').html(data);
                    $('.filterCatCol').val(1);
                    $('#product_short_list').niceSelect();
                    $('#paginate_by').niceSelect();
                    $('#pre-loader').hide();
                });
            }
            function activeTab(){
                var active_tab = localStorage.getItem('view_product_tab');
                if(active_tab != null && active_tab == 'profile'){
                    $("#profile").addClass("active");
                    $("#profile").addClass("show");
                    $("#home").removeClass('active');
                    $("#home-tab").removeClass("active");
                }else{
                    $("#home").addClass("active");
                    $("#home").addClass("show");
                    $("#profile").removeClass('active');
                    $("#profile-tab").removeClass("active");
                }
            }
            activeTab();
            $(document).on('click', ".view-product", function () {
                var target = $(this).attr("href");
                if(target == '#profile'){
                    localStorage.setItem('view_product_tab', 'profile');
                    $(this).addClass("active");
                    $("#profile").addClass("active");
                    $("#profile").addClass("show");
                    $("#home").removeClass('active');
                    $("#home-tab").removeClass("active");
                }else{
                    localStorage.setItem('view_product_tab', 'home');
                    $("#home").addClass("active");
                    $("#home").addClass("show");
                    $("#profile").removeClass('active');
                    $("#profile-tab").removeClass("active");
                }
            });
        });
    })(jQuery);
</script>
@endpush
@include(theme('partials.add_to_cart_script'))
@include(theme('partials.add_to_compare_script'))


