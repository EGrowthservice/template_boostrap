$(document).ready(function() {
    $('.toggle-status-form button').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var button = $(this);
        var icon = button.find('i');
        var tooltip = button.find('.tooltip-text');
        var blogId = form.data('blog-id');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                // Cập nhật trạng thái của nút
                if (response.status === 1) {
                    icon.removeClass('fa-eye-slash').addClass(
                        'fa-eye');
                    tooltip.text('Active');
                    button.removeClass('text-secondary')
                        .addClass('text-primary');
                } else if (response.status === 0) {
                    icon.removeClass('fa-eye').addClass(
                        'fa-eye-slash');
                    tooltip.text('Inactive');
                    button.removeClass('text-primary').addClass(
                        'text-secondary');
                }

                // Cập nhật trạng thái trong modal
                var modalStatus = $('#modal' + blogId +
                    ' .modal-status');
                if (modalStatus.length) {
                    if (response.status === 1) {
                        modalStatus.removeClass(
                            'text-secondary').addClass(
                            'text-primary').text('Show');
                    } else {
                        modalStatus.removeClass('text-primary')
                            .addClass('text-secondary').text(
                                'Hidden');
                    }
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr.responseText);
            }
        });
    });
});