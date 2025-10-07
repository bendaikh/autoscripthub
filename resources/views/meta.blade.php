<meta name="description" content="{{ $allsettings->site_desc }}" />
<meta name="keywords" content="{{ $allsettings->site_keywords }}" />
<meta name="robots" content="index,follow" />
<meta property="og:title" content="{{ $allsettings->site_title }}" />
<meta property="og:type" content="og:product" /> 
<meta property="og:description" content="{{ $allsettings->site_desc }}" /> 
<meta property="og:url" content="{{ url()->current() }}" /> 
<meta property="og:image" content="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}" />
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="{{ $allsettings->site_title }}" />
<meta name="twitter:title" content="{{ $allsettings->site_title }}" />
<meta name="twitter:description" content="{{ $allsettings->site_desc }}" />
<meta name="twitter:image" content="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}" />
<link rel="canonical" href="{{ url()->current() }}" />
<meta name="twitter:image:src" content="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}">