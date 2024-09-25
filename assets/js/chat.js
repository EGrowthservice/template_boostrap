jQuery(document).ready(function () {

    $(".chat-list a").click(function () {
        $(".chatbox").addClass('showbox');
        return false;
    });

    $(".chat-icon").click(function () {
        $(".chatbox").removeClass('showbox');
    });


}); jQuery(document).ready(function () {
    // Khi nhấn vào một phần tử trong chat-list
    $(".chat-list a").click(function () {
        // Lấy thông tin người dùng từ phần tử được nhấn
        var userName = $(this).find("h3").text(); // Tên người dùng
        var userStatus = $(this).find("p").text(); // Trạng thái người dùng

        // Cập nhật thông tin trong chatbox
        $(".chatbox .msg-head h3").text(userName);
        $(".chatbox .msg-head p").text(userStatus);

        // Hiển thị chatbox
        $(".chatbox").addClass('showbox');

        // Ngăn chặn hành vi mặc định của liên kết
        return false;
    });

    // Khi nhấn vào biểu tượng chat để đóng chatbox
    $(".chat-icon").click(function () {
        $(".chatbox").removeClass('showbox');
    });
});


// Lấy tất cả các thẻ a trong chat-list
const chatLinks = document.querySelectorAll('.chat-list a');

// Lấy iframe
const chatIframe = document.querySelector('.chatbox iframe');

// Gán sự kiện click cho mỗi thẻ a
chatLinks.forEach(link => {
    link.addEventListener('click', function (event) {
        event.preventDefault(); // Ngăn chặn hành động mặc định của thẻ a

        // Lấy URL từ thuộc tính data-url
        const url = this.getAttribute('data-url');

        // Làm mờ iframe trước khi thay đổi nội dung
        chatIframe.classList.remove('loaded');

        // Thay đổi src của iframe để hiển thị trang tương ứng
        chatIframe.src = url;
    });
});

// Sự kiện khi iframe đã tải xong nội dung
chatIframe.addEventListener('load', function () {
    // Khi iframe đã tải xong nội dung, thêm lớp 'loaded' để làm hiện dần iframe
    chatIframe.classList.add('loaded');
});

