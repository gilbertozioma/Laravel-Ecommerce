<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Product;
use App\Mail\LowStockNotificationMail;
use Illuminate\Support\Facades\Mail;

class NotifyLowStock implements ShouldQueue
{
    use Queueable;

    protected Product $product;

    /**
     * Create a new job instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $admin = config('mail.admin_email', env('ADMIN_EMAIL')) ?: env('ADMIN_EMAIL');
        if (! $admin) {
            return;
        }

        Mail::to($admin)->send(new LowStockNotificationMail($this->product));
    }
}
