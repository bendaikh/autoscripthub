<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($blog as $post)
        <url>
            <loc>{{ \Fickrr\Support\SiteUrl::to('/single/'.$post->post_slug) }}</loc>
            <lastmod>{{ date('Y-m-d') }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
        </url>
    @endforeach
</urlset>
