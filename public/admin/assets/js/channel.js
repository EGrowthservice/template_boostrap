document.addEventListener('DOMContentLoaded', function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Add event listeners for toggle buttons
    document.querySelectorAll('.toggle-status-btn').forEach(button => {
        button.addEventListener('click', function() {
            const channelId = this.dataset.channelId;
            const currentStatus = this.dataset.channelStatus;
            const url = `/channel/togglestatus/${channelId}`;

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        current_status: currentStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.alert.type === 'success') {
                        // Update button appearance
                        this.dataset.channelStatus = data.status;
                        this.classList.toggle('text-primary', data.status == 1);
                        this.classList.toggle('text-secondary', data.status == 0);
                        this.querySelector('i').className = data.status == 1 ?
                            'fas fa-eye' : 'fas fa-eye-slash';
                        this.querySelector('span').innerText = data.status == 1 ?
                            'Active' : 'Inactive';

                        // Show success Toast
                        Toast.fire({
                            icon: 'success',
                            title: data.alert.message
                        });
                    } else {
                        // Show error Toast
                        Toast.fire({
                            icon: 'error',
                            title: 'Failed to update channel status.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Show error Toast
                    Toast.fire({
                        icon: 'error',
                        title: 'An error occurred. Please try again later.'
                    });
                });
        });
    });
});