<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $verificationUrl = url('/api/auth/verify?token=' . $this->user->verification_token);
        // Mail::raw("Chào {$this->user->fullname},\n\nVui lòng nhấp vào liên kết sau để kích hoạt tài khoản:\n{$verificationUrl}\n\nTrân trọng,\n" . env('APP_NAME'), function ($message) {
        //     $message->to($this->user->email)->subject('Xác nhận email đăng ký');
        // });
        Mail::send('emails.verification', [
            'user' => $this->user,
            'verificationUrl' => $verificationUrl
        ], function ($message) {
            $message->to($this->user->email)->subject('Xác nhận email đăng ký');
        });
    }
}
