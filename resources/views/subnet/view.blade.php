@extends('layouts.dashboard')
@section('title', 'Subnets | ' . $organization->name )

@section('content')
<div class="container-fluid h-100">
    <h1>{{ $organization->name }}</h1>

    <div>
        <button class="btn btn-outline-primary active"
                id="show-orphan">
            Show orphan devices
        </button>
    </div>

    <div id="cy" class="w-100 h-100">

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
            console.log('tapped ' + node.id());
            window.location = node.data("url");
        });


        document.addEventListener("DOMContentLoaded", function() {
            $("#show-orphan").on("click", function(evt) {
                let btn = $("#show-orphan");
                if (btn.hasClass("active")) {
                    console.log("hide orphan devices");
                    btn.removeClass("active")
                            .blur();
                    cy.nodes().forEach(function(node) {
                        if (node.degree() === 0) {
                            node.hide();
                        }
                    });
                } else {
                    console.log("show orphan devices");
                    btn.addClass("active")
                            .blur();

                    cy.nodes().forEach(function(node) {
                        node.show();
                    });
                }
            });
        });
    </script>
</div>
@endsection