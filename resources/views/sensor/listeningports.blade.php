<table class='table table-sm'>
<tr>
    <th>Port</th>
    <th>Proto</th>
    <th>Bind address</th>
    <th>Process</th>
</tr>

@foreach ($ports as $port) 
<tr>
    <td>{{ $port->port }}</td>
    <td>{{ $port->proto }}</td>
    <td>{{ $port->bind }}</td>
    <td>{{ $port->process }}</td>
</tr>
@endforeach
</table>