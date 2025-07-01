@extends('frontend.amazy.layouts.app')
@section('title')
{{ __('common.merchants') }}
@endsection
<style>
    .seller_shop_details {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .seller_shop_details {}

    .seller_shop_logo,
    .seller_shop_logo img {
        width: 100%;
    }

    .seller_shop_logo {
        margin-bottom: 20px;
    }

    .seller_shop_logo img {
        object-fit: cover;
        max-width: 350px;
        display: flex;
        justify-content: center;
        margin: 0 auto;
        min-height: 350px;
        border-radius: 10px;
    }
    
</style>
@section('content')
<div class="amazy_section_padding">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section_title d-flex align-items-center justify-content-between mb_30">
                    <h3>{{ __('All Artists') }}</h3>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach($sellers as $seller)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="seller_shop_box mb_30">
                    <!-- <div class="seller_shop_banner">
                        @if($seller->SellerAccount && $seller->SellerAccount->banner)
                            <img src="{{ showImage($seller->SellerAccount->banner) }}" alt="{{ $seller->first_name }}" title="{{ $seller->first_name }}">
                        @else
                            <img src="{{ showImage('frontend/default/img/default_shop_banner.png') }}" alt="{{ $seller->first_name }}" title="{{ $seller->first_name }}">
                        @endif
                    </div> -->
                    <a href="{{ route('frontend.seller', $seller->slug) }}">
                    <div class="seller_shop_details">
                            <div class="seller_shop_logo">
                                @if($seller->photo)
                                <img src="{{ showImage($seller->photo) }}" alt="{{ $seller->first_name }}" title="{{ $seller->first_name }}">
                                @else
                                <img src="{{ showImage('frontend/default/img/avatar.jpg') }}" alt="{{ $seller->first_name }}" title="{{ $seller->first_name }}">
                                @endif
                            </div>
                            <div class="seller_shop_info">
                                <h4>
                                    @if($seller->slug)
                                    {{ $seller->first_name }} {{ $seller->last_name }}
                                    @else
                                    <a href="{{ route('frontend.seller', base64_encode($seller->id)) }}">{{ $seller->first_name }} {{ $seller->last_name }}</a>
                                    @endif
                                </h4>
                                @if($seller->SellerAccount && $seller->SellerAccount->seller_shop_display_name)
                                <p>{{ $seller->SellerAccount->seller_shop_display_name }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-12">
                <div class="pagination_part">
                    {{ $sellers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection