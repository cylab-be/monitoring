<div class="card">
    <div class="card-header">
        Agent installation
    </div>
    <div class="card-body">
        <p>Install dependencies</p>
        <pre><code>sudo apt-get install wget net-tools sysstat lm-sensors php-cli php-json php-curl
</code></pre>

        <p>Download client application:</p>
        <pre><code>wget {{ url("/monitor") }}
chmod +x monitor
</code></pre>

        <p>Test it:</p>
        <pre><code>sudo ./monitor -i {{ $server->id }} -t {{ $server->token }} -s {{ config("app.url") }}
</code></pre>

        <p>Move it to /usr/bin:</p>
        <pre><code>sudo mv monitor /usr/bin/monitor
</code></pre>



        <p>Add a cron entry to run it automatically:</p>
        <pre><code>echo "*/5 * * * * root sleep {{ $server->id % 240 }}  && /usr/bin/monitor -i {{ $server->id }} -t {{ $server->token }} -s {{ config("app.url") }}" | \
sudo tee -a /etc/crontab
</code></pre>
    </div>
</div>