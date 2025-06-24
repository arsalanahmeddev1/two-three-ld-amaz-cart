@php
    $headerSliderSection = $headers->where('type','slider')->first();
@endphp

<div class="bannerUi_active owl-carousel {{$headerSliderSection->is_enable == 0?'d-none':''}}">
    @php
        $sliders = $headerSliderSection->sliders();
    @endphp
    @if(count($sliders) > 0)
        @foreach($sliders as $key => $slider)
            <a class="banner_img" href="
                @if($slider->data_type == 'url')
                    {{$slider->url}}
                @elseif($slider->data_type == 'product')
                    {{singleProductURL(@$slider->product->seller->slug, @$slider->product->slug)}}
                @elseif($slider->data_type == 'category')
                    {{route('frontend.category-product',['slug' => @$slider->category->slug, 'item' =>'category'])}}
                @elseif($slider->data_type == 'brand')
                    {{route('frontend.category-product',['slug' => @$slider->brand->slug, 'item' =>'brand'])}}
                @elseif($slider->data_type == 'tag')
                    {{route('frontend.category-product',['slug' => @$slider->tag->name, 'item' =>'tag'])}}
                @else
                    {{url('/category')}}
                @endif
                " {{$slider->is_newtab == 1?'target="_blank"':''}}>
                <div class="banner_content" style="background-image: url({{showImage($slider->slider_image)}}); background-size: cover; background-position: center; height: 500px; width: 100%;">
                    <div class="banner_content_text">
                        <h3 class="slider_heading text-white text-center">{{@$slider->heading}}</h3>
                        <p class="slider_sub_heading text-white text-center">{{@$slider->sub_heading}}</p>
                    </div>
                </div>
                </a>
        @endforeach
    @endif
</div>
