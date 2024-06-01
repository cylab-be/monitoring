<p>
    Current load: <b>{{ $current_load }}</b> | 
    Max in the last 24h: <b>{{ $max_load }}</b>
</p>
<p>
    Warning threshold: <span class="text-warning font-weight-bold">{{ $warning_threshold }}</span> |
    Error threshold: <span class="text-danger font-weight-bold">{{ $error_threshold }}</span>
</p>

<canvas id="load-chart" width='400' height='200'></canvas>
<script>
    window.addEventListener('load', function() {
        window.monitorLoadChart(document.getElementById('load-chart'));
    });
</script>
