@extends('layouts.client_layout')


@section('content')
<main class="main">

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url("/") }}">Home</a></li>
                <li class="breadcrumb-item"><a href="#">New</a></li>
                <li class="breadcrumb-item active" aria-current="page">View news status</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->
    <div class="page-content">
        <div class="cart">
            <div class="container">
                <div class="row d-flex justify-content-Start">
                    <div class="col col-md-9 col-lg-7 col-xl-6">
                        <div style="width:  fit-content;min-width:400px; ">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        @if ($user->image_user)
                                        <img src="{{ asset($user->image_user) }}"
                                            alt="Generic placeholder image" class="img-fluid" style="width: 120px;  border-radius: 10px;">
                                        @else
                                        <img src="https://i.pinimg.com/originals/c6/e5/65/c6e56503cfdd87da299f72dc416023d4.jpg"
                                            alt="Generic placeholder image" class="img-fluid" style="width: 120px;  border-radius: 10px;">
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 ms-3   ">
                                        <h5 class="mb-1">{{ $user->full_name }}</h5>

                                        <p style="color: #ff0000"><i class="fa-solid fa-face-smile" style="color: #FFD43B;"></i> Please pay attention when trading products that do not receive the protection of the exchange ! </p>
                                        <div style="background-color: inherit !important" class="d-flex justify-content-start rounded-3 p-2 mb-2 bg-body-tertiary  " style="width: fit-content">
                                            <div>
                                                <p class="small text-muted"><i class="fa-solid fa-clock" style="color: #74C0FC; "></i> {{ $user->created_at }}</p>
                                                <p class="small text-muted "> <i class="fa-solid fa-file-invoice-dollar" style="color: #74C0FC;  "></i> {{$sale_news->count()}}
                                                </p>

                                            </div>


                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-12">
                    <ul class="nav nav-tabs" id="tabs-2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active Bage text-uppercase" id="tab-5-tab" data-toggle="tab" href="#tab-5" role="tab"
                                aria-controls="tab-5" aria-selected="true">On sale ({{$sale_news->where('status', 1)->count()}}
                                )</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="tab-6-tab" data-toggle="tab" href="#tab-6" role="tab"
                                aria-controls="tab-6" aria-selected="false">sold
                                ( {{$sale_news->where('status', 0)->count()}}
                                )</a>
                        </li>

                    </ul>
                    <div class="tab-content tab-content-border" id="tab-content-2">
                        <div class="tab-pane fade active show" id="tab-5" role="tabpanel"
                            aria-labelledby="tab-5-tab">


                            @if ($sale_news->where('status', 1)->count()>0)
                            @foreach ($sale_news as $item)
                            @if ($item->status == 1)
                            <div class="products mb-3">
                                <div class="product product-list">
                                    <div class="row">
                                        <div class="col-5 col-lg-2">
                                            <figure class="product-media">


                                                <img src="{{ asset($item->firstImage->image_name) }}" style="width: 180px;   alt=" Product image" class="product-image">
                                            </figure><!-- End .product-media -->
                                        </div><!-- End .col-sm-6 col-lg-3 -->

                                        <div class="col-6 col-lg-4 order-lg-last">
                                            <div class="product-list-action">
                                                <div class="product-price">
                                                    ${{ $item->price }}
                                                </div><!-- End .product-price -->



                                                <a href="{{ route('salenew.detail', $item->sale_new_id) }}" class="mb-1 btn btn-outline-primary btn-rounded" style="width: 100%"><i class="fa-solid fa-eye" style="color: #74C0FC;"></i> <span>Detail</span></a>

                                                <form action="{{ route('toggleWishlist') }}" method="POST"
                                                    class="wishlist-form" data-id="{{ $item->sale_new_id }}">
                                                    @csrf
                                                    <button type="button"
                                                        style="width: 100%  " class="mb-1 btn btn-light    btn-rounded  mb-2 wishlist-btn {{ $item->isFavorited ? 'text-primary' : '' }} color-danger"
                                                        title="{{ $item->isFavorited ? 'Remove from wishlist' : 'Add to wishlist' }}">
                                                        <i class="fas fa-heart"></i>
                                                        {{ $item->isFavorited ? 'Added to wishlist' : 'Add to wishlist' }}
                                                    </button>
                                                </form>

                                            </div><!-- End .product-list-action -->
                                        </div><!-- End .col-sm-6 col-lg-3 -->

                                        <div class="col-lg-6">
                                            <div class="product-body product-action-inner">

                                                <div class="product-cat">
                                                    <a href="#">{{ $item->sub_category->name_sub_category }}</a>
                                                </div><!-- End .product-cat -->
                                                <h3 class="product-title">{{ $item->title }}
                                                    @if ($item->vip_package_id)
                                                    <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                                    @endif
                                                </h3><!-- End .product-title -->

                                                <div class="product-content">
                                                    <p>
                                                        @if ($item->vip_package_id)
                                                        <i class="fa-solid fa-crown " style="color: #FFD43B;"></i>
                                                        Best quality
                                                        @else

                                                        @endif

                                                    </p>

                                                    <p><i class="fa-solid fa-eye" style="color: #74C0FC;"></i> {{ $item->views }}</p>
                                                    <p><i class="fa-solid fa-clock" style="color: #74C0FC;"></i> {{ $item->created_at }}</p>
                                                    <p><i class="fa-solid fa-location-dot" style="color: #74C0FC;"></i> {{ $item->address }}</p>
                                                </div><!-- End .product-content -->


                                            </div><!-- End .product-body -->
                                        </div><!-- End .col-lg-6 -->
                                    </div><!-- End .row -->
                                </div>

                            </div>

                            @endif

                            @endforeach
                            @else
                            <h5 class='text-danger text-center'>No products sold yet</h5>

                            @endif



                        </div><!-- .End .tab-pane -->
                        <div class="tab-pane fade" id="tab-6" role="tabpanel" aria-labelledby="tab-6-tab">
                            @if ($sale_news->where('status', 0)->count()>0)
                            @foreach ($sale_news as $item)
                            @if ($item->status == 0)
                            <div class="products mb-3">
                                <div class="product product-list">
                                    <div class="row">
                                        <div class="col-5 col-lg-2">
                                            <figure class="product-media">


                                                <img src="{{ asset($item->firstImage->image_name) }}" style="width: 180px;   alt=" Product image" class="product-image">
                                            </figure><!-- End .product-media -->
                                        </div><!-- End .col-sm-6 col-lg-3 -->

                                        <div class="col-6 col-lg-4 order-lg-last">
                                            <div class="product-list-action">
                                                <div class="product-price">
                                                    ${{ $item->price }}
                                                </div><!-- End .product-price -->



                                                <a href="{{ route('salenew.detail', $item->sale_new_id) }}" class="mb-1 btn btn-outline-primary btn-rounded" style="width: 100%"><i class="fa-solid fa-eye" style="color: #74C0FC;"></i> <span>Detail</span></a>
                                                <a href="#" men class="mb-1 btn disabled  btn-rounded" style="width: 100%; "> <i class="fas fa-heart" ></i><span>Not Add to wishlist</span></a>

                                            </div><!-- End .product-list-action -->
                                        </div><!-- End .col-sm-6 col-lg-3 -->

                                        <div class="col-lg-6">
                                            <div class="product-body product-action-inner">

                                                <div class="product-cat">
                                                    <a href="#">{{ $item->sub_category->name_sub_category }}</a>
                                                </div><!-- End .product-cat -->
                                                <h3 class="product-title">{{ $item->title }}
                                                    @if ($item->vip_package_id)
                                                    <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                                    @endif
                                                </h3><!-- End .product-title -->

                                                <div class="product-content">
                                                    <p>
                                                        @if ($item->vip_package_id)
                                                        <i class="fa-solid fa-crown " style="color: #FFD43B;"></i>
                                                        Best quality
                                                        @else

                                                        @endif

                                                    </p>

                                                    <p><i class="fa-solid fa-eye" style="color: #74C0FC;"></i> {{ $item->views }}</p>
                                                    <p><i class="fa-solid fa-clock" style="color: #74C0FC;"></i> {{ $item->created_at }}</p>
                                                    <p><i class="fa-solid fa-location-dot" style="color: #74C0FC;"></i> {{ $item->address }}</p>
                                                </div><!-- End .product-content -->


                                            </div><!-- End .product-body -->
                                        </div><!-- End .col-lg-6 -->
                                    </div><!-- End .row -->
                                </div>

                            </div>

                            @endif

                            @endforeach
                            @else
                            <h5 class='text-danger text-center'>No products sold yet</h5>

                            @endif

                        </div><!-- .End .tab-pane -->


                    </div><!-- End .tab-content -->
                </div>
            </div>
        </div>

    </div>


