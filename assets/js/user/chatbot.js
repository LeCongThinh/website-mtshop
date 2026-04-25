// assets/js/chatbot.js
$(document).ready(function () {
    const chatContent = $('#chat-content');
    const userInput = $('#user-input');
    const sendBtn = $('#send-btn');

    sendBtn.on('click', function () {
        let message = userInput.val().trim();
        if (message === "") return;

        // 1. Hiển thị tin nhắn của User
        chatContent.append(`
            <div class="text-end mb-2">
                <span class="bg-primary text-white p-2 rounded d-inline-block">Bạn: ${message}</span>
            </div>
        `);
        userInput.val(''); // Xóa ô nhập
        chatContent.scrollTop(chatContent[0].scrollHeight); // Cuộn xuống cuối

        // 2. Hiển thị trạng thái "AI đang trả lời..."
        const typingId = 'typing-' + Date.now();
        chatContent.append(`
            <div id="${typingId}" class="text-start mb-2">
                <span class="bg-light p-2 rounded d-inline-block">Trợ lý ảo MTShop đang soạn câu trả lời...</span>
            </div>
        `);

        // 3. Gửi AJAX đến Backend PHP
        $.ajax({
            url: 'functions/ai/chat_endpoint.php',
            method: 'POST',
            data: JSON.stringify({ message: message }),
            contentType: 'application/json',
            success: function (response) {
                $(`#${typingId}`).remove(); // Xóa dòng "đang trả lời"

                let reply = response.reply || "Có lỗi xảy ra, vui lòng thử lại.";
                let formattedReply = reply
                    .replace(/\n\s*\n\s*\n/g, '\n\n') // Dọn dẹp dòng trống quá xa
                    .replace(/\n/g, '<br>');         // Biến \n thành xuống dòng HTML

                chatContent.append(`
                    <div class="text-start mb-2">
                        <div class="bg-light border p-2 rounded d-inline-block" style="max-width: 85%; line-height: 1.4;">
                            <b>Trợ lý ảo MTShop: </b> ${formattedReply}
                        </div>
                    </div>
                `);

                chatContent.scrollTop(chatContent[0].scrollHeight);
            },
            error: function () {
                $(`#${typingId}`).html('<span class="text-danger">Lỗi kết nối server.</span>');
            }
        });
    });

    // Cho phép nhấn Enter để gửi
    userInput.on('keypress', function (e) {
        if (e.which == 13) sendBtn.click();
    });
});