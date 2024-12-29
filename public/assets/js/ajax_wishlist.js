//  nhánh user và channels
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.wishlist-form').forEach(form => {
        form.querySelector('.wishlist-btn').addEventListener('click', function (event) {
            event.preventDefault(); // Ngăn form gửi mặc định qua GET

            const saleNewId = form.getAttribute('data-id'); // Lấy ID sản phẩm
            const button = this; // Nút được click

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ sale_new_id: saleNewId }), // Dữ liệu gửi lên server
            })
                .then(response => response.json())
                .then(data => {
                    if (data.type === 'success') {
                        // Hiển thị thông báo thành công
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });

                        // Thay đổi trạng thái yêu thích
                        if (button.classList.contains('text-primary')) {
                            button.classList.remove('text-primary');
                            button.innerHTML = `<i class="fas fa-heart"></i> Add to wishlist`;
                        } else {
                            button.classList.add('text-primary');
                            button.innerHTML = `<i class="fas fa-heart"></i> Added to wishlist`;
                        }
                    } else {
                        // Thông báo lỗi
                        Swal.fire({
                            icon: 'error',
                            title: data.message || 'Something went wrong.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Chuyển hướng tới đăng nhập
                    Swal.fire({
                        icon: 'warning',
                        title: 'You need to log in to manage your wishlist.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                    }).then(() => {
                        window.location.href = '/login'; // Chuyển hướng đến trang đăng nhập
                    });
                });
        });
    });
});
// end user và channels
// nhánh main
 // Lắng nghe sự kiện click vào nút thêm vào danh sách yêu thích
 $(document).on('click', '.add-to-wishlist-btn', function(e) {
    e.preventDefault(); // Ngăn hành vi gửi form mặc định của trình duyệt

    var form = $(this).closest('form'); // Lấy form chứa nút bấm
    var saleNewId = form.find('input[name="sale_new_id"]').val(); // Lấy giá trị sale_new_id từ input hidden

    // Kiểm tra nếu userId không có giá trị (người dùng chưa đăng nhập)
    if (!userId) {
        Swal.fire({
            icon: 'warning', // Hiển thị biểu tượng cảnh báo
            title: 'You need to log in to add this to your favorites!', // Thông báo yêu cầu đăng nhập
            toast: true, // Hiển thị thông báo nhỏ
            position: 'top-end', // Vị trí thông báo ở góc trên cùng bên phải
            showConfirmButton: false, // Không hiển thị nút xác nhận
            timer: 1500, // Thời gian hiển thị thông báo là 2 giây
            timerProgressBar: true // Hiển thị thanh tiến trình đếm ngược
        }).then(() => {
            window.location.href = "/login"; // Chuyển hướng đến trang đăng nhập
        });
        return; // Kết thúc hàm, không thực hiện các bước tiếp theo
    }

    // Gửi yêu cầu AJAX để thêm sản phẩm vào danh sách yêu thích
    $.ajax({
        url: form.attr('action'), // Lấy URL từ thuộc tính action của form
        type: 'POST', // Phương thức gửi yêu cầu là POST
        data: form.serialize(), // Lấy toàn bộ dữ liệu của form
        success: function(response) {
            // Nếu thêm thành công
            Swal.fire({
                icon: response.type === 'success' ? 'success' : 'info', // Hiển thị thông báo dựa trên type
                title: response.message || 'Action completed!', // Nội dung từ server
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000, // Thời gian hiển thị thông báo
                timerProgressBar: true
            });

            // Cập nhật giao diện cho nút
            if (response.type === 'success') {
                form.find('.add-to-wishlist-btn')
                    .toggleClass('bg-primary text-white text-primary')
                    .attr('title', response.isFavorited ? 'Add to wishlist' : '  Added to wishlist')
                    .html(response.isFavorited ? '<i class="fas fa-heart"></i>  ' : '<i class="fas fa-heart"></i>  ');
            }
        },
        error: function(xhr) {
            // Nếu xảy ra lỗi
            Swal.fire({
                icon: 'error',
                title: xhr.responseJSON?.message || 'An error occurred!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000, // Thời gian hiển thị
                timerProgressBar: true
            });
        }
    }); 
});
