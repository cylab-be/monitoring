@extends('layouts.app')

@section('content')
<div class="container-fluid pb-4">
    
    <div class="rack-container">
        <div class="rack size-42u">
            @for ($i = 0; $i < 42; $i++)
            <div class="slot" style="bottom: {{ 2*$i }}rem">
                {{ $i + 1 }}
            </div>
            @endfor

            @foreach ($organization->servers as $server)
            @if ($server->size == 0)
                @continue
            @endif
            <div class="server size-{{ $server->size }}u"
                 style="bottom: {{ 2*($server->position - 1) }}rem;">
                <p>
                    <a href="{{ $server->getUrlAttribute() }}"
                       class="text-decoration-none">
                        {{ $server->name }}
                    </a>
                    {!! $server->status()->badge() !!}
                </p>
            </div>
            @endforeach
        </div>
        
        <div class="rack size-12u">
            @for ($i = 0; $i < 12; $i++)
            <div class="slot" style="bottom: {{ 2*$i }}rem">
                {{ $i + 1 }}
            </div>
            @endfor
        </div>
    </div>

</div>
@endsection
