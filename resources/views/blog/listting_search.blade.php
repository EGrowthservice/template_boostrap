@extends('layouts.client_layout')

@section('content')
<main class="main">
    <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
        <div class="container">
            <h1 class="page-title">Blogs Us<span>Blogs</span></h1>
        </div><!-- End .container -->
    </div><!-- End .pagessets/css/bootstrap.min.css-header -->
    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-2">
        <nav aria-label="breadcrumb" class="breadcrumb-nav mb-2">
             <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Breadcrumb -->
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url("/") }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Blog</a></li>
                        <li class="breadcrumb-item"><a href="#">{{ request('ws') }}</a></li>
                    </ol>

                    <!-- Filter Button -->
                    <ol class="breadcrumb mb-0">
                        <form action="{{ route('blog.search') }}" method="GET" id="filter-form" class="d-flex">
                            <!-- Tìm kiếm từ khóa -->


                            <!-- Dropdown lọc ngày -->
                            <div class="widget widget-search search-blogs position-relative">



                                <label for="ws" class="sr-only">Search in blog</label>
                                <input type="search" class="form-control" name="ws" placeholder="Search in blog"
                                    value="{{ old('ws', request('ws')) }}">
                                <button type="submit" class="btn"><i class="icon-search"></i><span
                                        class="sr-only">Search</span></button>

                            </div>
                            <div class="dropdown position-relative">
                                <!-- Nút hiển thị -->
                                <button class="btn btn-white  no-hover dropdown-toggle" type="button" id="dateFilterDropdown"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Filter by date <i class="ml-2 fas fa-filter"></i>
                                </button>

                                <!-- Danh sách dropdown -->
                                <ul class="dropdown-menu" aria-labelledby="dateFilterDropdown">
                                    <form action="{{ route('blog.search') }}" method="GET" id="filter-form">
                                        <!-- Danh sách các lựa chọn -->
                                        <li class="dropdown-item  li-pad">
                                            <button type="submit" name="date_filter" value="today"
                                                class="btn btn-link text-left p-0">
                                                <i class="   mr-2"></i> Today
                                            </button>
                                        </li>
                                        <li class="dropdown-item">
                                            <button type="submit" name="date_filter" value="yesterday"
                                                class="btn btn-link text-left p-0">
                                                <i class="  mr-2"></i> Yesterday
                                            </button>
                                        </li>
                                        <li class="dropdown-item">
                                            <button type="submit" name="date_filter" value="this-week"
                                                class="btn btn-link text-left p-0">
                                                <i class="  mr-2"></i> This Week
                                            </button>
                                        </li>
                                        <li class="dropdown-item">
                                            <button type="submit" name="date_filter" value="last-week"
                                                class="btn btn-link text-left p-0">
                                                <i class="  mr-2"></i> Last Week
                                            </button>
                                        </li>
                                        <li class="dropdown-item">
                                            <button type="submit" name="date_filter" value="this-month"
                                                class="btn btn-link text-left p-0">
                                                <i class="  mr-2"></i> This Month
                                            </button>
                                        </li>
                                        <li class="dropdown-item">
                                            <button type="submit" name="date_filter" value="last-month"
                                                class="btn btn-link text-secondary text-left p-0">
                                                <i class="  mr-2"></i> Last Month
                                            </button>
                                        </li>
                                    </form>
                                </ul>
                            </div>



                        </form>

                        <!-- Search Form -->



                    </ol>

                </div>

                <!-- Collapse Section (Filter Input) -->

            </div>


            <!-- End .container -->
            <!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->
        <div class="container">

            <div class="entry-container max-col-4">
                @if(isset($message))
                <p class="text-center text-danger">{{ $message }}</p>
                @elseif($blogs->isNotEmpty())
                @foreach($blogs as $blog)
                <div class="entry-item cat{{ $blog->category_id }} shopping col-sm-6 col-md-4 col-lg-3">
                    <article class="entry entry-grid text-center">
                        <figure class="entry-media">
                            <a href="{{ route('blogs.detail', $blog->blog_id) }}">
                                <img style="height:200px" src="{{ asset('storage/' . $blog->image) }}"
                                    alt="Blogs Image">
                            </a>
                        </figure><!-- End .entry-media -->

                        <div class="entry-body">
                            <div class="entry-meta">
                                <a href="{{ route('blogs.detail', $blog->blog_id) }}">
                                    {{ $blog->created_at->format('M d, Y') }}
                                </a>
                                <span class="meta-separator">|</span>
                                @if($blog->category)
                                <a class="ml-2" href="#">
                                    {{ $blog->category->name_category }}
                                </a>
                                @endif
                            </div><!-- End .entry-meta -->

                            <h2 class="entry-title">
                                <a href="{{ route('blogs.detail', $blog->blog_id) }}">{{ $blog->title }}</a>
                            </h2><!-- End .entry-title -->

                            <div class="entry-content">
                                <a href="{{ route('blogs.detail', $blog->blog_id) }}" class="read-more">Continue
                                    Reading</a>
                            </div><!-- End .entry-cats -->
                        </div><!-- End .entry-body -->
                    </article><!-- End .entry -->
                </div><!-- End .entry-item -->
                @endforeach
                @else
                <p class="text-center">
                <p>Not empty blog category!</p>
                </p>
                @endif
            </div>

        </div><!-- End .container -->
        </div><!-- End .page-content -->
</main><!-- End .main -->

@endsection

@section('script-link-css')
<script src="{{ asset('assets/js/imagesloaded.pkgd.min.js') }}"></script>
<script src="{{ asset('assets/js/isotope.pkgd.min.js') }}"></script>

<script>
    document.getElementById('date-filter').addEventListener('change', function() {
        document.getElementById('filter-form').submit();
    });
</script>

@endsection