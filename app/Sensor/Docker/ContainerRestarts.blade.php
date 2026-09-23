<table class='table table-sm'>
    <tr>
        <th>Container</th>
        <th class="text-right">Restarts in 24h</th>
    </tr>

    @foreach ($restarts as $name => $restarts)
    <tr>
        <td>{{ $name }}</td>
        <td class="text-right">{{ $restarts }}</td>
    </tr>
    @endforeach
</table>