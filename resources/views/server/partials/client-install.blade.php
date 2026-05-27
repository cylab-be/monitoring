<div class="card">
    <div class="card-header">
        Agent installation
    </div>
    <div class="card-body">
        <pre><code>
# Install dependencies
sudo apt-get install wget net-tools sysstat lm-sensors php-cli php-json php-curl

# Download client application:
wget {{ url("/monitor") }}
chmod +x monitor

# Test the client
sudo ./monitor -i {{ $server->id }} -t {{ $server->token }} -s {{ config("app.url") }}

# Move client to /usr/bin
sudo mv monitor /usr/bin/monitor

# Add a cron entry to run it automatically
echo "*/5 * * * * root sleep {{ $server->id % 240 }}  && /usr/bin/monitor -i {{ $server->id }} -t {{ $server->token }} -s {{ config("app.url") }}" | \
sudo tee -a /etc/cron.d/monitor
        
{{ $server->customInstallationInstructions() }}
</pre></code>
    </div>
</div>