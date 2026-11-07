<table class="table table-sm">
    @foreach ($neighbors as $neighbor)
    <tr>
        <td>{{ $neighbor->ip }}</td>
        <td>{{ $neighbor->mac }}</td>
        <td>{{ $neighbor->vendor ?? '' }}</td>
        <td>{{ $neighbor->interface }}</td>
        <td>{{ $neighbor->seenAt()->format("Y-m-d H:i:s") }}</td>
    </tr>
    @endforeach
</table>