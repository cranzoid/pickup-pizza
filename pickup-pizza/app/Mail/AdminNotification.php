<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $alertType;
    public $alertMessage;
    public $order;
    public $actionText;
    public $actionUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $subject,
        string $message = null,
        string $alertType = null,
        string $alertMessage = null,
        $order = null,
        string $actionText = null,
        string $actionUrl = null
    ) {
        $this->subject = $subject;
        $this->message = $message;
        $this->alertType = $alertType;
        $this->alertMessage = $alertMessage;
        $this->order = $order;
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[PISA Pizza Admin] ' . $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 