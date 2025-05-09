@extends('layouts.app')
@section('title', 'Subnets | ' . $organization->name )

@section('content')
<div class="container">
    <h1>Subnets</h1>
    
    <div class="container-fluid">
        <div id="cy" style="width: 100%; height: 800px">
    </div>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.32.0/cytoscape.min.js" 
    integrity="sha512-JUacxc3LBNCUyDO2C+80nYCLPIbfPpyuD7rCpVqEz6n2PCk1LSdHNldgBzaVELc6ft+jwomNa9L2W8Wo9Dt1pA==" 
    crossorigin="anonymous" 
    referrerpolicy="no-referrer">
    </script>
    
    <script>
        let cy = cytoscape({
            container: document.getElementById('cy'),
            elements: @json($organization->toCytoscape()),
            style: [
                {
                  selector: 'node',
                  style: {
                    "background-color": '#777',
                    "text-valign": "center",
                    "text-halign": "center",
                    "label": "data(label)"
                  }
                },
                {
                    selector: 'node[type="subnet"]',
                    style: {
                        'shape': 'square',
                        'background-color': '#007bff'
                    }
                }
            ],
            layout: {
                name: 'breadthfirst'
            }
        });
        
        cy.on('tap', 'node', function(evt){
            let node = evt.target;
            console.log( 'tapped ' + node.id() );
            window.location = node.data("url");
        });
    </script>
</div>
@endsection