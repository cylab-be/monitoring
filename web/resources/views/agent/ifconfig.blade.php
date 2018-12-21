<table class="table table-striped table-sm">
    <tr>
        <th>Name</th>
        <th>Address</th>
        <th>RX</th>
        <th>TX</th>
    </tr>

    @foreach ($interfaces as $interface)
    <tr>
        <td>{{ $interface->name }}</td>
        <td>{{ $interface->address }}</td>
        <td>{{ $interface->humanReadableRx() }}</td>
        <td>{{ $interface->humanReadableTx() }}</td>
    </tr>
    @endforeach
</table>
