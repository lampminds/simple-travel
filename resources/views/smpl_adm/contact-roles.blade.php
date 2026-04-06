{{-- Legacy view: contact_roles was renamed to contact_positions. Redirect to the new resource. --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="0;url={{ url('/smpl_adm/contact-positions') }}">
    <title>Redirecting…</title>
</head>
<body>
    <p>Redirecting to <a href="{{ url('/smpl_adm/contact-positions') }}">Contact positions</a>…</p>
    <script>window.location.replace("{{ url('/smpl_adm/contact-positions') }}");</script>
</body>
</html>
