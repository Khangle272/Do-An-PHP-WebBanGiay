<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Xác nhận đơn hàng</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 24px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden;">
        <div style="background: #e94560; padding: 24px; text-align: center;">
            <h1 style="color: #fff; margin: 0; font-size: 22px;">SneakerShop</h1>
        </div>

        <div style="padding: 24px;">
            <h2 style="margin-top: 0;">Cảm ơn bạn đã đặt hàng!</h2>
            <p>Xin chào <strong>{{ $order->full_name }}</strong>,</p>
            <p>Đơn hàng <strong>#{{ $order->order_code }}</strong> của bạn đã được ghi nhận và đang chờ xác nhận.</p>

            <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
                <tr>
                    <td style="padding: 8px 0; color: #666;">Người nhận</td>
                    <td style="padding: 8px 0; text-align: right;">{{ $order->full_name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Số điện thoại</td>
                    <td style="padding: 8px 0; text-align: right;">{{ $order->phone }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Địa chỉ giao hàng</td>
                    <td style="padding: 8px 0; text-align: right;">{{ $order->address }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Phí vận chuyển</td>
                    <td style="padding: 8px 0; text-align: right;">{{ number_format($order->shipping_fee) }}đ</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; font-weight: 700; border-top: 1px solid #eee;">Tổng cộng</td>
                    <td style="padding: 12px 0; text-align: right; font-weight: 700; color: #e94560; border-top: 1px solid #eee;">
                        {{ number_format($order->total_price) }}đ
                    </td>
                </tr>
            </table>

            <p style="color: #999; font-size: 13px;">
                Chúng tôi sẽ liên hệ với bạn qua số điện thoại trên để xác nhận đơn hàng trong thời gian sớm nhất.
                Đây là email tự động, vui lòng không phản hồi lại email này.
            </p>
        </div>
    </div>
</body>

</html>
