<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * [QUEUE] Email thông báo khi admin đổi trạng thái đơn hàng
 * (pending -> processing -> completed / cancelled).
 */
class OrderStatusUpdatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 3;

    public function __construct(public Order $order)
    {
    }

    public function build()
    {
        return $this->subject('Cập nhật đơn hàng #' . $this->order->order_code . ' - ' . $this->order->status_label)
            ->view('emails.order-status-updated')
            ->with([
                'order' => $this->order,
            ]);
    }
}
