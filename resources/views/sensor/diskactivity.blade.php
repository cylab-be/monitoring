<table class='table table-sm'>
<tr>
    <th>Disk</th>
    <th class="text-right">Activity</th>
</tr>
@foreach ($values as $disk => $value)
<tr>
    <td>{{ $disk }}</td>
    <td class="text-right">
        {{ $value }}%
    </td>
</tr>
@endforeach
</table>