<?php

namespace Fickrr\Support;

/**
 * Permanent redirects for product URLs that blog posts still use.
 */
class LegacyItemSlugs
{
    public const MAP = [
        'justfans-v810-premium-content-creators-saas-platform-nulled' => 'justfans-premium-content-creators-saas-platform-all-versions-available',
        'wasender-bulk-whatsapp-sender-group-sender-whatsapp-auto-reply-bot-v360-nulled' => 'wasender-bulk-whatsapp-sender-group-sender-whatsapp-auto-reply-bot-v360',
        'lamat-ultimate-online-dating-platform-video-dating-live-stream-tinder-tiktok-tango-instagram' => 'lamat-ultimate-online-dating-platform-video-dating-live-stream-all-versions-available',
        'whatsjet-saas-a-whatsapp-marketing-platform-with-bulk-sending-nulled-no-license-key-needed' => 'whatsjet-saas-a-whatsapp-marketing-platform-with-bulk-sending-all-versions-available',
        'premium-url-shortener-v72-nulled-without-license-key-needed' => 'premium-url-shortener-all-versions-available',
        'shopperzz-pwa-ecommerce-and-whatsapp-ordering-nulled-without-license-key-needed' => 'shopperzz-pwa-ecommerce-and-whatsapp-ordering-all-versions-available',
        'whatscloud-seamless-cloud-api-integration-saas-free-script-installation-without-license-key-needed' => 'whatscloud-seamless-cloud-api-integration-saas-free-script-installation-all-versions-available',
    ];

    public static function target(string $slug): ?string
    {
        return self::MAP[$slug] ?? null;
    }
}
