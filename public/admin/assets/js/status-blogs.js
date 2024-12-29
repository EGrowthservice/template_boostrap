$(document).ready(function () {
    $('.toggle-status-form').on('submit', function (e) {
        e.preventDefault(); // Ngăn form gửi mặc định
  
        var form = $(this);
        var button = form.find('button');
        var icon = button.find('i');
        var tooltip = button.find('.tooltip-text');
  
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function (response) {
                // Cập nhật trạng thái của nút
                if (response.status === 1) {
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    tooltip.text('Active');
                    button.removeClass('text-secondary').addClass('text-primary');
                } else if (response.status === 0) {
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    tooltip.text('Inactive');
                    button.removeClass('text-primary').addClass('text-secondary');
                }
  
                // Hiển thị thông báo bằng Toast
                Toast.fire({
                    icon: response.alert.type,
                    title: response.alert.message,
                });
            },
            error: function (xhr) {
                console.log('Error:', xhr.responseText);
  
                // Hiển thị thông báo lỗi
                Toast.fire({
                    icon: 'error',
                    title: 'Đã xảy ra lỗi khi cập nhật trạng thái!',
                });
            },
        });
    });
  });
  
  
  const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
  });
