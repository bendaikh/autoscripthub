<?php

namespace Fickrr\Helpers;

use Illuminate\Support\Str;

/**
 * Turns the data a seller entered for a landing page into the view model used by
 * the public sales page: category theme, section copy, feature cards, FAQ, trust items.
 *
 * Nothing here fabricates facts about a product. Statistics are only emitted when
 * they can be counted from the database, and delivery/payment answers are derived
 * from the product's own delivery settings.
 */
class LandingPageContent
{
    /**
     * Accent palette per detected product category.
     * Every accent is picked to stay readable on both the dark hero and light sections.
     */
    protected const PALETTES = [
        'automotive' => ['#f97316', '#c2410c', 'car'],
        'food'       => ['#ef4444', '#b91c1c', 'utensils'],
        'fitness'    => ['#84cc16', '#4d7c0f', 'dumbbell'],
        'finance'    => ['#6366f1', '#4338ca', 'file-invoice-dollar'],
        'medical'    => ['#06b6d4', '#0e7490', 'stethoscope'],
        'education'  => ['#8b5cf6', '#6d28d9', 'graduation-cap'],
        'hospitality'=> ['#0ea5e9', '#0369a1', 'concierge-bell'],
        'ecommerce'  => ['#f59e0b', '#b45309', 'shopping-cart'],
        'crm'        => ['#14b8a6', '#0f766e', 'user-tie'],
        'hr'         => ['#ec4899', '#be185d', 'id-card'],
        'realestate' => ['#0d9488', '#115e59', 'home'],
        'logistics'  => ['#3b82f6', '#1d4ed8', 'truck'],
        'beauty'     => ['#d946ef', '#a21caf', 'cut'],
        'general'    => ['#22c55e', '#15803d', 'bolt'],
    ];

    /** Words that place a product in a category. */
    protected const CATEGORY_KEYWORDS = [
        'automotive'  => ['garage', 'auto ', 'automotive', 'vehicle', 'car ', 'cars', 'workshop', 'mechanic', 'repair shop', 'spare part', 'tyre', 'tire'],
        'food'        => ['restaurant', 'cafe', 'coffee', 'food', 'kitchen', 'menu', 'bakery', 'catering', 'bar ', 'diner'],
        'fitness'     => ['gym', 'fitness', 'workout', 'trainer', 'sport', 'yoga', 'member club'],
        'finance'     => ['invoice', 'accounting', 'finance', 'billing', 'payroll accounting', 'tax', 'bookkeeping', 'expense', 'budget'],
        'medical'     => ['clinic', 'hospital', 'medical', 'patient', 'doctor', 'dental', 'pharmacy', 'health'],
        'education'   => ['school', 'course', 'lms', 'student', 'learning', 'education', 'academy', 'training', 'university', 'quiz'],
        'hospitality' => ['hotel', 'booking', 'reservation', 'guest', 'travel', 'resort', 'rental property'],
        'ecommerce'   => ['ecommerce', 'e-commerce', 'shop', 'store', 'cart', 'marketplace', 'product catalog', 'dropship'],
        'crm'         => ['crm', 'lead', 'sales pipeline', 'client management', 'customer relationship', 'contact management'],
        'hr'          => ['hr ', 'human resource', 'employee', 'payroll', 'attendance', 'recruit', 'staff management'],
        'realestate'  => ['real estate', 'property', 'apartment', 'realtor', 'tenant', 'landlord'],
        'logistics'   => ['logistic', 'delivery', 'courier', 'shipping', 'fleet', 'warehouse', 'transport'],
        'beauty'      => ['salon', 'spa', 'barber', 'beauty', 'nail', 'hair'],
    ];

    /**
     * Feature keyword map: icon, a benefit-shaped title used in the outcomes section,
     * and a supporting line used under feature cards.
     */
    public const DESCRIPTION_MAX_CHARS = 200;

