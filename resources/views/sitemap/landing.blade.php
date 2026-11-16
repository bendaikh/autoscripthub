<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach ($pages as $page)
        <url>
            <loc>{{ \Fickrr\Support\SiteUrl::to('/landing/'.$page->lp_slug) }}</loc>
            <lastmod>{{ \Fickrr\Support\SiteUrl::lastmod($page->updated_at ?? $page->created_at ?? null) }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
            @if(!empty($page->lp_banner_image))
            <image:image>
                <image:loc>{{ \Fickrr\Support\SiteUrl::to('/public/storage/landing-pages/'.$page->lp_banner_image) }}</image:loc>
            </image:image>
            @endif
        </url>
    @endforeach
</urlset>
