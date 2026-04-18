<table class="table table-striped my-5">
    @foreach($servers as $server)
    <tr>
        <td>
            <a class="text-decoration-none h5"
               href="{{ route('servers.show', ['server' => $server]) }}">
                {{ $server->name }}
            </a><br>

            <div class="small text-muted">
                <i class="fas fa-microchip"></i> {{ $server->info->cpuName() }} | {{ $server->info->vCores() }} vCores
                <i class="fas fa-memory ml-3"></i> {{ $server->info->memoryTotalForHumans() }}
                <i class="fas fa-server"></i> {{ $server->info->manufacturer }} {{ $server->info->product }}<br>

                <i class="fas fa-heart"></i> {{ $server->info->kernel }}
                <i class="fas fa-terminal ml-3"></i> {{ $server->info->lsb }}<br>

                @if (count($server->info->addresses()))
                <i class="fas fa-network-wired"></i>
                @foreach ($server->info->addresses() as $address)
                {{ $address }}
                @endforeach
            </div>
            @endif
        </td>

        <td class="text-right">
            {!! $server->status()->badge() !!}
            {{ $server->lastSummary()->time()->diffForHumans() }}
        </td>
    </tr>
    @endforeach
</table>