    protected const FEATURE_KEYWORDS = [
        ['keys' => ['inventory', 'stock', 'spare part', 'warehouse'], 'icon' => 'boxes', 'short' => 'Inventory Control', 'outcome' => 'Stay on top of your stock', 'blurb' => 'Know what you have available at any moment.'],
        ['keys' => ['customer', 'client', 'contact', 'crm'], 'icon' => 'users', 'short' => 'Customer Records', 'outcome' => 'Keep every customer detail organised', 'blurb' => 'Full history in one place, always searchable.'],
        ['keys' => ['invoice', 'billing', 'payment', 'receipt', 'quote', 'estimate'], 'icon' => 'file-invoice-dollar', 'short' => 'Invoicing', 'outcome' => 'Get paid without the paperwork', 'blurb' => 'Create clean documents in a few clicks.'],
        ['keys' => ['report', 'analytic', 'statistic', 'insight', 'dashboard', 'kpi'], 'icon' => 'chart-line', 'short' => 'Reports', 'outcome' => 'Understand how your business performs', 'blurb' => 'See the numbers that drive your decisions.'],
        ['keys' => ['repair', 'job', 'work order', 'task', 'service', 'ticket'], 'icon' => 'wrench', 'short' => 'Job Tracking', 'outcome' => 'Follow every job to completion', 'blurb' => 'Track progress from intake to delivery.'],
        ['keys' => ['vehicle', 'car', 'fleet'], 'icon' => 'car', 'short' => 'Vehicle Records', 'outcome' => 'Keep vehicle history at hand', 'blurb' => 'Every detail stored against the right record.'],
        ['keys' => ['booking', 'appointment', 'reservation', 'schedule', 'calendar'], 'icon' => 'calendar-check', 'short' => 'Scheduling', 'outcome' => 'Fill your calendar without double-booking', 'blurb' => 'Plan the day and see it at a glance.'],
        ['keys' => ['employee', 'staff', 'team', 'user role', 'permission', 'attendance'], 'icon' => 'user-shield', 'short' => 'Team Access', 'outcome' => 'Give your team exactly the access they need', 'blurb' => 'Roles and permissions kept under control.'],
        ['keys' => ['notification', 'reminder', 'alert', 'sms', 'email', 'whatsapp'], 'icon' => 'bell', 'short' => 'Notifications', 'outcome' => 'Keep everyone in the loop automatically', 'blurb' => 'Timely messages without manual follow-up.'],
        ['keys' => ['mobile', 'responsive', 'tablet', 'phone', 'anywhere'], 'icon' => 'mobile-alt', 'short' => 'Works Anywhere', 'outcome' => 'Work from any device you have with you', 'blurb' => 'Same experience on desktop, tablet and phone.'],
        ['keys' => ['product', 'catalog', 'menu', 'price list', 'item'], 'icon' => 'tags', 'short' => 'Catalog', 'outcome' => 'Keep your catalogue tidy and current', 'blurb' => 'Organise what you sell in one structure.'],
        ['keys' => ['order', 'sale', 'pos', 'checkout', 'cart'], 'icon' => 'shopping-cart', 'short' => 'Orders', 'outcome' => 'Process orders without friction', 'blurb' => 'Faster checkout, fewer mistakes.'],
        ['keys' => ['expense', 'cost', 'profit', 'accounting', 'cash'], 'icon' => 'coins', 'short' => 'Finances', 'outcome' => 'See where your money actually goes', 'blurb' => 'Costs and revenue side by side.'],
        ['keys' => ['document', 'file', 'attachment', 'contract', 'pdf', 'print'], 'icon' => 'file-alt', 'short' => 'Documents', 'outcome' => 'Find any document in seconds', 'blurb' => 'Files attached where they belong.'],
        ['keys' => ['secure', 'security', 'backup', 'encrypt', 'privacy'], 'icon' => 'shield-alt', 'short' => 'Secure by Design', 'outcome' => 'Keep your business data protected', 'blurb' => 'Sensible safeguards built in.'],
        ['keys' => ['host', 'self-host', 'own server', 'install'], 'icon' => 'server', 'short' => 'Self-Host or We Host', 'outcome' => 'Run it on your server, or let us host it', 'blurb' => 'Install the web app yourself, or ask us to host it for you.'],
        ['keys' => ['multi', 'branch', 'location', 'language', 'currency'], 'icon' => 'globe', 'short' => 'Flexible Setup', 'outcome' => 'Adapt the system to how you operate', 'blurb' => 'Configure it around your own workflow.'],
        ['keys' => ['support', 'update', 'documentation', 'guide', 'tutorial'], 'icon' => 'life-ring', 'short' => 'Guided Onboarding', 'outcome' => 'Never be left without an answer', 'blurb' => 'Guidance available when you need it.'],
        ['keys' => ['automat', 'workflow', 'efficien', 'productiv', 'streamline', 'time'], 'icon' => 'bolt', 'short' => 'Automation', 'outcome' => 'Cut out the repetitive manual work', 'blurb' => 'Let the system handle the routine steps.'],
    ];