</main><!-- End .main -->
 

<script src="{{ asset('assets/js/ajax_wishlist.js') }}"></script>

<style>
    .listing-card {
        /* background: #d7d7d756; */
        /* border-radius: 8px; */
        /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
        /* padding: 20px; */
        /* width: 300px; */
    }

    .card-content {
        border-radius: 8px 1px;
        background: #efeeee61;
        text-align: left;
    }

    .card-content h2 {
        font-size: 1.2em;
        margin-bottom: 10px;
    }

    .card-content .price {
        font-size: 1.5em;
        color: #e74c3c;
        margin-bottom: 10px;
    }

    .card-content .location,
    .date,
    .expiry,
    .views,
    .services {
        margin-bottom: 10px;
        color: #555;
    }

    .card-content .details,
    .renew {
        display: inline-block;
        margin-right: 10px;
        padding: 10px 15px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .card-content .renew {
        background-color: #28a745;
    }

    .card-content .details:hover,
    .renew:hover {
        background-color: #0056b3;
    }

    .page-content {
        /* max-width: 1px; */
        margin: 0 auto;
        padding-left: 20px;
    }

    .profile {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .profile img {
        border-radius: 80%;
        width: 50px;
        height: 50px;
        margin-right: 10px;
    }

    .profile span {
        font-size: 1.2em;
        margin-right: auto;
    }

    .create-store-btn {
        padding: 10px 15px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .illustration img {
        width: 100px;
        height: auto;
    }
</style>
@endsection
