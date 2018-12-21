<table class="table table-striped table-sm">
    @foreach ($interfaces as $interface)
    <tr>
        <td>{{ $interface->name }}</td>
        <td>{{ $interface->address }}</td>
        <td>{{ $interface->rx }}</td>
        <td>{{ $interface->tx }}</td>
    </tr>
    @endforeach
</table>
