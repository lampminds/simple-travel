<!-- meta -->
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- title -->
<title>{{ is_test_site() ? __('nav.test_site_badge').' · ' : '' }}{{ $title ?? 'Prompt' }}</title>
<meta content="Coderthemes" name="author" />
<meta content="A fully featured multi purpose template" name="description" />

<!-- theme favicon -->
<link rel="shortcut icon" href="/images/favicon.ico">

{{-- Mapbox public token for maps (optional). Set MAPBOX_PUBLIC_TOKEN in .env --}}
<meta name="mapbox-public-token" content="{{ config('services.mapbox.public_token', '') }}">
