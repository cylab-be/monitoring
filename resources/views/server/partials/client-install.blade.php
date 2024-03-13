<div class="card">
    <div class="card-header">
        PHP Client installation
    </div>
    <div class="card-body">
        <p>Download client application:</p>
        <pre><code>wget {{ \App\Jobs\FetchClientManifest::url() }}
unzip monitor-*.zip
</code></pre>
        <p>Move it to /usr/bin:</p>
        <pre><code>sudo mv monitor /usr/bin/monitor
</code></pre>

        <p>Test it:</p>
        <pre><code>sudo monitor ping -i {{ $server->id }} -t {{ $server->token }} -s {{ config("app.url") }}
</code></pre>

        <p>Add a cron entry to run it automatically:</p>
        <pre><code>echo "*/5 * * * * root /usr/bin/monitor sleep {{ $server->id % 240 }}  && ping -i {{ $server->id }} -t {{ $server->token }} -s {{ config("app.url") }}" | \
sudo tee -a /etc/crontab
</code></pre>
    </div>
</div>