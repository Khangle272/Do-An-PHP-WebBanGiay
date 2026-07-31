<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>Đặt lại mật khẩu</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 24px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden;">
        <div style="background: #e94560; padding: 24px; text-align: center;">
            <h1 style="color: #fff; margin: 0; font-size: 22px;">SneakerShop</h1>
        </div>

        <div style="padding: 24px;">
            <h2 style="margin-top: 0;">Yêu cầu đặt lại mật khẩu</h2>
            <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
            <p>Chúng tôi vừa nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Nhấn vào nút bên dưới để tiến hành
                đặt lại mật khẩu:</p>

            <div style="text-align: center; margin: 24px 0;">
                <a href="{{ $resetUrl }}"
                    style="display: inline-block; background: #e94560; color: #fff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    Đặt lại mật khẩu
                </a>
            </div>

            <p style="word-break: break-all; font-size: 13px; color: #666;">
                Hoặc sao chép liên kết sau vào trình duyệt:<br>
                <a href="{{ $resetUrl }}" style="color: #e94560;">{{ $resetUrl }}</a>
            </p>

            <p style="color: #999; font-size: 13px;">
                Liên kết này sẽ hết hạn sau 60 phút. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.
                Đây là email tự động, vui lòng không phản hồi lại email này.
            </p>
        </div>
    </div>
</body>

</html>
