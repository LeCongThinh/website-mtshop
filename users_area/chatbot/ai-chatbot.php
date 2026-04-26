<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* Nút tròn nổi */
    #chat-circle {
        position: fixed;
        bottom: 70px;
        right: 30px;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        transition: transform 0.3s ease;
        animation: pulse-border 2s infinite;
    }

    #chat-circle:hover {
        transform: scale(1.1) rotate(5deg);
    }

    /* Tăng kích thước cho icon logo */
    #chat-circle img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        animation: shake 5s infinite;
    }

    /* Khung chat */
    #chat-box {
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 350px;
        max-width: 90%;
        height: 500px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
        display: none;
        flex-direction: column;
        z-index: 9999;
        overflow: hidden;
    }

    /* Tiêu đề khung chat */
    .chat-header {
        background: #0d6efd;
        color: white;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Vùng chứa tin nhắn */
    #chat-content {
        flex: 1;
        height: 350px;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 15px;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 10px;
        scrollbar-width: thin;
    }

    /* Tùy chỉnh thanh cuộn cho đẹp (Optional) */
    #chat-content::-webkit-scrollbar {
        width: 6px;
    }

    #chat-content::-webkit-scrollbar-thumb {
        background-color: #ced4da;
        border-radius: 10px;
    }

    .chat-tooltip {
    position: absolute;
    right: 80px;
    background: #333;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
    }

    #chat-circle:hover .chat-tooltip {
        opacity: 1;
    }

    /* Style tin nhắn */
    .msg {
        padding: 8px 12px;
        border-radius: 10px;
        max-width: 85%;
        font-size: 14px;
        line-height: 1.4;
    }

    .msg-ai {
        background: #e9ecef;
        align-self: flex-start;
        color: #333;
        white-space: pre-line;
    }

    .msg-user {
        background: #0d6efd;
        color: white;
        align-self: flex-end;
    }

    /* Vùng nhập liệu */
    .chat-input-area {
        padding: 10px;
        border-top: 1px solid #eee;
        display: flex;
    }

    .typing-container {
    display: flex;
    align-items: center;
    gap: 8px; /* Khoảng cách giữa chữ và dấu chấm */
    padding: 8px 12px;
    background-color: #f1f0f0;
    border-radius: 12px;
    width: fit-content;
    font-size: 14px;
    color: #555;
    font-family: sans-serif;
    }

    .typing-text {
        font-style: italic;
    }

    .typing-indicator {
        display: flex;
        align-items: center;
    }

    .typing-indicator span {
        height: 4px; /* Thu nhỏ dấu chấm một chút cho cân đối với chữ */
        width: 4px;
        margin: 0 1px;
        background-color: #888;
        display: block;
        border-radius: 50%;
        opacity: 0.4;
        animation: typing 1s infinite;
    }

    /* Giữ nguyên hiệu ứng nhảy của dấu chấm */
    .typing-indicator span:nth-child(1) { animation-delay: 0s; }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
        0%, 100% { transform: translateY(0); opacity: 0.4; }
        50% { transform: translateY(-3px); opacity: 1; }
    }

    @keyframes pulse-border {
        0% {
            box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4);
        }
        70% {
            box-shadow: 0 0 0 20px rgba(13, 110, 253, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
        }
    }

    /* Keyframes cho hiệu ứng rung icon */
    @keyframes shake {
        0%, 90%, 100% { transform: rotate(0); }
        92% { transform: rotate(-10deg); }
        94% { transform: rotate(10deg); }
        96% { transform: rotate(-10deg); }
        98% { transform: rotate(10deg); }
    }
</style>

<div id="chat-circle">
    <div class="chat-tooltip">Tư vấn ngay!</div>
    <img src="assets/images/logo/chat-bot.png" alt="AI Chatbot">
</div>


<div id="chat-box">
    <div class="chat-header">
        <span><i class="fas fa-robot me-2"></i> Trợ lý ảo MTShop</span>
        <i class="fas fa-times" id="close-chat" style="cursor:pointer;"></i>
    </div>

    <div id="chat-content">
        <div class="msg msg-ai">Chào bạn! MTShop có thể giúp gì cho bạn?</div>
    </div>

    <div class="chat-input-area">
        <div class="input-group">
            <input type="text" id="user-input" class="form-control" placeholder="Bạn muốn tư vấn về sản phẩm nào?">
            <button id="send-btn" class="btn btn-primary shadow-none"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Đóng mở khung chat
        $('#chat-circle').click(function() {
            $('#chat-box').fadeIn();
            $(this).hide();
        });
        $('#close-chat').click(function() {
            $('#chat-box').fadeOut();
            $('#chat-circle').show();
        });

        function appendMessage(role, text) {
            let msgClass = (role === 'ai') ? 'msg-ai' : 'msg-user';
            let label = (role === 'ai') ? '' : '';
            $('#chat-content').append(`<div class="msg ${msgClass}">${label}${text}</div>`);
            $('#chat-content').scrollTop($('#chat-content')[0].scrollHeight);
        }

        function handleSend() {
            let msg = $('#user-input').val().trim();
            if (!msg) return;

            appendMessage('user', msg);
            $('#user-input').val('');

            // Hiệu ứng loading
            let typingId = 'typing-' + Date.now();
            let typingHtml = `
                <div id="${typingId}" class="msg msg-ai typing-container">
                    <span class="typing-text">MTShop đang soạn tin</span>
                    <div class="typing-indicator">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>`;
            $('#chat-content').append(typingHtml);
            

            $.ajax({
                url: 'functions/ai/chat_endpoint.php',
                type: 'POST',
                data: JSON.stringify({
                    message: msg
                }),
                contentType: 'application/json',
                success: function(res) {
                    $(`#${typingId}`).remove();
                    appendMessage('ai', res.reply || "Xin lỗi, tôi đang bận.");
                },
                error: function() {
                    $(`#${typingId}`).html('<span class="text-danger">Lỗi kết nối server.</span>');
                }
            });
        }

        $('#send-btn').click(handleSend);
        $('#user-input').keypress(function(e) {
            if (e.which == 13) handleSend();
        });
    });
</script>