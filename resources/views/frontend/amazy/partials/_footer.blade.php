@php
    $footer_content = \Modules\FooterSetting\Entities\FooterContent::first();
    $subscribeContent = \Modules\FrontendCMS\Entities\SubscribeContent::find(1);
    $about_section = Modules\FrontendCMS\Entities\HomePageSection::where('section_name','about_section')->first();
@endphp
@if(url()->current() == url('/'))
<!--<div id="about_section" class="amaz_section section_spacing4 {{ ($about_section)? ($about_section->status == 0?'d-none':'') : ''}}">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section__title d-flex align-items-center gap-3 mb_20">
                    <h3 class="m-0 flex-fill">{{ app('general_setting')->footer_about_title }}</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="amaz_mazing_text">
                    @php echo app('general_setting')->footer_about_description; @endphp
                </div>
            </div>
        </div>
    </div>
</div>-->
@endif

<!-- FOOTER::START  -->
    <!--
    <footer class="home_three_footer">
        <div class="main_footer_wrap">
            <div class="container">
                 <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-6 footer_links_50 ">
                        <div class="footer_widget" >
                            <ul class="footer_links">
                                @foreach($sectionWidgets->where('section','1') as $page)
                                    @if($page->pageData)
                                    @if(!isModuleActive('Lead') && $page->pageData->module == 'Lead')
                                        @continue
                                    @endif
                                    <li><a href="{{ url($page->pageData->slug) }}">{{$page->name}}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 footer_links_50 ">
                        <div class="footer_widget">
                            <ul class="footer_links">
                                @foreach($sectionWidgets->where('section','2') as $page)
                                    @if($page->pageData)
                                        @if(!isModuleActive('Lead') && $page->pageData->module == 'Lead')
                                            @continue
                                        @endif
                                        <li><a href="{{ url($page->pageData->slug) }}">{{$page->name}}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xl-3 col-md-6">
                        <div class="footer_widget" >

                            <div class="apps_boxs">
                                @if($footer_content->show_play_store)
                                <a href="{{$footer_content->play_store}}" class="google_play_box d-flex align-items-center mb_10">
                                    <div class="icon">
                                        <img src="{{url('/')}}/public/frontend/amazy/img/amaz_icon/google_play.svg" alt="{{__('amazy.Google Play')}}" title="{{__('amazy.Google Play')}}">
                                    </div>
                                    <div class="google_play_text">
                                        <span>{{__('amazy.Get it on')}}</span>
                                        <h4 class="text-nowrap">{{__('amazy.Google Play')}}</h4>
                                    </div>
                                </a>
                                @endif
                                @if($footer_content->show_app_store)
                                <a href="{{$footer_content->app_store}}" class="google_play_box d-flex align-items-center">
                                    <div class="icon">
                                        <img src="{{url('/')}}/public/frontend/amazy/img/amaz_icon/apple_icon.svg" alt="{{__('amazy.Apple Store')}}"  title="{{__('amazy.Apple Store')}}">
                                    </div>
                                    <div class="google_play_text">
                                        <span>{{__('amazy.Get it on')}}</span>
                                        <h4 class="text-nowrap">{{__('amazy.Apple Store')}}</h4>
                                    </div>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <x-subscribe-component :subscribeContent="$subscribeContent"/>
                </div>
            </div>
        </div>
        <div class="copyright_area p-0">
            <div class="container">
                <div class="footer_border m-0"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="copy_right_text d-flex align-items-center gap_20 flex-wrap justify-content-between">
                            @php echo app('general_setting')->footer_copy_right; @endphp
                            <div class="footer_list_links">
                                @foreach($sectionWidgets->where('section','3') as $page)
                                    @if($page->pageData)
                                        @if(!isModuleActive('Lead') && $page->pageData->module == 'Lead')
                                            @continue
                                        @endif
                                        <a href="{{ url($page->pageData->slug) }}">{{$page->name}}</a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @if($footer_content->show_payment_image != 0 && $footer_content->payment_image)
                    <div class="footer_border m-0"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="payment_imgs text-center ">
                                <img class="img-fluid" src="{{showImage($footer_content->payment_image)}}" alt="{{__('common.payment_method')}}" title="{{__('common.payment_method')}}">
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </footer>-->
    <footer class="bg-black pt-65 pb-150">
  <div class="container">
    <ul class="d-flex justify-content-between mx-auto footer-navs" style="max-width: 790px;">
      <li><a href="/" class="text-white fs-22 primary-font">Market Place</a></li>
      <li><a href="/" class="text-white fs-22 primary-font">Community Partners</a></li>
      <li><a href="/" class="text-white fs-22 primary-font">About us</a></li>
      <li><a href="/" class="text-white fs-22 primary-font">Work with Us</a></li>
    </ul>
    <div class="border-bottom mb-100 border-white w-100 mx-auto mt-45 mb-170" style="max-width: 1050px;"></div>
    <div class="row justify-content-between">
      <div class="col-12 col-md-4">
        <div class="logo" style="max-width: 250px;">
          <img src="{{ asset('public/uploads/all/68534b76c74ba.png') }}" alt="logo" class="mb-20">
        </div>
        <p class="text-white fs-22 mb-30 primary-font">Join us and get 10% off your first order!</p>
        <p class="text-white fs-22 mb-20 primary-font" style="max-width: 410px;">Stay inspired with fresh artwork and curated collections added weekly.</p>
        <ul class="d-flex gap-20 ps-0">
          <li><a href="/" class="text-white fs-25"><i class="fa-brands fa-facebook-f"></i></a></li>
          <li><a href="/" class="text-white fs-25"><i class="fa-brands fa-instagram"></i></a></li>
          <li><a href="/" class="text-white fs-25"><i class="fa-brands fa-linkedin-in"></i></a></li>
        </ul>
      </div>
      <div class="col-12 col-md-2">
        <ul class="footer-links">
          <li class="text-white fs-22 mb-20 fw-700 primary-font">For Art Collectors</li>
          <li class="mb-10"><a href="{{ route('frontend.artists') }}" class="fs-22 primary-font">Artists</a></li>
          <li class="mb-10"><a href="{{ route('frontend.shop') }}" class="fs-22 primary-font">Shop</a></li>
          <li class="mb-10"><a href="/" class=" fs-22 primary-font">Gallery</a></li>
        </ul>
      </div>
      <div class="col-12 col-md-2">
        <ul class="footer-links">
          <li class="text-white fs-22 mb-20 fw-700 primary-font">For Artists</li>
          <li class="mb-10"><a href="/" class="text-white fs-22 primary-font">Private Workshops</a></li>
          <li class="mb-10"><a href="/" class="text-white fs-22 primary-font">Our Quiz</a></li>
          <li class="mb-10"><a href="/" class="text-white fs-22 primary-font">Our Team</a></li>
        </ul>
      </div>
      <div class="col-12 col-md-4">
        <ul class="footer-links">
          <li class="text-white fs-22 mb-20 fw-700 primary-font">Contact Us</li>
          <li class="mb-10"><a href="tel:9548500145" class="text-white fs-22 d-flex gap-10 primary-font"> <i class="fa-solid fa-phone"></i> 954 850 0145</a></li>
          <li class="mb-10"><a href="tel:2057778284" class="text-white fs-22 d-flex gap-10 primary-font"> <i class="fa-solid fa-phone"></i> 205 777 8284</a></li>
          <li class="mb-10"><a href="mailto:alexsoto.23ld@gmail.com" class="text-white fs-22 d-flex gap-10 primary-font"> <i class="fa-solid fa-envelope d-flex align-items-center"></i> alexsoto.23ld@gmail.com</a></li>
          <li class="mb-10"><a href="mailto:devinpughsley.23ld@gmail.com" class="text-white fs-22 d-flex gap-10 primary-font"> <i class="fa-solid fa-envelope d-flex align-items-center"></i> devinpughsley.23ld@gmail.com</a></li>
        </ul>
        <form action="">
          <div class="d-flex align-items-center mt-40">
            <input type="email" placeholder="Email" class="footer-input" name="email" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
            <button type="submit" class="btn btn-primary footer-submit-btn bg-white text-black fs-16 py-15 px-20 border border-white primary-font" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</footer>
    <!-- FOOTER::END  -->
