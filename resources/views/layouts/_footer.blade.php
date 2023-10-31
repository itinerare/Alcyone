<nav class="navbar navbar-expand-md navbar-light">
    <ul class="navbar-nav ms-auto me-auto">
        <li class="nav-item"><a href="{{ url('info/terms') }}" class="nav-link">Terms</a></li>
        <li class="nav-item"><a href="{{ url('info/privacy') }}" class="nav-link">Privacy</a></li>
        <li class="nav-item"><a href="{{ url('reports/new') }}" class="nav-link">Report Content</a></li>
        <li class="nav-item"><a href="mailto:{{ env('CONTACT_ADDRESS') }}" class="nav-link">Contact</a></li>
        <li class="nav-item"><a href="https://github.com/itinerare/alcyone" class="nav-link">Alcyone
                v{{ config('alcyone.settings.version') }}</a></li>
    </ul>
</nav>
