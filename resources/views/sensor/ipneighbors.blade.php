<table class="table table-sm">
    @foreach ($neighbors as $neighbor)
    <tr>
        <td>{{ $neighbor->dst }}</td>
        <td>{{ $neighbor->lladdr }}</td>
        <td>{{ $neighbor->vendor }}</td>
    </tr>
    @endforeach
</table>