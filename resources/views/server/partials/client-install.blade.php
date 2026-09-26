<div class="card">
    <div class="card-header">
        Agent installation
    </div>
    <div class="card-body">
        <pre><code>
# Install dependencies
sudo apt-get install net-tools sysstat lm-sensors php-cli php-curl

# Test the client
curl -sL {{ config("app.url") }}/monitor | env ID="{{ $server->id }}" TOKEN="{{ $server->token }}" SERVER="{{ config("app.url") }}" php

# Add a cron entry to run it automatically
echo '*/5 * * * * root sleep {{ $server->id % 240 }}  && curl -sL {{ config("app.url") }}/monitor | env ID="{{ $server->id }}" TOKEN="{{ $server->token }}" SERVER="{{ config("app.url") }}" /usr/bin/php' | \
sudo tee -a /etc/cron.d/monitor
        
{{ $server->customInstallationInstructions() }}
</pre></code>
    </div>
</div>