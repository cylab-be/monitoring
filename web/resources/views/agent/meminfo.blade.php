
<canvas id="memory-chart" width='400' height='300'></canvas>
<script src="/js/sensor.memory.js"></script>
<script>
    window.addEventListener('load', function() {
        window.monitorMemChart(document.getElementById('memory-chart'));
    });
</script>