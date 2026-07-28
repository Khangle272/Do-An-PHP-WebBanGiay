<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Cập nhật đơn hàng</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 24px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden;">
        <div style="background: #e94560; padding: 24px; text-align: center;">
            <h1 style="color: #fff; margin: 0; font-size: 22px;">SneakerShop</h1>
        </div>

        <div style="padding: 24px;">
            <h2 style="margin-top: 0;">Đơn hàng của bạn vừa được cập nhật</h2>
            <p>Xin chào <strong>{{ $order->full_name }}</strong>,</p>
            <p>
                Đơn hàng <strong>#{{ $order->order_code }}</strong> hiện có trạng thái mới:
            </p>

            <div
                style="display: inline-block; padding: 10px 20px; border-radius: 8px; background: #fde8e8; color: #e94560; font-weight: 700; margin: 12px 0;">
                {{ $order->status_label }}
            </div>

            <p style="color: #999; font-size: 13px; margin-top: 24px;">
                Bạn có thể xem chi tiết đơn hàng trong mục "Đơn hàng của tôi" trên website.
                Đây là email tự động, vui lòng không phản hồi lại email này.
            </p>
        </div>
    </div>
</body>

</html>
