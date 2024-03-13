<div class="card">
    <div class="card-header">
        PHP Client installation
    </div>
    <div class="card-body">
        <p>Download client application:</p>
        <pre style="font-size: 75%; overflow: hidden"><code>wget {{ \App\Jobs\FetchClientManifest::url() }}
unzip monitor-*.zip
</code></pre>
        <p>Move it to /usr/bin:</p>
        <pre style="font-size: 75%; overflow: hidden"><code>sudo mv monitor /usr/bin/monitor
</code></pre>

        <p>Test it:</p>
        <pre style="font-size: 75%; overflow: hidden"><code>sudo monitor ping -i {{ $server->id }} -t {{ $server->token }} -s {{ config("app.url") }}
</code></pre>

        <p>Add a cron entry to run it automatically:</p>
        <pre style="font-size: 75%; overflow: hidden"><code>echo "*/5 * * * * root /usr/bin/monitor ping -i {{ $server->id }} -t {{ $server->token }} -s {{ config("app.url") }}" | \
sudo tee -a /etc/crontab
</code></pre>
    </div>
</div>