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
                'html' => '<p>This policy describes how ' . e($name) . ' (' . e($url) . ') handles information when you browse the store or place an order.</p>'
                    . '<p>We collect the name, email, phone number, and delivery address you submit at checkout, plus the items and quantities in the order. If you create an account, we also store the login details you choose.</p>'
                    . '<p>We use that information to ship the order after you pay, and to reply if you contact us. We do not sell customer lists.</p>'
                    . '<p>Card details are handled by the card processor. PayPal payments are handled by PayPal. This store does not take cash on delivery.</p>'
                    . '<p>You can ask us what we hold about you, or ask us to correct it, by emailing <a href="mailto:' . e($email) . '">' . e($email) . '</a>.</p>',
            ],
            'terms-and-conditions' => [
                'title' => 'Terms & Conditions',
                'html' => '<p>These terms apply to orders placed at ' . e($url) . ' for products sold by ' . e($name) . '.</p>'
                    . '<p>Product pages show the price, and checkout shows the shipping fee before you place the order. Placing an order is an offer to buy those goods for delivery to the address you enter.</p>'
                    . '<p>You pay online by card or PayPal before the order ships. An order is confirmed only after that payment succeeds. No shipment is prepared for an unpaid order.</p>'
                    . '<p>Stock is limited. If an item is no longer in stock when payment is confirmed, that part of the order is not confirmed.</p>'
                    . '<p>You agree to provide a name, phone number, and a delivery address where the shipment can be received.</p>'
                    . '<p>Questions about an order can be sent to <a href="mailto:' . e($email) . '">' . e($email) . '</a>.</p>',
            ],
            'refund-policy' => [
                'title' => 'Returns',
                'html' => '<p>' . e($name) . ' ships products to you. This page explains how to ask for a return. It does not promise a refund for every order.</p>'
                    . '<p>Contact <a href="mailto:' . e($email) . '">' . e($email) . '</a> with your order number if an item arrives damaged, is not what you ordered, or you want to return it. We will tell you whether the return is accepted and how to send the goods back.</p>'
                    . '<p>A return is recorded on the order only after the goods are received back or we confirm the return. A refund of an online payment is issued only when a return is accepted.</p>'
                    . '<p>Orders that have not shipped can be cancelled from our side before they leave. Once an order is marked shipped, use this contact path instead of expecting an automatic cancellation.</p>',
            ],
            'faq' => [
                'title' => 'FAQ',
                'html' => '<p><strong>What am I buying?</strong><br>Products that we pack and ship to your address.</p>'
                    . '<p><strong>How do I pay?</strong><br>By card or PayPal at checkout. The order ships after that payment is confirmed. Cash on delivery is not offered.</p>'
                    . '<p><strong>What details do you need?</strong><br>Your name, phone number, email, street address, city, and country, plus the quantity you want.</p>'
                    . '<p><strong>When does stock change?</strong><br>Stock is reduced when the paid order is confirmed. It is put back if that order is cancelled or returned.</p>'
                    . '<p><strong>How do I know an order shipped?</strong><br>You receive an email when the order is marked shipped. The order page shows the status: pending, confirmed, shipped, delivered, cancelled, or returned.</p>'
                    . '<p><strong>Who do I contact?</strong><br><a href="mailto:' . e($email) . '">' . e($email) . '</a></p>',
            ],
        ];
    }
}
