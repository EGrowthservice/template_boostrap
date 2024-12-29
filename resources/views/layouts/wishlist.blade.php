@extends('layouts.client_layout')

@section('content')
<main class="main">

    <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
        <div class="container">
            <h1 class="page-title">Wishlist<span>Shop</span></h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->
    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>

                <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <table class="table table-wishlist table-mobile">
                @if($wishlist ->count()>0)
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock Status</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($wishlist as $item)
                    @if ($item->saleNews && $item->saleNews->approved == 1 && $item->saleNews->is_delete == null)
                    <!-- Điều kiện kiểm tra -->
                    <tr>
                        <td class="product-col">
                            <div class="product">
                                <figure class="product-media">
                                    @if ($item->saleNews->images->isNotEmpty())
                                    <img src="{{ $item->saleNews->images->first()->image_name }}" alt="Image">
                                    @endif
                                </figure>

                                <h3 class="product-title">
                                    <a
                                        href="{{ route('salenew.detail', $item->sale_new_id) }}">{{$item->saleNews->title}}</a>
                                </h3><!-- End .product-title -->
                            </div><!-- End .product -->
                        </td>
                        <td class="price-col">${{$item->saleNews->price}}</td>

                        <td class="stock-col">
                            <span class="{{ $item->saleNews->status == 1 ? 'in-stock' : 'out-of-stock' }}">
                                {{ $item->saleNews->status == 1 ? 'in-stock' : 'Out of stock' }}
                            </span>
                        </td>
                        <td class="action-col">
                            <button
                                class="btn btn-block btn-outline-primary-2 messenger-btn {{ $item->saleNews->status == 1 ? '' : 'disabled' }}"
                                data-id="{{ $item->saleNews->user_id }}"
                                data-name="{{ $item->saleNews->user->full_name }}">
                                <i class="fa fa-comments"></i> Messenger
                            </button>
                        </td>

                        <td class="remove-col">
                            <form action="{{ route('wishlist.destroy', $item->like_id) }}" method="POST"
                                class="cdelete-form">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="like_id" value="{{ $item->like_id }}">
                                <button type="button" class="btn-remove">
                                    <i class="icon-close"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>

                @else
                <H4 class="text-danger text-center ">No sale news here</H4>
                @endif
            </table><!-- End .table table-wishlist -->

        </div><!-- End .container -->
    </div><!-- End .page-content -->
</main><!-- End .main -->


@endsection

@section('script-link-css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
// Lắng nghe sự kiện click vào nút xóa trong danh sách yêu thích
$(document).on('click', '.btn-remove', function(e) {
    e.preventDefault(); // Ngừng hành động mặc định của nút

    // Lấy like_id từ input hidden trong form chứa nút
    var likeId = $(this).closest('form').find('input[name="like_id"]').val();
    var row = $(this).closest('tr'); 

    $.ajax({
        url: '/wishlist/' + likeId, // URL của route destroy
        type: 'DELETE',  
        data: {
            _token: '{{ csrf_token() }}',  
        },
        success: function(response) {
            if (response.success) {
                
                Swal.fire({
                    icon: 'success',
                    title: response.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
              
                row.remove();

                // Kiểm tra nếu danh sách không còn sản phẩm nào
                if ($('tbody tr').length === 0) {
                    // Nếu không còn sản phẩm, hiển thị thông báo "No sale news here"
                    $('tbody').html(
                    '<h4 class="text-danger text-center ">No sale news here</h4>'); // Thay đổi nội dung tbody
                    $('thead').remove(); // Xóa header nếu còn
                }


            } else {
                // Thông báo lỗi khi không thể xóa
                Swal.fire({
                    icon: 'error',
                    title: 'This item cannot be deleted.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error); // In ra lỗi trên console nếu có
            Swal.fire({
                icon: 'error',
                title: 'Error has occurred.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    });
});
document.addEventListener("DOMContentLoaded", function() {
    const messengerButtons = document.querySelectorAll(
    ".messenger-btn"); // Chọn tất cả nút có class messenger-btn

    messengerButtons.forEach(function(button) {
        if (!button.classList.contains("disabled")) {
            button.addEventListener("click", function() {
                const recipientId = this.getAttribute("data-id");
                const recipientName = this.getAttribute("data-name");

                // Gửi yêu cầu kiểm tra cuộc trò chuyện đã tồn tại
                fetch(`{{ route('message.checkconversations') }}?recipientId=${recipientId}`, {
                        method: "GET",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        },
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.channelExists) {
                            // Cuộc trò chuyện đã tồn tại
                            localStorage.setItem("channelName", data.channelName);
                            localStorage.setItem("recipientName", recipientName);
                            window.location.href = "{{ asset('message/conversations') }}";
                        } else {
                            // Tạo cuộc trò chuyện mới
                            createNewChannel(recipientId, recipientName);
                        }
                    })
                    .catch((error) => console.error("Error:", error));
            });
        }
    });

    function createNewChannel(recipientId, recipientName) {
        fetch(`{{ route('message.createconversations') }}?recipientId=${recipientId}`, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    localStorage.setItem("channelName", data.channelName);
                    localStorage.setItem("recipientName", recipientName);
                    window.location.href = "{{ asset('message/conversations') }}";
                } else {
                    console.error("Error creating channel:", data.error);
                }
            })
            .catch((error) => console.error("Error:", error));
    }
});
</script>

@endsection