import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

/**
 * [SOCKET] Cấu hình Laravel Echo để kết nối tới Reverb (WebSocket server).
 *
 * Chỉ khởi tạo khi trang có thẻ <meta name="app-user-logged-in"> (đã đăng
 * nhập) - vì mọi channel trong routes/channels.php đều là private channel,
 * cần request "broadcasting/auth" (đã đăng ký sẵn qua ->withBroadcasting()
 * trong bootstrap/app.php) để xác thực trước khi được lắng nghe.
 *
 * Biến VITE_REVERB_* được đọc từ file .env (đã có sẵn ví dụ trong
 * .env.example), Vite sẽ tự nhúng vào bundle lúc build.
 */
window.Pusher = Pusher;

const isLoggedIn = document.querySelector('meta[name="app-user-logged-in"]')?.content === '1';

if (isLoggedIn && import.meta.env.VITE_REVERB_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}
if (window.Echo) {
    window.Echo.private('admin.orders')
        .listen('.order.placed', (e) => {
            console.log('🔔 Có đơn hàng mới:', e);
        });
}