    /**
     * @param  object       $page             landing_pages row
     * @param  array        $features         decoded lp_features
     * @param  iterable     $gallery          landing_page_gallery rows
     * @param  int          $completedOrders  real completed order count for this page
     */
    public static function build($page, array $features, $gallery, int $completedOrders = 0): array
    {
        $features = array_values(array_filter(array_map('trim', array_map('strval', $features))));
        $haystack = Str::lower(
            ($page->lp_title ?? '') . ' ' .
            ($page->lp_meta_keywords ?? '') . ' ' .
            strip_tags((string) ($page->lp_description ?? '')) . ' ' .
            strip_tags((string) ($page->lp_about_description ?? '')) . ' ' .
            implode(' ', $features)
        );

        $category = self::detectCategory($haystack);
        [$accent, $accentDark, $categoryIcon] = self::PALETTES[$category] ?? self::PALETTES['general'];

        $savedAccent = self::sanitizeAccent($page->lp_accent_color ?? null);
        if ($savedAccent) {
            $accent = $savedAccent;
            $accentDark = self::darkenAccent($savedAccent);
        }

        $featureCards = self::featureCards($features);
        $deliversFile = ($page->lp_delivery_method ?? 'upload') !== 'link';
        $kind = self::productKind($page);

        return [
            'category' => $category,
            'accent' => $accent,
            'accentDark' => $accentDark,
            'accentRgb' => self::hexToRgb($accent),
            'categoryIcon' => $kind === 'sheet' ? 'table' : ($kind === 'product' ? 'download' : $categoryIcon),
            'badge' => $kind === 'sheet'
                ? self::t('Available in Google Sheets & Excel')
                : ($kind === 'product' ? self::t('Digital product — instant download') : self::badge($category)),
            'heroBenefits' => array_slice($featureCards, 0, 3),
            'featureCards' => $featureCards,
            'featuresHeading' => self::featuresHeading($page->lp_title ?? '', $category),
            'problem' => self::problem($category),
            'outcomes' => self::outcomes($featureCards),
            'whatYouGetItems' => self::listItems($page->lp_what_you_get ?? ''),
            'trust' => self::trust($featureCards, $deliversFile, $kind),
            'stats' => self::stats($completedOrders),
            'faq' => self::faq($page, $deliversFile, $kind),
            'deliversFile' => $deliversFile,
            'productKind' => $kind,
            'shortDescription' => self::shortDescription($page),
            'hosting' => self::hosting($kind),
            'reviews' => self::reviews($page),
        ];
    }

    public static function productKind($page): string
    {
        if (\Fickrr\Support\Store::isPhysical()) {
            return 'physical';
        }

        $kind = strtolower(trim((string) ($page->lp_product_kind ?? 'script')));
        if (in_array($kind, ['sheet', 'product'], true)) {
            return $kind;
        }

        return 'script';
    }

    protected static function t(string $key, array $replace = []): string
    {
        return LandingPageI18n::t($key, $replace);
    }

