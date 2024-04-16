@extends('layouts.app')

@section('content')
<div class="container-fluid pb-4 text-center">
    @foreach ($organization->racks as $rack)
    <div class="rack size-{{ $rack->height + 1 }}u">
        <div class="slot" style="top: 0">
            {{ $rack->name }}
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
@endsection
