<?php
// Hàm hiển thị Trạng thái Đơn hàng
if (!function_exists('getOrderStatusBadge')) {
    function getOrderStatusBadge($status)
    {
        return match ($status) {
            'pending'   => ['class' => 'bg-warning-subtle text-warning border border-warning', 'text' => 'Chờ duyệt đơn'],
            'confirmed' => ['class' => 'bg-primary-subtle text-primary border border-primary', 'text' => 'Đã xác nhận đơn hàng'],
            'shipping'  => ['class' => 'bg-info-subtle text-info border border-info', 'text' => 'Đang giao hàng'],
            'delivered' => ['class' => 'bg-success-subtle text-success border border-success', 'text' => 'Giao thành công'],
            'cancelled' => ['class' => 'bg-danger-subtle text-danger border border-danger', 'text' => 'Đã hủy đơn'],
            default     => ['class' => 'bg-secondary-subtle text-secondary border border-secondary', 'text' => 'Không rõ']
        };
    }
}

// Hàm hiển thị Trạng thái thanh toán
if (!function_exists('getPaymentStatusBadge')) {
    function getPaymentStatusBadge($status)
    {
        return match ($status) {
            'pending'  => ['class' => 'bg-secondary-subtle text-secondary border border-secondary', 'text' => 'Chưa thanh toán'],
            'paid'     => ['class' => 'bg-success-subtle text-success border border-success', 'text' => 'Đã thanh toán'],
            'failed'   => ['class' => 'bg-danger-subtle text-danger border border-danger', 'text' => 'Thanh toán thất bại'],
            'refunded' => ['class' => 'bg-warning-subtle text-warning border border-warning', 'text' => 'Đã hoàn tiền'],
            default    => ['class' => 'bg-secondary', 'text' => 'Chưa rõ']
        };
    }
}
