<?php

namespace Fickrr\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Fickrr\Models\Settings;

class OpenAIService
{
    protected string $apiKey;
    protected string $model;
    protected string $imageModel;
    protected bool $generateImages;
    protected string $baseUrl = 'https://api.openai.com/v1';

    public function __construct(?string $apiKey = null)
    {
        $ai = Settings::editAiSettings();

        $this->apiKey = $apiKey
            ?: ($ai->openai_api_key ?? config('services.openai.api_key') ?? '');
        $this->model = $ai->openai_model ?? config('services.openai.model', 'gpt-4o-mini');
        $this->imageModel = $ai->openai_image_model ?? config('services.openai.image_model', 'gpt-image-1');
        $this->generateImages = (int) ($ai->openai_generate_images ?? 1) === 1;
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function isEnabled(): bool
    {
        $ai = Settings::editAiSettings();
        return (int) ($ai->openai_enabled ?? 0) === 1 && $this->isConfigured();
    }

    /**
     * Languages the landing-page generator can write copy in.
     *
     * @return array<string,string> code => English name
     */
    public static function contentLanguages(): array
    {
        return [
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'pt-BR' => 'Portuguese (Brazil)',
            'nl' => 'Dutch',
            'pl' => 'Polish',
            'ro' => 'Romanian',
            'cs' => 'Czech',
            'hu' => 'Hungarian',
            'sv' => 'Swedish',
            'da' => 'Danish',
            'nb' => 'Norwegian',
            'fi' => 'Finnish',
            'el' => 'Greek',
            'tr' => 'Turkish',
            'ru' => 'Russian',
            'uk' => 'Ukrainian',
            'ar' => 'Arabic',
            'he' => 'Hebrew',
            'hi' => 'Hindi',
            'bn' => 'Bengali',
            'id' => 'Indonesian',
            'ms' => 'Malay',
            'th' => 'Thai',
            'vi' => 'Vietnamese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'zh' => 'Chinese (Simplified)',
            'zh-TW' => 'Chinese (Traditional)',
        ];
    }

    /**
     * Generate high-converting landing page copy + optional hero image from title & About This Product.
     *
     * @param string $description Plain-text product details from the About This Product field (not the short hero text).
     * @return array{success:bool,data?:array,error?:string}
     */
    public function generateLandingPage(string $title, string $description, bool $withImage = true, string $language = 'en', string $productKind = 'script'): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'OpenAI API key is not configured. Add it under AI API Connection.'];
        }

        try {
            $content = $this->generateCopy($title, $description, $language, $productKind);

            $bannerFilename = null;
            if ($withImage && $this->generateImages && !empty($content['image_prompt'])) {
                $bannerFilename = $this->generateAndSaveImage($content['image_prompt'], $title);
            }

            if ($bannerFilename) {
                $content['banner_image'] = $bannerFilename;
            }

            return ['success' => true, 'data' => $content];
        } catch (\Throwable $e) {
            Log::error('OpenAI landing generation failed', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Test the API key with a lightweight models list call.
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'API key is empty.'];
        }

        try {
            $client = new Client(['timeout' => 20]);
            $response = $client->get($this->baseUrl . '/models', [
                'headers' => $this->headers(),
            ]);
            $body = json_decode($response->getBody()->getContents(), true);
            $count = isset($body['data']) ? count($body['data']) : 0;

            return [
                'success' => true,
                'message' => 'Connected successfully. ' . $count . ' models available.',
            ];
        } catch (GuzzleException $e) {
            $msg = $this->extractErrorMessage($e);
            return ['success' => false, 'error' => $msg];
        }
    }

    protected function generateCopy(string $title, string $description, string $language = 'en', string $productKind = 'script'): array
    {
        $paletteLines = [];
        foreach (\Fickrr\Helpers\LandingPageContent::paletteChoices() as $choice) {
            $paletteLines[] = $choice['category'] . ': ' . $choice['accent'];
        }
        $paletteList = implode("\n", $paletteLines);

        $languages = self::contentLanguages();
        $langCode = array_key_exists($language, $languages) ? $language : 'en';
        $langName = $languages[$langCode];

        $kind = in_array($productKind, ['sheet', 'product'], true) ? $productKind : 'script';
        $productRules = $kind === 'sheet'
            ? 'PRODUCT TYPE: spreadsheet / Google Sheets / Excel file. Buyers download the file instantly after payment and open it in Excel or Google Sheets. Do NOT mention hosting, servers, installation, or self-host. Emphasize instant download and that no hosting is required. CTA examples: Get Instant Download.'
            : ($kind === 'product'
                ? 'PRODUCT TYPE: normal digital product (PDF, templates, graphics, ebooks, assets, etc.). Buyers download the files instantly after payment. Do NOT mention hosting, servers, or installation. Emphasize instant download. CTA examples: Get Instant Download.'
                : 'PRODUCT TYPE: self-hosted web app / script. The buyer can install the software on their own server, or we can host it for them. Mention that clearly in about_html, at least one feature, and inside what_you_get_html. Do not describe it as a SaaS subscription they log into on our site.');

        $system = <<<PROMPT
You are an expert conversion copywriter and landing page strategist for digital product marketplaces.
You receive a product title and the seller's "About This Product" write-up. That About text is the source of truth for what the product is, who it is for, and what it includes. Ground every claim in those details. You may polish, structure, and expand for conversion, but do not invent unrelated features, audiences, or capabilities that contradict or ignore the provided About content.

LANGUAGE (mandatory): Write EVERY visitor-facing string in {$langName} ({$langCode}). This includes title, tagline, cta_text, description_html, about_html, what_you_get_html, every item in features, meta_title, meta_description, meta_keywords, review highlights, review texts, and seller responses. JSON keys must stay in English. HTML tags stay as HTML. Do not mix in English except for brand names that must stay as-is. If the seller supplied the title in another language, translate and polish it into a natural {$langName} page title (keep recognizable brand/product proper names). Translate CTA examples such as "Get Instant Access" into {$langName}.

Also choose ONE accent colour that fits this specific product (industry, audience, mood). Do not default to orange unless the product is actually automotive, garage, or vehicle-related.
Prefer one of these proven CTA accents:
{$paletteList}
You may pick a different hex if it fits the product better. It must be saturated enough for a CTA button on both a dark hero and a white page (not white, grey, black, or a pale pastel).

The url_slug field is ALWAYS English (even when all other copy is in {$langName}). It is the URL path segment after /landing/ — 2 to 4 short lowercase words joined by hyphens, no numbers-only slugs, no special characters. Examples: kids-coloring-pages, seo-report-tool, invoice-template-pack.

The title field is the landing page H1 / page title in {$langName}, under 80 characters, clear and conversion-friendly.

The description_html field is the short hero text under the title — one or two sentences, 200 characters maximum, distilled from the About This Product details. Put all longer selling copy in about_html instead (rewrite/polish the About content into persuasive, scannable HTML — do not ignore its facts).

{$productRules}

Also invent a realistic reviews block for social proof (8 to 12 reviews). Mix 5-star and a few 4-star reviews. Use first names only. Dates should be recent months in 2025–2026. Seller responses should be short and warm. Do not claim specific unverifiable results.

Do not generate image prompts. Copy only.

Return ONLY valid JSON (no markdown) with this exact shape:
{
  "title": "Page title in the output language, under 80 chars",
  "tagline": "One compelling sentence under 120 chars",
  "cta_text": "Short CTA button text",
  "accent_color": "#22c55e",
  "description_html": "ONE short <p> only. Maximum 200 characters of visible text. Hero text under the title — must fit without truncation. No headings, no lists.",
  "about_html": "Rich HTML for the About this product section: 2-4 short sections using <h3>, <p>, <ul><li>. Persuasive, scannable, benefit-focused. No scripts.",
  "what_you_get_html": "HTML explaining post-purchase delivery using <ul><li> items.",
  "features": ["6 to 8 short benefit-focused feature strings"],
  "meta_title": "SEO title under 60 chars",
  "meta_description": "SEO description under 155 chars",
  "meta_keywords": "comma,separated,keywords",
  "url_slug": "english-url-slug",
  "suggested_price": 29.00,
  "reviews": {
    "rating": 4.8,
    "count": 86,
    "quality": 4.9,
    "shipping": 5.0,
    "service": 4.8,
    "recommend": 94,
    "highlights": ["Easy to use", "Great quality", "As described"],
    "items": [
      {
        "name": "Nichole",
        "stars": 5,
        "date": "May 26, 2026",
        "theme": "Ease of use",
        "text": "Review body",
        "response": "Thank you so much!"
      }
    ]
  }
}
PROMPT;

        $user = "Output language: {$langName} ({$langCode})\nProduct type: {$kind}\n\nProduct title (may be in another language — return title in {$langName}): {$title}\n\nAbout This Product (source of truth — use these details):\n{$description}\n\nCreate a landing page that will convert visitors into leads/buyers. Base all product facts on the About This Product text above. All visitor-facing copy including the page title must be in {$langName}.";

        $client = new Client(['timeout' => 90]);
        $response = $client->post($this->baseUrl . '/chat/completions', [
            'headers' => $this->headers(),
            'json' => [
                'model' => $this->model,
                'temperature' => 0.75,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user],
                ],
            ],
        ]);

        $body = json_decode($response->getBody()->getContents(), true);
        $raw = $body['choices'][0]['message']['content'] ?? '';
        $parsed = json_decode($raw, true);

        if (!is_array($parsed)) {
            throw new \RuntimeException('OpenAI returned invalid JSON for landing page copy.');
        }

        $features = $parsed['features'] ?? [];
        if (!is_array($features)) {
            $features = [];
        }
        $features = array_values(array_filter(array_map('strval', $features)));

        $accent = \Fickrr\Helpers\LandingPageContent::sanitizeAccent((string) ($parsed['accent_color'] ?? ''));
        if (!$accent) {
            $accent = '#22c55e';
        } else {
            $accent = \Fickrr\Helpers\LandingPageContent::ensureAccentContrast($accent);
        }

        return [
            'title' => mb_substr(trim((string) ($parsed['title'] ?? $title)), 0, 255) ?: $title,
            'tagline' => (string) ($parsed['tagline'] ?? ''),
            'cta_text' => (string) ($parsed['cta_text'] ?? 'Buy Now'),
            'accent_color' => $accent,
            'description_html' => \Fickrr\Helpers\LandingPageContent::clampDescriptionHtml((string) ($parsed['description_html'] ?? '')),
            'about_html' => (string) ($parsed['about_html'] ?? ''),
            'what_you_get_html' => (string) ($parsed['what_you_get_html'] ?? ''),
            'features' => $features,
            'meta_title' => (string) ($parsed['meta_title'] ?? $title),
            'meta_description' => (string) ($parsed['meta_description'] ?? ''),
            'meta_keywords' => (string) ($parsed['meta_keywords'] ?? ''),
            'url_slug' => self::normalizeUrlSlug((string) ($parsed['url_slug'] ?? '')),
            'suggested_price' => isset($parsed['suggested_price']) ? (float) $parsed['suggested_price'] : null,
            'reviews' => is_array($parsed['reviews'] ?? null) ? $parsed['reviews'] : null,
        ];
    }

    /**
     * Generate image via OpenAI Images API and save under public/storage/landing-pages.
     */
    public function generateAndSaveImage(string $prompt, string $titleHint = 'landing'): ?string
    {
        try {
            $client = new Client(['timeout' => 120]);
            $isDalle = stripos($this->imageModel, 'dall-e') !== false;

            // Image models render text poorly, so restate the constraint at request time.
            $guard = ' Absolutely no text, words, letters, numbers, logos, watermarks or UI mockups anywhere in the image.';
            // Prefer square + lower quality so shared-host proxies (~60s) do not time out.
            $payload = [
                'model' => $this->imageModel,
                'prompt' => mb_substr($prompt, 0, 3200) . $guard,
                'n' => 1,
                'size' => '1024x1024',
            ];
            if (!$isDalle) {
                $payload['quality'] = 'low';
            }

            $body = null;
            try {
                $response = $client->post($this->baseUrl . '/images/generations', [
                    'headers' => $this->headers(),
                    'json' => $payload,
                ]);
                $body = json_decode($response->getBody()->getContents(), true);
            } catch (GuzzleException $e) {
                // Some accounts/models reject quality/size options; retry with a minimal payload.
                $message = $this->extractErrorMessage($e);
                Log::warning('OpenAI image retry with minimal payload', ['reason' => $message]);
                $payload = [
                    'model' => $this->imageModel,
                    'prompt' => mb_substr($prompt, 0, 3200) . $guard,
                    'n' => 1,
                    'size' => '1024x1024',
                ];
                $response = $client->post($this->baseUrl . '/images/generations', [
                    'headers' => $this->headers(),
                    'json' => $payload,
                ]);
                $body = json_decode($response->getBody()->getContents(), true);
            }

            $item = $body['data'][0] ?? null;
            if (!$item) {
                return null;
            }

            $binary = null;
            if (!empty($item['b64_json'])) {
                $binary = base64_decode($item['b64_json']);
            } elseif (!empty($item['url'])) {
                $imgRes = $client->get($item['url'], ['timeout' => 60]);
                $binary = $imgRes->getBody()->getContents();
            }

            if (!$binary) {
                return null;
            }

            $dir = public_path('storage/landing-pages');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower(substr($titleHint, 0, 40)));
            $filename = 'ai-' . $slug . '-' . time() . '.jpg';

            // Raw model output is multi-MB PNG; downscale to a web-friendly banner.
            try {
                \Image::make($binary)
                    ->resize(1600, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('jpg', 82)
                    ->save($dir . '/' . $filename);
            } catch (\Throwable $e) {
                Log::warning('OpenAI image optimisation failed, storing raw file', ['message' => $e->getMessage()]);
                $filename = 'ai-' . $slug . '-' . time() . '.png';
                file_put_contents($dir . '/' . $filename, $binary);
            }

            return $filename;
        } catch (\Throwable $e) {
            Log::error('OpenAI image generation failed', ['message' => $e->getMessage()]);
            return null;
        }
    }

    protected function normalizeUrlSlug(string $raw): string
    {
        $slug = Str::slug($raw, '-', 'en');
        $slug = strtolower(preg_replace('/[^a-z0-9-]/', '', $slug));
        $slug = preg_replace('/-+/', '-', trim($slug, '-'));

        if (strlen($slug) > 60) {
            $slug = rtrim(substr($slug, 0, 60), '-');
        }

        return $slug;
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    protected function extractErrorMessage(GuzzleException $e): string
    {
        if (method_exists($e, 'hasResponse') && $e->hasResponse()) {
            $body = (string) $e->getResponse()->getBody();
            $json = json_decode($body, true);
            if (!empty($json['error']['message'])) {
                return $json['error']['message'];
            }
            return $body ?: $e->getMessage();
        }
        return $e->getMessage();
    }
}
