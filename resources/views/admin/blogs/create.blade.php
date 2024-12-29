@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="app-ecommerce">
        <!-- Add Blog -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1">Add A New Blog</h4>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-4">
               
                <!-- Đổi nút Publish thành nút submit -->
                <button type="submit" form="blog-form" class="btn btn-primary" id="btn-publish-product">Publish</button>
            </div>
        </div>

        <div class="row">
            <!-- Blog Information -->
            <div class="col-12 col-lg-8">
                <div class="card mb-6">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Blog Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Form thêm Blog -->
                        <form id="blog-form" action="{{ route('blogs.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Title:</label>
                                <input type="text" name="title" class="form-control"  placeholder="Enter blog title">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tag:</label>
                                <input type="text" name="tags" class="form-control" placeholder="Enter blog tags">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Short Description:</label>
                                <input type="text" name="short_description" class="form-control"
                                    placeholder="Enter short description">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content:</label>
                                <textarea name="content" id="editor" rows="5" class="form-control"></textarea>
                            </div>
                    </div>
                </div>
            </div>

            <!-- Upload Image -->
            <div class="col-12 col-lg-4">
                <div class="mb-6">
                    <label class="form-label">Category:</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">Select Category</option>
                        @foreach($category as $cat)
                        <option id="{{ $cat->category_id }}" value="{{ $cat->category_id }}">{{ $cat->name_category }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="card mb-6">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Upload Image</h5>
                    </div>
                    <div class="card-body">
                        <div class="dropzone p-0 dz-clickable"
                            style="display: flex; justify-content: center; align-items: center; flex-direction: column; height: 11.5rem;">
                            <img id="preview_img" class="mt-3 rounded" style="width: 100px; height: auto;">
                            <div style="font-weight: 500; text-align: center; width: 300px; height: 2.5rem;">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnBrowse">Browse
                                    image</button>
                                <input type="file" id="fileInput" name="image" style="display: none">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Đóng form tại đây -->
        </form>
    </div>
</div>
@endsection

@section('script-link-css')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/super-build/ckeditor.js"></script>
<script src="{{ asset('admin/assets/js/ckeditor.js') }}"></script>
<script
    src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/dropzone/dropzone.js">
</script>
<link rel="stylesheet"
    href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/libs/dropzone/dropzone.css">

<script>
@if(session('message')) <
    div class = "alert alert-success" >
    <
    strong > Success! < /strong> {{ session('message') }} <
    /div>
@endif

@if(session('alert')) <
    div class = "alert alert-{{ session('alert')['type'] }}" >
    <
    strong > {
        {
            ucfirst(session('alert')['type'])
        }
    }! < /strong> {{ session('alert')['message'] }} <
    /div>
@endif

document.getElementById('btnBrowse').addEventListener('click', function() {
    document.getElementById('fileInput').click();
});

document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('fileInput');
    const previewImg = document.getElementById('preview_img');

    input.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
            };

            reader.readAsDataURL(file);
        } else {
            previewImg.classList.add('hidden');
        }
    });
});
</script>
@endsection