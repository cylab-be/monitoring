<table class='table table-sm'>
    <tr>
        <th>Name</th>
        <th class="text-right">Restarts</th>
    </tr>

    @foreach ($restarts as $name => $restarts)
    <tr>
        <td>{{ $name }}</td>
        <td class="text-right">{{ $restarts }}</td>
    </tr>
    @endforeach
</table>