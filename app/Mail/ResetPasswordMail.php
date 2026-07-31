<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email gửi liên kết đặt lại mật khẩu.
 *
 * Gửi đồng bộ (không queue) để đảm bảo người dùng nhận được email
 * ngay trong request, tránh trường hợp queue worker chưa chạy
 * làm người dùng không nhận được liên kết đặt lại mật khẩu.
 */
class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $token
    ) {
    }

    public function build()
    {
        return $this->subject('Đặt lại mật khẩu')
            ->view('emails.reset-password')
            ->with([
                'user' => $this->user,
                'resetUrl' => route('password.reset', [
                    'token' => $this->token,
                    'email' => $this->user->email,
                ]),
            ]);
    }
}
