<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * [QUEUE] Email xác nhận đơn hàng.
 *
 * Implement ShouldQueue -> khi gọi Mail::to()->send($this),
 * Laravel sẽ TỰ ĐỘNG đẩy việc gửi mail vào bảng `jobs` (queue)
 * thay vì gửi ngay trong request, giúp trang "Đặt hàng thành công"
 * trả về ngay lập tức mà không phải chờ SMTP phản hồi.
 *
 * Yêu cầu chạy worker để xử lý hàng đợi:
 *   php artisan queue:work
 */
class OrderPlacedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Số lần thử lại nếu gửi thất bại (VD: SMTP tạm thời lỗi).
     */
    public $tries = 3;

    public function __construct(public Order $order)
    {
    }

    public function build()
    {
        return $this->subject('Xác nhận đơn hàng #' . $this->order->order_code)
            ->view('emails.order-placed')
            ->with([
                'order' => $this->order,
            ]);
    }
}
