@extends('layouts.admin')
@section('content')
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="app-ecommerce">
                <!-- Add Channel -->
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
                    <div class="d-flex flex-column justify-content-center">
                        <h4 class="mb-1">Channels</h4>
                    </div>
                </div>

                <div class="row">
                    <!-- Channel Table -->
                    <div class="col-12 col-lg-12">
                        <div class="card mb-6">
                            <div class="card-body">
                                <table id="channel-table" class="table table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Id Channel</th>
                                            <th>Channel Name</th>
                                            <th>Address</th>
                                            <th>Phone Number</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($channels as $channel)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <span class="badge bg-label-info my-1">ID:
                                                            {{ $channel->channel_id }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="badge bg-label-info my-1">
                                                            {{ date('D, d M Y', strtotime($channel->created_at)) }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ $channel->name_channel }}</td>
                                                <td>{{ $channel->address }}</td>
                                                <td>{{ $channel->phone_number }}</td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm toggle-status-btn {{ $channel->status == 1 ? 'text-primary' : 'text-secondary' }}"
                                                        data-channel-id="{{ $channel->channel_id }}"
                                                        data-channel-status="{{ $channel->status }}">
                                                        <i
                                                            class="fas {{ $channel->status == 1 ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                                        <span>{{ $channel->status == 1 ? 'Active' : 'Inactive' }}</span>
                                                    </button>
                                                </td>
                                                <td>

                                                    <div class="container">
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow"
                                                                data-bs-toggle="dropdown">
                                                                <i class="bx bx-dots-vertical-rounded"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li data-bs-toggle="modal"
                                                                    data-bs-target="#modal{{ $channel->channel_id }}">
                                                                    <a class="dropdown-item" href="#"><span><i
                                                                                class="fa-solid fa-eye me-1"></i></span>View</a>
                                                                </li>

                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="modal{{ $channel->channel_id }}"
                                                        tabindex="-1"
                                                        aria-labelledby="exampleModalLabel{{ $channel->channel_id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title text-truncate-1">Details of
                                                                        {{ $channel->name_channel }}</h4><button
                                                                        type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <table class="table">
                                                                        <tbody>
                                                                            <tr data-dt-row="2" data-dt-column="2">
                                                                                <td class="col-3">channel:</td>
                                                                                <td class="col-9">
                                                                                    <div
                                                                                        class="d-flex justify-content-start align-items-center product-name">
                                                                                        <div class="avatar-wrapper">
                                                                                            <div
                                                                                                class="avatar avatar me-4 rounded-2 bg-label-secondary">
                                                                                                <img src="{{ asset($channel->image_channel) }}"
                                                                                                    alt="Product-3"
                                                                                                    class="rounded"
                                                                                                    style="width: 100%; object-fit: cover;">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="d-flex flex-column">
                                                                                            <h6
                                                                                                class="mb-0 text-truncate-1">
                                                                                                {{ $channel->name_channel }}
                                                                                            </h6>
                                                                                            <small
                                                                                                class="text-truncate-1">{{ $channel->description }}</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr data-dt-row="2" data-dt-column="7">
                                                                                <td class="col-3">Adress:</td>
                                                                                <td class="col-9">
                                                                                    <span>{{ $channel->address }}</span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr data-dt-row="2" data-dt-column="7">
                                                                                <td class="col-3">Phone</td>
                                                                                <td class="col-9">
                                                                                    <span>{{ $channel->phone_number }}</span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr data-dt-row="2" data-dt-column="8">
                                                                                <td class="col-3">Status:</td>
                                                                                <td class="col-8 bg-light rounded">
                                                                                    @if ($channel->status == 1)
                                                                                        <span
                                                                                            class="badge bg-label-success">Active</span>
                                                                                    @else
                                                                                        <span
                                                                                            class="badge bg-label-danger">Deactive</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            <tr data-dt-row="2" data-dt-column="9">
                                                                                <td class="col-3">Created At:</td>
                                                                                <td class="col-9">
                                                                                    <span>{{ date('D, d M Y', strtotime($channel->created_at)) }}</span>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- / Content -->

        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
<script src="{{ asset('/../admin/assets/js/channel.js') }}"></script> 

@endsection

 
