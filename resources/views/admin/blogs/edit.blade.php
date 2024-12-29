@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="app-ecommerce">
        <!-- Edit Blog -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1">Edit Blog</h4>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-4">
                <div class="d-flex gap-4">
                    <a href="{{ route('blogs.index') }}" class="btn btn-label-secondary">Discard</a>
                </div>

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
                        <!-- Form sửa Blog -->
                        <form id="blog-form" action="{{ route('blogs.update', $blog->blog_id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Title:</label>
                                <input type="text" name="title" class="form-control" placeholder="Enter blog title"
                                    value="{{ old('title', $blog->title) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tag:</label>
                                <input type="text" name="tags" class="form-control" placeholder="Enter blog title"
                                    value="{{ old('tags', $blog->tags) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Short Description:</label>
                                <input type="text" name="short_description" class="form-control"
                                    placeholder="Enter short description"
                                    value="{{ old('short_description', $blog->short_description) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Content:</label>
                                <textarea name="content" id="editor" rows="5"
                                    class="form-control">{{ old('content', $blog->content) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status:</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="1" {{ $blog->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $blog->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                    </div>
                </div>
            </div>

            <!-- Upload Image -->
            <div class="col-12 col-lg-4">
                <div class="mb-6">
                    <label class="form-label">Category:</label>
                    <select name="category_id" id="category_id" class="form-select">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->category_id }}"
                            {{ $blog->category_id == $cat->category_id ? 'selected' : '' }}>
                            {{ $cat->name_category }}
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
                            @if ($blog->image)
                            <img src="{{ asset('storage/' . $blog->image) }}" alt="Blog Image" id="preview_img"
                                class="mt-3 rounded" style="width: 100px; height: auto;">
                            @else
                            <img id="preview_img" class="mt-3 rounded hidden" style="width: 100px; height: auto;">
                            @endif
                            <div style="font-weight: 500; text-align: center; width: 300px; height: 2.5rem;">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnBrowse">Browse
                                    image</button>
                                <input type="file" id="fileInput" name="image" style="display: none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Submit Button -->
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Update Blog</button>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script-link-css')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/super-build/ckeditor.js"></script>
<script src="{{ asset('admin/assets/js/ckeditor.js') }}"></script>
<script>
    
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