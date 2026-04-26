<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* 1. Wrapper quản lý vị trí toàn cụm */
    .zalo-wrapper {
        position: fixed;
        bottom: 170px;
        right: 30px;
        z-index: 9998;
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    /* 2. Nút tròn Zalo (Bỏ position fixed ở đây để theo Wrapper) */
    #zalo-circle {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #0068e1;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        animation: pulse-zalo 2s infinite;
        position: relative;
    }

    #zalo-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* 3. Tooltip nằm NGOÀI vùng overflow của circle */
    .zalo-tooltip {
        position: absolute;
        right: 75px;
        background: #0068e1;
        color: white;
        padding: 5px 15px;
        border-radius: 10px;
        font-size: 13px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        z-index: 9999;
    }

    /* 4. Logic Hover: Hover vào cụm wrapper thì hiện tooltip */
    .zalo-wrapper:hover .zalo-tooltip {
        opacity: 1;
        visibility: visible;
        right: 80px;
    }

    .zalo-wrapper:hover #zalo-circle {
        transform: scale(1.1);
        border-color: #0056b3;
    }

    @keyframes pulse-zalo {
        0% {
            box-shadow: 0 0 0 0 rgba(0, 104, 225, 0.5);
        }

        70% {
            box-shadow: 0 0 0 15px rgba(0, 104, 225, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(0, 104, 225, 0);
        }
    }
</style>

<?php
$zalo_number = "0379711416";
?>

<div class="zalo-wrapper">
    <div class="zalo-tooltip">Chat với nhân viên</div>
    <a href="https://zalo.me/<?php echo $zalo_number; ?>" target="_blank" id="zalo-circle">
        <img src="assets/images/logo/logo-zalo.png" alt="Zalo Chat">
    </a>
</div>