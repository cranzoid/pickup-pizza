<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email? : The email address to send test emails to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all email templates with sample data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? config('mail.from.address');
        
        // Find a sample order or use a fake one
        $order = Order::first();
        
        if (!$order) {
            $this->error('No orders found in the database to use for testing');
            return 1;
        }
        
        $this->info('Testing email templates using order #' . $order->order_number);
        $this->info('Sending test emails to: ' . $email);
        
        // Test OrderConfirmation email
        $this->info('Sending Order Confirmation email...');
        Mail::to($email)->send(new \App\Mail\OrderConfirmation($order));
        
        // Test NewOrderNotification email
        $this->info('Sending New Order Notification email...');
        Mail::to($email)->send(new \App\Mail\NewOrderNotification($order));
        
        // Test OrderStatusUpdate email with different statuses
        foreach (['preparing', 'ready', 'cancelled'] as $status) {
            $this->info("Sending Order Status Update email for status: {$status}...");
            $order->status = $status;
            Mail::to($email)->send(new \App\Mail\OrderStatusUpdate($order));
        }
        
        // Test AdminNotification email
        $this->info('Sending Admin Notification email...');
        Mail::to($email)->send(new \App\Mail\AdminNotification(
            'Test Admin Notification',
            'This is a test of the admin notification email template.',
            'info',
            'Everything is working correctly!',
            $order,
            'Go to Dashboard',
            url('/admin')
        ));
        
        $this->info('All test emails have been sent. Please check your inbox.');
        
        return 0;
    }
} 