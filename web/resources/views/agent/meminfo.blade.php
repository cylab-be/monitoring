
<canvas id="memory-chart" width='400' height='300'></canvas>
<script src="/js/sensor.memory.js"></script>
<script>
    window.monitorURL = {{ url('/') }};
    window.monitorServerID = {{ $server->id }};
    window.monitorServerToken = "{{ $server->read_token }}";

    window.addEventListener('load', function() {
        window.monitorMemChart(document.getElementById('memory-chart'));
    });
</script>