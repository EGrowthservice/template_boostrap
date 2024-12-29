@extends('layouts.admin')
@section('content')

<style>
.info-item {
    border-bottom: solid 0.1px #C4C5C9;
    padding-bottom: 10px;
}
</style>
<!-- Content wrapper -->
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="app-ecommerce">

            <!-- Add Blogs Header -->
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
                <div class="d-flex flex-column justify-content-center">
                    <h4 class="mb-1">Blogs</h4>
                </div>

            </div>

            <!-- Blog Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-6">
                        <div class="card-body">
                            <table id="example" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Title</th>
                                        <th>Image</th>
                                        <th class="px-1">Start date</th>

                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($blogs as $blog)
                                    <tr>
                                        <td>
                                            <div><span class="badge bg-label-secondary my-1">#{{ $blog->blog_id }}

                                                    <!-- Biểu tượng ngôi sao -->
                                                </span>
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($blog->title, 50, '...') }}</td>
                                        <td><img src="{{ asset('storage/' . $blog->image) }}" alt="Blog Image"
                                                style="width: 70px; height: auto;"></td>
                                        <td class="px-1">{{ $blog->created_at->format('Y-m-d') }}</td>

                                        <td>
                                            <form action="{{ route('blogs.toggleStatus', $blog->blog_id) }}"
                                                method="POST" class="toggle-status-form"
                                                data-blog-id="{{ $blog->blog_id }}">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $blog->status == 1 ? 'text-primary' : 'text-secondary' }}"
                                                    style="position: relative;">
                                                    <i
                                                        class="fas {{ $blog->status == 1 ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                                    <span
                                                        class="tooltip-text eye">{{ $blog->status == 1 ? 'Active' : 'Inactive' }}</span>
                                                </button>
                                            </form>

                                        </td>
                                        <td>

                                            <div class="btn-group">
                                                <button type="button"
                                                    class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <button type="button" class="dropdown-item "
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modal{{ $blog->blog_id }}">
                                                            <span> <i
                                                                    class="fa-solid fas fa-eye   "></i></span> View
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('blogs.edit', $blog->blog_id) }}">
                                                            <button type="submit" class="dropdown-item ">
                                                                <span><i class="fas fa-edit"></i> </span>Edit
                                                           </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form id="deleteForm_{{ $blog->blog_id }}"
                                                            action="{{ route('blogs.destroy', $blog->blog_id) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class=" delete-btn dropdown-item "
                                                                data-blog-id="{{ $blog->blog_id }}">
                                                              <i class="fas fa-trash"></i>
                                                                <span> </span>Delete
                                                            </button>
                                                        </form>
                                                    </li>

                                                </ul>
                                            </div>



                                        </td>

                                        <!-- Modal -->
                                        <div class="modal fade" id="modal{{ $blog->blog_id }}" tabindex="-1"
                                            aria-labelledby="exampleModalLabel{{ $blog->blog_id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="exampleModalLabel{{ $blog->blog_id }}">Info Blog</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">

                                                            <div class="mx-2 blog-info">
                                                                <div class="info-item">
                                                                    <strong>Image:</strong>
                                                                    <img src="{{ asset('storage/' . $blog->image) }}"
                                                                        alt="Blog Image" class="blog-image">
                                                                </div>

                                                                <div class="info-item">
                                                                    <strong>ID:</strong>
                                                                    {{ $blog->blog_id }}
                                                                </div>

                                                                <div class="info-item">
                                                                    <strong>Tags:</strong>
                                                                    {{ $blog->tags}}
                                                                </div>
                                                                <div class="info-item">
                                                                    <strong>Title:</strong>
                                                                    {{ $blog->title }}
                                                                </div>


                                                                <div class="info-item">
                                                                    <strong>Description:</strong>
                                                                    {{ $blog->short_description }}
                                                                </div>

                                                                <div class="info-item">
                                                                    <strong>Content:</strong>
                                                                    <div class="content-scroll">
                                                                        {!! $blog->content !!}
                                                                    </div>
                                                                </div>

                                                                <div class="info-item">
                                                                    <strong>Start date:</strong>
                                                                    {{ $blog->created_at->format('Y-m-d') }}
                                                                </div>

                                                                <div class="info-item">
                                                                    <strong>Status:</strong>
                                                                    <span
                                                                        class="modal-status {{ $blog->status == 1 ? 'text-primary' : 'text-secondary' }}">
                                                                        {{ $blog->status == 1 ? 'Active' : 'Inactive' }}
                                                                    </span>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- JavaScript -->

                            
                            

                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
@if (session('alert'))
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });
    Toast.fire({
        icon: "{{ session('alert')['type'] }}",
        title: "{{ session('alert')['message'] }}"
    });
</script>
@endif
<script src="{{ asset('/../admin/assets/js/status-blogs.js') }}"></script> 
@endsection
 