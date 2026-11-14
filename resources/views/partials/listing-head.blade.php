{{-- Shared look for product listing pages (shop, popular, featured, new, free, flash sale, tags).
     Reuses the home page palette (home-vibrant.css) plus listing.css. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
@php
  $lsVibrant = base_path('public/assets/home-vibrant.css');
  $lsListing = base_path('public/assets/listing.css');
@endphp
<link rel="stylesheet" href="{{ asset('assets/home-vibrant.css') }}?v={{ is_file($lsVibrant) ? filemtime($lsVibrant) : 1 }}">
<link rel="stylesheet" href="{{ asset('assets/listing.css') }}?v={{ is_file($lsListing) ? filemtime($lsListing) : 1 }}">
