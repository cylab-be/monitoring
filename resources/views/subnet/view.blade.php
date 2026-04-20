@extends('layouts.app')
@section('title', 'Subnets | ' . $organization->name )

@section('content')
<div style='position: relative;' class='h-100'>
    <h1>{{ $organization->name }}</h1>

    <div>
        <button class="btn btn-outline-primary active btn-sm"
                id="show-orphan">
            Orphan devices
        </button>
        
        @foreach ($organization->subnets as $subnet)
        <button class="btn btn-outline-primary active btn-sm"
                id="btn-subnet-{{ $subnet->id }}">
            {{ $subnet->name }}
        </button>
        @endforeach
    </div>

    <div id="cy" class="w-100 mt-3" style='height: 900px; background-color: #ddd'>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.32.0/cytoscape.min.js"
    integrity="sha512-JUacxc3LBNCUyDO2C+80nYCLPIbfPpyuD7rCpVqEz6n2PCk1LSdHNldgBzaVELc6ft+jwomNa9L2W8Wo9Dt1pA=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer">
    </script>

    <script defer>
        let cy = cytoscape({
            container: document.getElementById('cy'),
            elements: @json($organization->toCytoscape()),
            style: [
                {
                  selector: 'node',
                  style: {
                    "background-color": '#777',
                    "text-valign": "bottom",
                    "text-halign": "center",
                    "label": "data(label)"
                  }
                },
                {
                    selector: 'node[type="subnet"]',
                    style: {
                        'shape': 'square'
                    }
                }
            ],
            layout: {
                name: 'breadthfirst'
            }
        });

        cy.on('tap', 'node', function(evt){
            let node = evt.target;
            console.log('tapped ' + node.id());
            window.location = node.data("url");
        });
        
        let degreeVisible = function(node) {
            let visibleEdges = 0;
            node.connectedEdges().forEach(function(edge) {
                if (edge.visible()) {
                    visibleEdges++;
                }
            });
            return visibleEdges;
        };
        
        let refreshCytoscape = function() {
            let btn = $("#show-orphan");
            cy.nodes().forEach(function(node) {
                if (node.data('type') === 'subnet') {
                    return;
                }

                // not an orphan
                if (degreeVisible(node) !== 0) {
                    return;
                }

                if (btn.hasClass("active")) {
                    node.show();
                } else {
                    node.hide();
                }
            });
        }


        document.addEventListener("DOMContentLoaded", function() {
            @foreach ($organization->subnets as $subnet)
            $("#btn-subnet-{{ $subnet->id }}").on("click", function(evt) {
                let btn = $("#btn-subnet-{{ $subnet->id }}");
                btn.toggleClass("active").blur();
                if (btn.hasClass("active")) {
                    cy.getElementById('#subnet-{{ $subnet->id }}').show();
                } else {
                    cy.getElementById('#subnet-{{ $subnet->id }}').hide();
                }
                refreshCytoscape();
            });
            @endforeach
            
            
            
            $("#show-orphan").on("click", function(evt) {
                $("#show-orphan").toggleClass("active").blur();
                refreshCytoscape();
            });
        });
    </script>
</div>
@endsection