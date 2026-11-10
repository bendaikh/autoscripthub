<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ \Fickrr\Support\SiteUrl::to('/') }}</loc>
        <lastmod>{{ date('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    @foreach ($items as $item)
        <url>
            <loc>{{ \Fickrr\Support\SiteUrl::to('/item/'.$item->item_slug) }}</loc>
            <lastmod>{{ \Fickrr\Support\SiteUrl::lastmod($item->updated_item ?? $item->created_item ?? null) }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>