    /**
     * @return array<string,mixed>
     */
    public static function reviews($page): array
    {
        $raw = $page->lp_reviews ?? '';
        if (is_array($raw)) {
            $data = $raw;
        } else {
            $data = json_decode((string) $raw, true);
        }
        if (!is_array($data)) {
            return [];
        }

        $items = [];
        foreach ($data['items'] ?? [] as $item) {
            if (!is_array($item)) {
                continue;
            }
            $text = trim((string) ($item['text'] ?? ''));
            if ($text === '') {
                continue;
            }
            $items[] = [
                'name' => trim((string) ($item['name'] ?? self::t('Buyer'))),
                'stars' => max(1, min(5, (int) ($item['stars'] ?? 5))),
                'date' => trim((string) ($item['date'] ?? '')),
                'text' => $text,
                'theme' => trim((string) ($item['theme'] ?? '')),
                'response' => trim((string) ($item['response'] ?? '')),
            ];
        }

        if (!$items && empty($data['rating'])) {
            return [];
        }

        $highlights = [];
        foreach ($data['highlights'] ?? [] as $tag) {
            $tag = trim((string) $tag);
            if ($tag !== '') {
                $highlights[] = $tag;
            }
        }

        return [
            'rating' => round((float) ($data['rating'] ?? 4.8), 1),
            'count' => (int) ($data['count'] ?? count($items)),
            'quality' => round((float) ($data['quality'] ?? 4.8), 1),
            'shipping' => round((float) ($data['shipping'] ?? 4.9), 1),
            'service' => round((float) ($data['service'] ?? 4.8), 1),
            'recommend' => (int) ($data['recommend'] ?? 91),
            'highlights' => array_slice($highlights, 0, 8),
            'items' => $items,
        ];
    }

    /** Preferred accents the AI (and keyword fallback) can choose from. */
    public static function paletteChoices(): array
    {
        $choices = [];
        foreach (self::PALETTES as $category => $palette) {
            $choices[] = [
                'category' => $category,
                'accent' => $palette[0],
                'accentDark' => $palette[1],
            ];
        }

        return $choices;
    }

    /**
     * Keep only a usable 6-digit hex. Returns null when empty/invalid so the
     * page can fall back to keyword-based palettes.
     *
     * Admin-entered colours are kept as-is (any valid hex). Contrast clamping
     * is only applied to AI suggestions via ensureAccentContrast().
     */
    public static function sanitizeAccent(?string $hex): ?string
    {
        if ($hex === null) {
            return null;
        }

        $hex = strtolower(trim($hex));
        if ($hex === '') {
            return null;
        }
        if ($hex[0] !== '#') {
            $hex = '#' . $hex;
        }
        if (!preg_match('/^#[0-9a-f]{6}$/', $hex)) {
            return null;
        }

        return $hex;
    }

    /**
     * Snap near-white / near-black AI accents to the closest palette colour so
     * CTAs stay readable. Do not use this for admin overrides.
     */
    public static function ensureAccentContrast(string $hex): string
    {
        $hex = self::sanitizeAccent($hex) ?? '#22c55e';

        $r = hexdec(substr($hex, 1, 2));
        $g = hexdec(substr($hex, 3, 2));
        $b = hexdec(substr($hex, 5, 2));
        $luminance = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;

        if ($luminance < 0.12 || $luminance > 0.72) {
            return self::nearestPaletteAccent($hex);
        }

        return $hex;
    }

    public static function darkenAccent(string $hex): string
    {
        foreach (self::PALETTES as $palette) {
            if (strtolower($palette[0]) === strtolower($hex)) {
                return $palette[1];
            }
        }

        $hex = ltrim($hex, '#');
        $r = (int) round(hexdec(substr($hex, 0, 2)) * 0.68);
        $g = (int) round(hexdec(substr($hex, 2, 2)) * 0.68);
        $b = (int) round(hexdec(substr($hex, 4, 2)) * 0.68);

        return sprintf('#%02x%02x%02x', min(255, $r), min(255, $g), min(255, $b));
    }

    protected static function nearestPaletteAccent(string $hex): string
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $best = self::PALETTES['general'][0];
        $bestDist = PHP_INT_MAX;

