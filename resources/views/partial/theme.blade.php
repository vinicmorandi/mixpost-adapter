<style>
    :root {
        @foreach($primary_colors as $key => $value)
         --color-primary-{{ $key }}: {{ $value }};
        @endforeach

        --color-primary-context: {{ $primary_context }};
        --color-alert: {{ $alert }};
        --color-alert-context: {{ $alert_context }};
    }

    .focus\:ring-primary-200:focus {
        --tw-ring-color: {{ $primary_ring_focus }} !important;
    }

    .ring-primary-200 {
        --tw-ring-color: {{ $primary_ring_focus }} !important;
    }
</style>