@include('frontend.amazy.auth.partials._login_modal')
<div id="cart_data_show_div">
    @include('frontend.amazy.partials._cart_details_submenu')
</div>
<div id="cart_success_modal_div">
    @include('frontend.amazy.partials._cart_success_modal')
</div>
<input type="hidden" id="login_check" value="@if(auth()->check()) 1 @else 0 @endif">
<div class="add-product-to-cart-using-modal">

</div>

@include('frontend.amazy.partials._modals')

<div id="back-top" style="display: none;">
    <a title="{{__('common.go_to_top')}}" href="#"><i class="fas fa-chevron-up"></i></a>
</div>

@php
    $messanger_data = \Modules\GeneralSetting\Entities\FacebookMessage::first();
@endphp
@if($messanger_data->status == 1)
    @php echo $messanger_data->code; @endphp
@endif


@include('frontend.amazy.partials._script')
@stack('scripts')
@stack('wallet_scripts')

<script>
    $(document).ready(function(){
    $(".marketplace-slider").owlCarousel({
        items: 1,  // One item per slide
        loop: true,  // Enable looping
        autoplay: true,  // Enable autoplay
        autoplayTimeout: 3000,  // Autoplay interval (3 seconds)
        autoplayHoverPause: true,  // Pause on hover
        nav: true,  // Show next/prev buttons
        dots: false,  // Disable dots
        responsiveClass: true,
        responsive: {
            0: {
                items: 1  // On small screens, display 1 item
            },
            600: {
                items: 1  // On medium screens, display 1 item
            },
            1000: {
                items: 1  // On large screens, display 1 item
            }
        }
    });
});
$(document).ready(function() {
  // Remove text inside owl-prev and owl-next
  $(".marketplace-slider.owl-carousel .owl-nav .owl-prev, .marketplace-slider.owl-carousel .owl-nav .owl-next").each(function() {
    $(this).text(''); // Removes the text content (Prev and Next)
  });
});
</script>


</body>

</html>
