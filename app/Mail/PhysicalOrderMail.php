<?php

namespace Fickrr\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PhysicalOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(public string $kind, public array $payload)
    {
    }

    public function build()
    {
        $shipped = $this->kind === 'shipped';
        $number = $this->payload['order']->order_number ?? '';

        return $this->subject($shipped
            ? 'Your order ' . $number . ' has shipped'
            : 'We received your order ' . $number)
            ->view($shipped ? 'emails.physical-order-shipped' : 'emails.physical-order-confirmation', $this->payload);
    }
}
