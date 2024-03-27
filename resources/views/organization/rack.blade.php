@extends('layouts.app')

@section('content')
<style>
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
    
    div.1u {
        height: 2rem;
    }
    
    div.2u {
        height: 4rem;
    }
</style>

<div class="container">

    <h1>{{ $organization->name }}</h1>
    
    <div style="position: relative; width: 100%; height: 96rem">
        
        <div class="server 1u"
             style="bottom: 2rem;">
            Server 02
        </div>
        
        <div class="server 1u"
             style="bottom: 0rem;">
            Server 01
        </div>
    </div>

</div>
@endsection
