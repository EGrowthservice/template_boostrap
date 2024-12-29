@extends('layouts.partner_layout')

@section('content')
<!-- Content wrapper -->
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="app-ecommerce">

            <!-- Add Blogs Header -->
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
                <div class="d-flex flex-column justify-content-center">
                    <h4 class="mb-1">Sale news My Channels</h4>
                </div>

            </div>
            <ul class="nav nav-tabs mb-2 " role="tablist">

                <li class="nav-item me-1">
                    <a class="nav-link active" data-bs-toggle="tab" href="#all"> All news</a>
                </li>


                @if ($data->where('approved', 1)->count() > 0)
                <li class="nav-item me-1">
                    <a class="nav-link" data-bs-toggle="tab" href="#approved">Approved</a>
                </li>
                @else
                @endif
                @if ($data->where('approved', 2)->count() > 0)
                <li class="nav-item  me-1">
                    <a class="nav-link " data-bs-toggle="tab" href="#rejected">Rejected</a>
                </li>
                @else
                @endif
                @if ($data->where('approved', 0)->count() > 0)
                <li class="nav-item me-1 position-relative">
                    <a class="nav-link" data-bs-toggle="tab" href="#waiting">Waiting for approved</a>

                </li>
                @else
                @endif
               
                <li class="nav-item me-1 position-relative">
                    <a class="nav-link" data-bs-toggle="tab" href="#still">Still</a>

                </li>
               

                <li class="nav-item me-1 position-relative">
                    <a class="nav-link" data-bs-toggle="tab" href="#sold">Sold</a>

                </li>




            </ul>

            <!-- Blog Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-6">
                        <div class="card-body">
                            <div class="tab-content p-0">

                                <!-- All news Tab -->
                                @include('partner.sale_news.tabs.all')
                                <!-- approved tab -->
                                @include('partner.sale_news.tabs.approved')

                                <!--rejected -->
                                @include('partner.sale_news.tabs.rejected')

                                <!-- waiting -->
                                @include('partner.sale_news.tabs.waiting')
                                <!-- still -->

                                @include('partner.sale_news.tabs.still')
                                <!-- sold -->

                                @include('partner.sale_news.tabs.sold')


                            </div>

                            <!-- JavaScript -->

                            
                            <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
                            <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
                            <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('/../admin/assets/js/status-salenew-channel.js') }}"></script> 

@endsection