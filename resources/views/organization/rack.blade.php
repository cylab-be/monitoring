@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4 text-center">
    @foreach ($organization->racks as $rack)
    <div class="rack size-{{ $rack->height + 1 }}u">
        <div class="slot" style="top: 0">
            {{ $rack->name }}
            
            <div class="bottom-right">
                <a href="{{ action('RackController@edit', 
                            ['organization' => $rack->organization_id,
                             'rack' => $rack]) }}">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>
        
        @for ($i = 0; $i < $rack->height; $i++)
        <div class="slot text-left" style="bottom: {{ 2*$i }}rem">
            {{ $i + 1 }}
        </div>
        @endfor

        @foreach ($rack->servers as $server)
        @if ($server->size == 0)
            @continue
        @endif
        <div class="server text-left size-{{ $server->size }}u"
             style="bottom: {{ 2*($server->position - 1) }}rem;">
            <p>
                <a href="{{ $server->getUrlAttribute() }}"
                   class="text-decoration-none">
                    {{ $server->name }}
                </a>
                {!! $server->status()->badgeIfExists() !!}
            </p>
        </div>
        @endforeach
    </div>
    @endforeach
</div>

<div class="text-muted bottom-right">
    Reload in <span id="reload-countdown">300</span> seconds
</div>

<script type="text/javascript">
    var reload_countdown = 300;
    setInterval(function() {
        reload_countdown -= 1;
        $('#reload-countdown').text(reload_countdown);

        if (reload_countdown === 0) {
            console.log('reload...');
            location.reload();
        }
    }, 1000);
</script>
@endsection