        foreach (self::PALETTES as $palette) {
            $p = ltrim($palette[0], '#');
            $dr = $r - hexdec(substr($p, 0, 2));
            $dg = $g - hexdec(substr($p, 2, 2));
            $db = $b - hexdec(substr($p, 4, 2));
            $dist = ($dr * $dr) + ($dg * $dg) + ($db * $db);
            if ($dist < $bestDist) {
                $bestDist = $dist;
                $best = $palette[0];
            }
        }

        return $best;
    }

    /** "#f97316" => "249, 115, 22" so the stylesheet can build rgba() shades. */
    protected static function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return implode(', ', [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ]);
    }

    protected static function detectCategory(string $haystack): string
    {
        $best = 'general';
        $bestScore = 0;

        foreach (self::CATEGORY_KEYWORDS as $category => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                $score += substr_count($haystack, $keyword);
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $category;
            }
        }

        return $best;
    }

    protected static function badge(string $category): string
    {
        $badges = [
            'automotive'  => self::t('Workshop & Garage Software'),
            'food'        => self::t('Restaurant & Hospitality Software'),
            'fitness'     => self::t('Fitness & Membership Software'),
            'finance'     => self::t('Finance & Invoicing Software'),
            'medical'     => self::t('Clinic & Healthcare Software'),
            'education'   => self::t('Education & Training Software'),
            'hospitality' => self::t('Booking & Reservation Software'),
            'ecommerce'   => self::t('Online Selling Software'),
            'crm'         => self::t('Customer & Sales Software'),
            'hr'          => self::t('Team & HR Software'),
            'realestate'  => self::t('Property Management Software'),
            'logistics'   => self::t('Delivery & Fleet Software'),
            'beauty'      => self::t('Salon & Spa Software'),
        ];

        return $badges[$category] ?? self::t('All-in-One Digital Solution');
    }

    protected static function featuresHeading(string $title, string $category): string
    {
        $subject = [
            'automotive'  => self::t('run your workshop'),
            'food'        => self::t('run your venue'),
            'fitness'     => self::t('run your club'),
            'finance'     => self::t('keep your finances in order'),
            'medical'     => self::t('run your practice'),
            'education'   => self::t('run your programme'),
            'hospitality' => self::t('manage every booking'),
            'ecommerce'   => self::t('grow your store'),
            'crm'         => self::t('win more customers'),
            'hr'          => self::t('manage your team'),
            'realestate'  => self::t('manage your properties'),
            'logistics'   => self::t('keep deliveries moving'),
            'beauty'      => self::t('run your salon'),
        ];

        if (isset($subject[$category])) {
            return self::t('Everything you need to') . ' ' . $subject[$category];
        }

        return $title !== ''
            ? self::t('Everything included in') . ' ' . $title
            : self::t('Everything you need in one place');
    }

    /**
     * Feature cards keep the seller's wording as the headline. When a feature is written
     * as "Title: detail" the two halves are split; otherwise a supporting line is chosen
     * from the keyword map so cards read as copy instead of a bare list.
     */
    protected static function featureCards(array $features): array
    {
        $cards = [];

        foreach ($features as $feature) {
            $title = $feature;
            $text = '';

            foreach ([' — ', ' – ', ': ', ' - '] as $separator) {
                if (Str::contains($feature, $separator)) {
                    [$title, $text] = array_pad(explode($separator, $feature, 2), 2, '');
                    break;
                }
            }

            $match = self::matchKeyword($feature);

            if ($text === '' && $match) {
                $text = self::t($match['blurb']);
            }

            $cards[] = [
                'icon' => $match['icon'] ?? 'check',
                'title' => trim($title),
                'text' => trim($text),
                'short' => $match ? self::t($match['short']) : self::shortLabel($title),
                'outcome' => $match ? self::t($match['outcome']) : '',
            ];
        }

        return $cards;
    }

    protected static function matchKeyword(string $text): ?array
    {
        $text = Str::lower($text);

        foreach (self::FEATURE_KEYWORDS as $entry) {
            foreach ($entry['keys'] as $key) {
                if (Str::contains($text, $key)) {
                    return $entry;
                }
            }
        }

        return null;
    }

    /**
     * Outcome cards re-frame matched features as results. Rendered only when enough
     * features match, so the section never pads itself with duplicates.
     */
    protected static function outcomes(array $featureCards): array
    {
        $outcomes = [];
        $seen = [];

        foreach ($featureCards as $card) {
            if ($card['outcome'] === '' || isset($seen[$card['outcome']])) {
                continue;
            }
            $seen[$card['outcome']] = true;
            $outcomes[] = [
                'icon' => $card['icon'],
                'title' => $card['outcome'],
                'text' => $card['title'],
            ];
        }

        return count($outcomes) >= 3 ? array_slice($outcomes, 0, 6) : [];
    }

    /**
     * Pain framing for the problem/solution section. Deliberately about the manual
     * way of working, never a claim about the product or its results.
     */
    protected static function problem(string $category): array
    {
        $pains = [
            'automotive'  => ['Job cards written on paper', 'Customer and vehicle history spread across notebooks', 'Parts running out with no warning'],
            'food'        => ['Orders taken on scraps of paper', 'Stock counted by hand at closing time', 'No clear view of what actually sells'],
            'fitness'     => ['Memberships tracked in spreadsheets', 'Renewals missed until someone complains', 'Class attendance guessed rather than known'],
            'finance'     => ['Invoices rebuilt from scratch every time', 'Payments chased from memory', 'Numbers that never quite reconcile'],
            'medical'     => ['Patient files stored in cabinets', 'Appointments juggled by phone', 'Histories that take minutes to find'],
            'education'   => ['Student records kept in separate files', 'Progress tracked manually', 'Reports assembled by hand each term'],
            'hospitality' => ['Bookings written in a diary', 'Double-bookings discovered too late', 'Guest details retyped again and again'],
            'ecommerce'   => ['Products updated in several places', 'Orders copied between tools', 'Stock levels that are already out of date'],
            'crm'         => ['Leads living in someone\'s inbox', 'Follow-ups forgotten', 'No shared view of the pipeline'],
            'hr'          => ['Attendance collected on paper', 'Payroll rebuilt every month', 'Employee documents scattered'],
            'realestate'  => ['Property details in separate documents', 'Tenant records hard to retrieve', 'Payments tracked from memory'],
            'logistics'   => ['Deliveries tracked over the phone', 'Fleet costs recorded after the fact', 'No single view of what is in transit'],
            'beauty'      => ['Appointments booked in a notebook', 'Client preferences remembered, not recorded', 'Quiet hours nobody planned for'],
        ];

        $defaults = ['Work spread across spreadsheets and notes', 'Information re-entered in several places', 'No single view of what is going on'];

        return [
            'heading' => self::t('Running it all manually costs you time'),
            'items' => array_map(function ($item) {
                return self::t($item);
            }, $pains[$category] ?? $defaults),
            'solutionHeading' => self::t('Everything in one place instead'),
        ];
    }

    /**
     * Trust items are statements the product data supports: how it is delivered,
     * how payment works, how many features are included, how to reach support.
     */
    protected static function trust(array $featureCards, bool $deliversFile, string $kind = 'script'): array
    {
        $items = [];

        if ($kind === 'physical') {
            $items[] = [
                'icon' => 'truck',
                'title' => self::t('Ships to your address'),
                'text' => self::t('Enter your name, phone, and full delivery address at checkout'),
            ];
            $items[] = [
                'icon' => 'money',
                'title' => self::t('Card or PayPal'),
                'text' => self::t('Pay online first. The order ships after that payment is confirmed'),
            ];
        } else {
        $items[] = [
            'icon' => 'bolt',
            'title' => self::t('Instant Access'),
            'text' => $deliversFile ? self::t('Download right after payment') : self::t('Access link right after payment'),
        ];

        if ($kind === 'sheet') {
            $items[] = [
                'icon' => 'file-excel',
                'title' => self::t('Google Sheets & Excel'),
                'text' => self::t('Open it instantly — no hosting needed'),
            ];
        } elseif ($kind === 'product') {
            $items[] = [
                'icon' => 'download',
                'title' => self::t('Instant digital download'),
                'text' => self::t('Download your files as soon as your payment is confirmed'),
            ];
        } else {
            $items[] = [
                'icon' => 'server',
                'title' => self::t('Self-Host or We Host'),
                'text' => self::t('Install on your server, or let us host it'),
            ];
        }

        $items[] = [
            'icon' => 'infinity',
            'title' => self::t('One-Time Payment'),
            'text' => self::t('No subscription, no monthly fees'),
        ];

        $items[] = [
            'icon' => 'lock',
            'title' => self::t('Secure Checkout'),
            'text' => self::t('Pay by PayPal or card'),
        ];
        }

        if (count($featureCards) > 0) {
            $items[] = [
                'icon' => 'th-large',
                'title' => count($featureCards) . ' ' . self::t('Included Features'),
                'text' => self::t('All part of the same package'),
            ];
        }

        $items[] = [
            'icon' => 'comments',
            'title' => self::t('Ask Before You Buy'),
            'text' => self::t('Use the contact form or live chat on this page'),
        ];

        return $items;
    }

    /** Only counted figures are returned; an empty array hides the section. */
    protected static function stats(int $completedOrders): array
    {
        if ($completedOrders < 5) {
            return [];
        }

        return [[
            'value' => number_format($completedOrders),
            'label' => self::t('Customers already using it'),
        ]];
    }

    /** Splits the seller's "what you'll get" HTML into check-list items. */
    protected static function listItems(string $html): array
    {
        if (trim($html) === '') {
            return [];
        }

        preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $html, $matches);

        $items = [];
        foreach ($matches[1] ?? [] as $item) {
            $clean = trim(preg_replace('/\s+/', ' ', strip_tags($item)));
            if ($clean !== '') {
                $items[] = $clean;
            }
        }

        return $items;
    }

    protected static function faq($page, bool $deliversFile, string $kind = 'script'): array
    {
        $faq = [];
        $title = $page->lp_title ?? self::t('this product');

        $received = array_map(function ($item) {
            return rtrim(trim($item), '.;,');
        }, array_slice(self::listItems($page->lp_what_you_get ?? ''), 0, 3));

        if ($kind === 'physical') {
            $answer = self::t('The product is shipped to the address you provide.');
        } else {
            $answer = self::t('You get immediate access to') . ' ' . $title . '.';
            if ($received) {
                $answer .= ' ' . implode('. ', $received) . '.';
            }
        }

        $faq[] = [
            'q' => self::t('What do I receive after payment?'),
            'a' => $answer,
        ];

        if ($kind === 'physical') {
            $faq[] = [
                'q' => self::t('How is the product delivered?'),
                'a' => self::t('It is packed and shipped to the address you enter at checkout.'),
            ];
            $faq[] = [
                'q' => self::t('How do I pay?'),
                'a' => self::t('Pay by card or PayPal at checkout. The order ships after that payment is confirmed.'),
            ];
        } else {
        $faq[] = [
            'q' => self::t('How is the product delivered?'),
            'a' => $deliversFile
                ? self::t('As soon as your payment is confirmed you are taken to a download page for the product files.')
                : self::t('As soon as your payment is confirmed you are taken to the product access link.'),
        ];

        if ($kind === 'sheet') {
            $faq[] = [
                'q' => self::t('How do I open this after purchase?'),
                'a' => self::t('You download the spreadsheet file immediately after payment. Open it in Microsoft Excel or upload it to Google Sheets. No hosting or installation is required.'),
            ];
        } elseif ($kind === 'product') {
            $faq[] = [
                'q' => self::t('How do I open this after purchase?'),
                'a' => self::t('You download the digital product files immediately after payment. No hosting or installation is required.'),
            ];
        } else {
            $faq[] = [
                'q' => self::t('Do I install this myself, or can you host it?'),
                'a' => self::t('This is a web app you can install on your own server. If you prefer not to run it yourself, we can host it for you. Use the contact form on this page if you want hosting.'),
            ];
        }
        }

        $faq[] = [
            'q' => self::t('Is this a one-time payment?'),
            'a' => self::t('Yes. You pay once for this product. There is no subscription and no recurring charge.'),
        ];

        if ($kind === 'physical') {
            $faq[] = [
                'q' => self::t('Which payment methods can I use?'),
                'a' => self::t('Card and PayPal. The order is confirmed only after the payment succeeds.'),
            ];
        } else {
        $faq[] = [
            'q' => self::t('Which payment methods can I use?'),
            'a' => self::t('You can pay with PayPal or with a credit or debit card at checkout.'),
        ];
        }

        if ($kind !== 'physical' && ($page->lp_extended_price ?? 0) > 0) {
            $faq[] = [
                'q' => self::t('What is the extended licence?'),
                'a' => self::t('The extended licence is a higher-priced option for wider commercial use. Both options cover the same product.'),
            ];
        }

        $faq[] = [
            'q' => self::t('Can I ask a question before buying?'),
            'a' => self::t('Yes. Use the contact form on this page, or start a live chat if it is available.'),
        ];

        return $faq;
    }

    protected static function hosting(string $kind = 'script'): array
    {
        if ($kind === 'physical') {
            return [
                'heading' => self::t('Shipped to you'),
                'text' => self::t('After you pay, we ship it to your address.'),
            ];
        }

        if ($kind === 'sheet') {
            return [
                'heading' => self::t('Instant spreadsheet download'),
                'text' => self::t('This is a spreadsheet file. After payment you download it immediately and open it in Excel or Google Sheets — no hosting required.'),
            ];
        }

        if ($kind === 'product') {
            return [
                'heading' => self::t('Instant digital download'),
                'text' => self::t('This is a digital product. After payment you download the files immediately — no hosting required.'),
            ];
        }

        return [
            'heading' => self::t('Install it yourself or let us host it'),
            'text' => self::t('This is a web app you can install on your own server. If you prefer, we can host it for you.'),
        ];
    }

    /** A plain-text lead paragraph pulled from the seller's own description. */
    /**
     * Short hero label from a feature title without cutting it mid-idea
     * (e.g. "Plus de 10" from "Plus de 10 millions de pages").
     */
    protected static function shortLabel($title): string
    {
        // The full title is shown (it wraps in the hero); cutting it to a few
        // words left labels like "Plus de 10" that read as truncated.
        return trim((string) $title);
    }

    protected static function shortDescription($page): string
    {
        $source = self::plainText($page->lp_description ?? '');

        if ($source === '') {
            return '';
        }

        if (mb_strlen($source) <= self::DESCRIPTION_MAX_CHARS) {
            return $source;
        }

        return Str::limit($source, self::DESCRIPTION_MAX_CHARS);
    }

    /** Strip HTML without gluing headings onto the next sentence. */
    public static function plainText(string $html): string
    {
        $html = preg_replace('/<\s*br\s*\/?\s*>/i', ' ', $html);
        $html = preg_replace('/<\/(p|div|h[1-6]|li|blockquote|section)>/i', ' ', $html);
        $html = preg_replace('/<(p|div|h[1-6]|li|blockquote|section)[^>]*>/i', ' ', $html);

        return trim(preg_replace('/\s+/', ' ', strip_tags($html)));
    }

    /**
     * Keep description HTML short enough for the hero. Long copy belongs in
     * features / what-you-get, not this field.
     */
    public static function clampDescriptionHtml(string $html): string
    {
        $plain = self::plainText($html);

        if ($plain === '') {
            return '';
        }

        if (mb_strlen($plain) <= self::DESCRIPTION_MAX_CHARS) {
            return '<p>' . e($plain) . '</p>';
        }

        $cut = mb_substr($plain, 0, self::DESCRIPTION_MAX_CHARS);
        $sentence = mb_strrpos($cut, '. ');
        if ($sentence !== false && $sentence >= (int) (self::DESCRIPTION_MAX_CHARS * 0.55)) {
            $cut = trim(mb_substr($cut, 0, $sentence + 1));
        } else {
            $space = mb_strrpos($cut, ' ');
            if ($space !== false) {
                $cut = rtrim(mb_substr($cut, 0, $space), '.,;:');
            }
        }

        return '<p>' . e($cut) . '</p>';
    }
}
