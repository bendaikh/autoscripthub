<?php

namespace Fickrr\Support;

class PhysicalLegalCopy
{
    /**
     * Neutral store policies. They do not invent a company registration,
     * a street address, or customer counts.
     *
     * @return array<string, array{title: string, html: string}>
     */
    public static function pages(): array
    {
        $name = Store::name();
        $url = Store::url();
        $email = Store::supportEmail();

        return [
            'privacy-policy' => [
                'title' => 'Privacy Policy',
                'html' => '<p>This policy describes how ' . e($name) . ' (' . e($url) . ') handles information when you browse the store or place an order for a physical product.</p>'
                    . '<p>We collect the name, email, phone number, and delivery address you submit at checkout, plus the items and quantities in the order. If you create an account, we also store the login details you choose.</p>'
                    . '<p>We use that information to confirm the order, arrange delivery, collect cash on delivery when that is the payment method, and reply if you contact us. We do not sell customer lists.</p>'
                    . '<p>Payment card details are handled by the card processor when card payment is enabled. Cash on delivery does not require a card.</p>'
                    . '<p>You can ask us what we hold about you, or ask us to correct it, by emailing <a href="mailto:' . e($email) . '">' . e($email) . '</a>.</p>',
            ],
            'terms-and-conditions' => [
                'title' => 'Terms & Conditions',
                'html' => '<p>These terms apply to orders placed at ' . e($url) . ' for physical products sold by ' . e($name) . '.</p>'
                    . '<p>Product pages show the price, and checkout shows the shipping fee before you place the order. Placing an order is an offer to buy those goods for delivery to the address you enter.</p>'
                    . '<p>Cash on delivery is the usual way to pay. You pay the order total when the shipment is delivered. If card payment is offered, it is charged only after you complete that payment with the card processor.</p>'
                    . '<p>Stock is limited. An order is confirmed only when we accept it. If an item is no longer in stock, we will not confirm that part of the order.</p>'
                    . '<p>You agree to provide a name, phone number, and a delivery address where the shipment can be received.</p>'
                    . '<p>Questions about an order can be sent to <a href="mailto:' . e($email) . '">' . e($email) . '</a>.</p>',
            ],
            'refund-policy' => [
                'title' => 'Returns',
                'html' => '<p>' . e($name) . ' sells physical products that are shipped to you. This page explains how to ask for a return. It does not promise a refund for every order.</p>'
                    . '<p>Contact <a href="mailto:' . e($email) . '">' . e($email) . '</a> with your order number if an item arrives damaged, is not what you ordered, or you want to return it. We will tell you whether the return is accepted and how to send the goods back.</p>'
                    . '<p>A return is recorded on the order only after the goods are received back or we confirm the return. Cash collected on delivery is refunded only when a return is accepted.</p>'
                    . '<p>Orders that have not shipped can be cancelled from our side before they leave. Once an order is marked shipped, use this contact path instead of expecting an automatic cancellation.</p>',
            ],
            'faq' => [
                'title' => 'FAQ',
                'html' => '<p><strong>What am I buying?</strong><br>Physical products. Nothing is delivered as a download, licence key, or subscription.</p>'
                    . '<p><strong>How do I pay?</strong><br>Cash on delivery is available at checkout. You pay when the order is delivered. Card payment is offered only when it is turned on for the live store.</p>'
                    . '<p><strong>What details do you need?</strong><br>Your name, phone number, email, street address, city, and country, plus the quantity you want.</p>'
                    . '<p><strong>When does stock change?</strong><br>Stock is reduced when the order is confirmed or paid. It is put back if the order is cancelled or returned after that.</p>'
                    . '<p><strong>How do I know an order shipped?</strong><br>You receive an email when the order is marked shipped. The order page shows the status: pending, confirmed, shipped, delivered, cancelled, or returned.</p>'
                    . '<p><strong>Who do I contact?</strong><br><a href="mailto:' . e($email) . '">' . e($email) . '</a></p>',
            ],
        ];
    }
}
