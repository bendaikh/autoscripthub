<?php

namespace Fickrr\Helpers;

/**
 * Per-page UI translations for public landing pages.
 * Uses language files in resources/lang/landing/{code}.json with English fallback.
 */
class LandingPageI18n
{
    protected static string $locale = 'en';

    /** @var array<string, array<string, string>> */
    protected static array $cache = [];

    /**
     * Sample of seller-written page copy used for language heuristics.
     */
    protected static function pageCopySample($page): string
    {
        return trim(
            ($page->lp_title ?? '') . ' ' .
            ($page->lp_tagline ?? '') . ' ' .
            ($page->lp_cta_text ?? '') . ' ' .
            strip_tags((string) ($page->lp_description ?? '')) . ' ' .
            strip_tags((string) ($page->lp_about_description ?? ''))
        );
    }

    /**
     * Detect Thai script in page copy when language was not saved (legacy pages).
     */
    public static function pageLooksThai($page): bool
    {
        $sample = self::pageCopySample($page);

        return $sample !== '' && (bool) preg_match('/[\x{0E00}-\x{0E7F}]/u', $sample);
    }

    /**
     * Detect French copy when language was left as English (legacy / mis-saved pages).
     */
    public static function pageLooksFrench($page): bool
    {
        $sample = self::pageCopySample($page);
        if ($sample === '') {
            return false;
        }

        $hasFrenchChars = (bool) preg_match('/[àâäçéèêëîïôöùûüÿœæÀÂÄÇÉÈÊËÎÏÔÖÙÛÜŸŒÆ]/u', $sample);
        $hasFrenchWords = (bool) preg_match(
            '/\b(le|la|les|des|une|du|au|aux|pour|avec|votre|vos|vous|dans|sur|plus|être|sont|cette|ceci|aussi|très|merci|téléchargement|instantané|modèles|pages)\b/ui',
            $sample
        );

        return $hasFrenchChars && $hasFrenchWords;
    }

    /**
     * Infer locale from page copy when the saved language is still English.
     */
    public static function inferLocaleFromCopy($page): ?string
    {
        if (self::pageLooksThai($page)) {
            return 'th';
        }
        if (self::pageLooksFrench($page)) {
            return 'fr';
        }

        return null;
    }

    public static function usePage($page): void
    {
        $lang = $page->lp_content_language ?? 'en';
        if (self::normalizeLocale($lang) === 'en') {
            $inferred = self::inferLocaleFromCopy($page);
            if ($inferred !== null) {
                $lang = $inferred;
            }
        }
        self::$locale = self::normalizeLocale($lang);
    }

    public static function setLocale(string $locale): void
    {
        self::$locale = self::normalizeLocale($locale);
    }

    public static function locale(): string
    {
        return self::$locale;
    }

    public static function normalizeLocale(?string $locale): string
    {
        $locale = strtolower(trim((string) $locale));
        if ($locale === '') {
            return 'en';
        }

        $languages = \Fickrr\Services\OpenAIService::contentLanguages();
        if (array_key_exists($locale, $languages)) {
            return $locale;
        }

        $base = explode('-', $locale)[0];
        if (array_key_exists($base, $languages)) {
            return $base;
        }

        return 'en';
    }

    public static function t(string $key, array $replace = []): string
    {
        $translations = self::load(self::$locale);
        $text = $translations[$key] ?? self::load('en')[$key] ?? $key;

        foreach ($replace as $search => $value) {
            $text = str_replace(':' . $search, (string) $value, $text);
        }

        return $text;
    }

    /** @return array<string, string> */
    public static function allForLocale(?string $locale = null): array
    {
        $locale = self::normalizeLocale($locale ?? self::$locale);
        $merged = array_merge(self::load('en'), self::load($locale));

        return $merged;
    }

    /** @return array<string, string> */
    protected static function load(string $locale): array
    {
        if (isset(self::$cache[$locale])) {
            return self::$cache[$locale];
        }

        $path = resource_path('lang/landing/' . $locale . '.json');
        if (!is_file($path)) {
            self::$cache[$locale] = [];

            return self::$cache[$locale];
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        self::$cache[$locale] = is_array($decoded) ? $decoded : [];

        return self::$cache[$locale];
    }
}
