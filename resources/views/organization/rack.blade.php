@extends('layouts.app')

@section('content')
<style>
    div.rack {
        position: relative; 
        width: 100%; 
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    div.server {
        position: absolute;
        width: 100%;
        
        padding: 0.2rem;
        
        display: flex;
        flex-direction: column;
        word-wrap: break-word;
        background-color: white;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    div.size-1u {
        height: 2rem;
    }
    
    div.size-2u {
        height: 4rem;
    }
    
    div.size-3u {
        height: 6rem;
    }
    
    div.size-4u {
        height: 8rem;
    }
    
    div.size-48u {
        height: 96rem
    }
    
    div.slot {
        position: absolute;
        width: 100%;
        height: 2rem;
        
        padding: 0.2rem;
        
        display: flex;
        flex-direction: column;
        word-wrap: break-word;
        background-color: rgba(0, 0, 0, 0.01);
        border: 1px solid rgba(0, 0, 0, 0.05);
        
        color: rgba(0, 0, 0, 0.2);
    }
</style>

<div class="container">
    <div class="rack size-48u">
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

</div>
@endsection
