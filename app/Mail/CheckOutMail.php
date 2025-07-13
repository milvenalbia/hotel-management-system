<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckOutMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dining_data;
    public $booking_data;
    public $other_data;
    /**
     * Create a new message instance.
     */
    public function __construct($dining_data,$booking_data,$other_data)
    {
        $this->dining_data = $dining_data;
        $this->booking_data = $booking_data;
        $this->other_data = $other_data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('hotel.ms.simulator@gmail.com', 'Liz Park'),
            subject: 'Notification, this is your copy of your check out invoice',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.check-out-mail',
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
