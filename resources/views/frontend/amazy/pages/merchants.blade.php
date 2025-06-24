@extends('frontend.amazy.layouts.app')
@section('title')
    {{ __('common.merchants') }}
@endsection

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
                                    <a href="{{ route('frontend.seller', $seller->slug) }}">{{ $seller->first_name }} {{ $seller->last_name }}</a>
                                @else
                                    <a href="{{ route('frontend.seller', base64_encode($seller->id)) }}">{{ $seller->first_name }} {{ $seller->last_name }}</a>
                                @endif
                            </h4>
                            @if($seller->SellerAccount && $seller->SellerAccount->seller_shop_display_name)
                                <p>{{ $seller->SellerAccount->seller_shop_display_name }}</p>
                            @endif
                        </div>
                    </div>
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
