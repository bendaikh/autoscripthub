<?php

namespace Fickrr\Support;

/**
 * ash_fix7: unique titles/descriptions and product meta fallbacks.
 * View-layer only. Does not write product names or prices.
 */
class PageMeta
{
    public static function locale(): string
    {
        return class_exists(LocaleUrl::class) && LocaleUrl::current() === 'ru' ? 'ru' : 'en';
    }

    public static function plain(?string $v): string
    {
        $v = html_entity_decode((string) $v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $v = html_entity_decode(strip_tags($v), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $v = trim(preg_replace('/\s+/u', ' ', strip_tags($v)));
        return $v;
    }

    public static function isGeneric(?string $desc, ?string $siteDesc = null): bool
    {
        $d = self::plain($desc);
        if ($d === '') {
            return true;
        }
        $site = self::plain($siteDesc);
        if ($site !== '' && mb_strtolower($d) === mb_strtolower($site)) {
            return true;
        }
        $needles = [
            'discover premium digital tools',
            'smart solutions crafted by autoscripthub',
            'buy windows, office, steam, game keys, scripts and saas tools with instant digital delivery',
            'buy premium digital tools, templates, scripts and licenses. fast delivery, russian-language support',
        ];
        $low = mb_strtolower($d);
        foreach ($needles as $n) {
            if (strpos($low, $n) !== false) {
                return true;
            }
        }
        return false;
    }

    /** Short auto-generated listing lines such as "Steam digital products and licenses." */
    public static function isThinListingDesc(?string $desc): bool
    {
        $d = self::plain($desc);
        if ($d === '') {
            return true;
        }
        if (preg_match('/^.+\s+digital (products and licenses|gift cards)\.?$/iu', $d)) {
            return true;
        }
        if (preg_match('/instant digital delivery/iu', $d)) {
            return true;
        }
        return mb_strlen($d) < 40;
    }

    public static function clamp(string $text, int $max = 158): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        if (mb_strlen($text) <= $max) {
            return $text;
        }
        $cut = mb_substr($text, 0, $max - 1);
        if (preg_match('/^(.+)\s+\S*$/u', $cut, $m) && mb_strlen($m[1]) >= 80) {
            $cut = $m[1];
        }
        return rtrim($cut, ' .,;:-').'…';
    }

    public static function pages(): array
    {
        $brand = 'autoscripthub';
        return [
            'faq' => [
                'en' => [
                    'title' => 'FAQ — Payments, delivery and support | '.$brand,
                    'desc' => 'Answers about buying on Auto Script Hub: payment methods (PayPal, card, Crypto, Binance Pay, KAST, Wise, USDT), delivery by message after payment is confirmed, refunds and how to contact support.',
                ],
                'ru' => [
                    'title' => 'Вопросы и ответы — оплата, доставка и поддержка | '.$brand,
                    'desc' => 'Ответы о покупках на Auto Script Hub: способы оплаты (PayPal, карта, криптовалюта, Binance Pay, KAST, Wise, USDT), доставка сообщением после подтверждения оплаты, возвраты и как связаться с поддержкой.',
                ],
            ],
            'refund-policy' => [
                'en' => [
                    'title' => 'Refund Policy — 15-day money-back | '.$brand,
                    'desc' => 'Read the Auto Script Hub refund policy: 15-day money-back if a key or account cannot be activated or does not work. How to request a refund after your purchase.',
                ],
                'ru' => [
                    'title' => 'Политика возврата — 15 дней гарантии | '.$brand,
                    'desc' => 'Политика возврата Auto Script Hub: 15 дней на возврат, если ключ или аккаунт не активируется или не работает. Как запросить возврат после покупки.',
                ],
            ],
            'privacy-policy' => [
                'en' => [
                    'title' => 'Privacy Policy | '.$brand,
                    'desc' => 'How Auto Script Hub collects, uses and protects your information, including account details and payments. Crypto payments are processed by our provider; we do not store wallet keys or card details.',
                ],
                'ru' => [
                    'title' => 'Политика конфиденциальности | '.$brand,
                    'desc' => 'Как Auto Script Hub собирает, использует и защищает ваши данные, включая сведения аккаунта и платежи. Криптоплатежи обрабатывает платёжный провайдер; мы не храним ключи кошелька или данные карт.',
                ],
            ],
            'terms-and-conditions' => [
                'en' => [
                    'title' => 'Terms and Conditions | '.$brand,
                    'desc' => 'Terms for using Auto Script Hub: browsing, purchasing digital products, licences, payments and your responsibilities as a customer or seller.',
                ],
                'ru' => [
                    'title' => 'Условия использования | '.$brand,
                    'desc' => 'Условия использования Auto Script Hub: просмотр сайта, покупка цифровых товаров, лицензии, оплата и обязанности покупателя или продавца.',
                ],
            ],
            'terms' => [
                'en' => [
                    'title' => 'Terms and Conditions | '.$brand,
                    'desc' => 'Terms for using Auto Script Hub: browsing, purchasing digital products, licences, payments and your responsibilities as a customer or seller.',
                ],
                'ru' => [
                    'title' => 'Условия использования | '.$brand,
                    'desc' => 'Условия использования Auto Script Hub: просмотр сайта, покупка цифровых товаров, лицензии, оплата и обязанности покупателя или продавца.',
                ],
            ],
            'about-us' => [
                'en' => [
                    'title' => 'About Us | '.$brand,
                    'desc' => 'Auto Script Hub is an online marketplace for Windows and Office keys, game keys, gift cards, subscriptions and PHP scripts, with support on Telegram and email.',
                ],
                'ru' => [
                    'title' => 'О нас | '.$brand,
                    'desc' => 'Auto Script Hub — онлайн-магазин ключей Windows и Office, игровых ключей, подарочных карт, подписок и PHP-скриптов, с поддержкой в Telegram и по электронной почте.',
                ],
            ],
            'about' => [
                'en' => [
                    'title' => 'About Us | '.$brand,
                    'desc' => 'Auto Script Hub is an online marketplace for Windows and Office keys, game keys, gift cards, subscriptions and PHP scripts, with support on Telegram and email.',
                ],
                'ru' => [
                    'title' => 'О нас | '.$brand,
                    'desc' => 'Auto Script Hub — онлайн-магазин ключей Windows и Office, игровых ключей, подарочных карт, подписок и PHP-скриптов, с поддержкой в Telegram и по электронной почте.',
                ],
            ],
            'contact' => [
                'en' => [
                    'title' => 'Contact Us — Telegram and email support | '.$brand,
                    'desc' => 'Contact Auto Script Hub on Telegram @autoscript_hub or email support@auto-script-hub.com for order help, delivery after payment confirmation, or product questions.',
                ],
                'ru' => [
                    'title' => 'Контакты — поддержка в Telegram и по почте | '.$brand,
                    'desc' => 'Свяжитесь с Auto Script Hub в Telegram @autoscript_hub или по почте support@auto-script-hub.com по заказам, доставке после подтверждения оплаты и вопросам о товарах.',
                ],
            ],
            'blog' => [
                'en' => [
                    'title' => 'Blog | '.$brand,
                    'desc' => 'Articles from Auto Script Hub about digital licences, keys, scripts and how to buy and use products on our store.',
                ],
                'ru' => [
                    'title' => 'Блог | '.$brand,
                    'desc' => 'Статьи Auto Script Hub о цифровых лицензиях, ключах, скриптах и о том, как покупать и использовать товары в нашем магазине.',
                ],
            ],
            'popular-items' => [
                'en' => [
                    'title' => 'Popular Items | '.$brand,
                    'desc' => 'Best-selling Windows and Office keys, game keys, gift cards and PHP scripts on Auto Script Hub. Delivery by message after payment is confirmed.',
                ],
                'ru' => [
                    'title' => 'Популярные товары | '.$brand,
                    'desc' => 'Самые продаваемые ключи Windows и Office, игровые ключи, подарочные карты и PHP-скрипты на Auto Script Hub. Доставка сообщением после подтверждения оплаты.',
                ],
            ],
            'new-releases' => [
                'en' => [
                    'title' => 'New Releases | '.$brand,
                    'desc' => 'Newly added digital products on Auto Script Hub: software keys, games, gift cards and scripts. Delivery by message after payment is confirmed.',
                ],
                'ru' => [
                    'title' => 'Новинки | '.$brand,
                    'desc' => 'Новые цифровые товары на Auto Script Hub: ключи программ, игры, подарочные карты и скрипты. Доставка сообщением после подтверждения оплаты.',
                ],
            ],
            'featured-items' => [
                'en' => [
                    'title' => 'Featured Items | '.$brand,
                    'desc' => 'Featured software keys, games, gift cards and scripts selected on Auto Script Hub. Delivery by message after payment is confirmed.',
                ],
                'ru' => [
                    'title' => 'Рекомендуемые товары | '.$brand,
                    'desc' => 'Рекомендуемые ключи программ, игры, подарочные карты и скрипты на Auto Script Hub. Доставка сообщением после подтверждения оплаты.',
                ],
            ],
            'free-items' => [
                'en' => [
                    'title' => 'Free Items | '.$brand,
                    'desc' => 'Free digital items available on Auto Script Hub. Check each product page for what is included and how delivery works after you request the item.',
                ],
                'ru' => [
                    'title' => 'Бесплатные товары | '.$brand,
                    'desc' => 'Бесплатные цифровые товары на Auto Script Hub. На странице каждого товара указано, что входит в комплект и как происходит выдача после заявки.',
                ],
            ],
        ];
    }

    public static function normalizePath(?string $path = null): string
    {
        $path = $path ?? (string) request()->path();
        $path = strtolower(trim($path, '/'));
        $path = preg_replace('#^ru/#', '', $path);
        return $path;
    }

    public static function forSlug(?string $slug, ?string $locale = null): ?array
    {
        $slug = strtolower(trim((string) $slug, '/'));
        $slug = preg_replace('#^ru/#', '', $slug);
        if (strpos($slug, '/') !== false) {
            $parts = explode('/', $slug);
            $slug = end($parts);
        }
        $locale = $locale ?: self::locale();
        $all = self::pages();
        if (!isset($all[$slug][$locale]) && !isset($all[$slug]['en'])) {
            return null;
        }
        $row = $all[$slug][$locale] ?? $all[$slug]['en'];
        return is_array($row) ? $row : null;
    }

    public static function forPath(?string $path = null, ?string $locale = null): ?array
    {
        $path = self::normalizePath($path);
        if ($path === '' || $path === 'ru') {
            return null;
        }
        return self::forSlug($path, $locale);
    }

    public static function listingDesc(string $heading, bool $isCategory = true, ?string $locale = null): string
    {
        $heading = self::plain($heading);
        if ($heading === '') {
            $heading = $isCategory ? 'products' : 'our shop';
        }
        $locale = $locale ?: self::locale();
        if ($locale === 'ru') {
            $text = $isCategory
                ? 'Купить '.$heading.' на Auto Script Hub. Цифровая доставка сообщением после подтверждения оплаты. Оплата PayPal, картой, криптовалютой, Binance Pay, KAST, Wise и USDT.'
                : 'Каталог цифровых товаров Auto Script Hub: ключи Windows и Office, игры, подарочные карты и скрипты. Доставка сообщением после подтверждения оплаты.';
        } else {
            $text = $isCategory
                ? 'Buy '.$heading.' on Auto Script Hub. Digital delivery by message after payment is confirmed. Pay with PayPal, card, Crypto, Binance Pay, KAST, Wise or USDT.'
                : 'Browse digital products on Auto Script Hub: Windows and Office keys, games, gift cards and scripts. Delivery by message after payment is confirmed.';
        }
        return self::clamp($text);
    }

    public static function itemFallback(string $name, string $category, string $short, $price = null, ?string $locale = null): string
    {
        $name = self::plain($name);
        $category = self::plain($category);
        $short = self::plain($short);
        // drop delivery-filler phrases from the short text so we do not say "instant"
        $short = trim(preg_replace('/\b(instant\s+(digital\s+)?delivery|мгновенн\w*\s+(цифров\w*\s+)?(доставк|выдач)\w*)\b/iu', ' ', $short));
        $short = trim(preg_replace('/\s+/u', ' ', $short));
        if (self::isGeneric($short)) {
            $short = '';
        }
        // one or two sentences from the short description, not the whole body
        if ($short !== '' && preg_match('/^(.{40,220}?)([.!?]|$)(\s|$)/u', $short, $m)) {
            $short = trim($m[1].(isset($m[2]) && $m[2] !== '' ? $m[2] : '.'));
        } elseif (mb_strlen($short) > 180) {
            $short = rtrim(mb_substr($short, 0, 180), ' .,;').'…';
        }
        $locale = $locale ?: self::locale();
        if ($locale === 'ru') {
            $parts = [];
            if ($name !== '') {
                $parts[] = $name;
            }
            if ($category !== '') {
                $parts[] = 'Категория: '.$category.'.';
            }
            if ($short !== '') {
                $parts[] = $short;
            }
            $parts[] = 'Доставка сообщением после подтверждения оплаты.';
            $text = trim(implode(' ', $parts));
        } else {
            $parts = [];
            if ($name !== '') {
                $parts[] = $name;
            }
            if ($category !== '') {
                $parts[] = 'in '.$category.'.';
            }
            if ($short !== '') {
                $parts[] = $short;
            }
            $parts[] = 'Delivery by message after payment is confirmed.';
            $text = trim(implode(' ', $parts));
            $text = preg_replace('/\s+\./', '.', $text);
        }
        if ($price !== null && $price !== '' && class_exists(ItemI18n::class) && is_numeric($price) && (float) $price > 0) {
            $text = ItemI18n::descriptionWithPrice($text, $price);
        }
        return self::clamp($text);
    }
}
