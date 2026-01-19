<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailySalesReportMail;

class SendDailySalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-sales-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily sales report email to admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->startOfDay();
        $orders = Order::whereDate('placed_at', $today)->with('items.product')->get();

        // aggregate sales per product
        $report = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $pid = $item->product_id;
                if (! isset($report[$pid])) {
                    $report[$pid] = ['product' => $item->product, 'quantity' => 0, 'revenue' => 0];
                }
                $report[$pid]['quantity'] += $item->quantity;
                $report[$pid]['revenue'] += $item->quantity * $item->price;
            }
        }

        $admin = config('mail.admin_email', env('ADMIN_EMAIL')) ?: env('ADMIN_EMAIL');
        if (! $admin) {
            $this->info('No admin email configured.');
            return 1;
        }

        Mail::to($admin)->send(new DailySalesReportMail($report, $today->toDateString()));

        $this->info('Daily sales report sent to ' . $admin);

        return 0;
    }
}
