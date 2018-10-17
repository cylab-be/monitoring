
<p>Current load: {{ $current_load }}</p>

<canvas id="load-chart" width='400' height='300'></canvas>
<script src="/js/sensor.load.js"></script>
<script>
    window.monitorURL = "{{ url('/') }}";
    window.monitorServerID = {{ $server->id }};
    window.monitorServerToken = "{{ $server->read_token }}";

    window.addEventListener('load', function() {
        window.monitorLoadChart(document.getElementById('load-chart'));
    });
</script>
