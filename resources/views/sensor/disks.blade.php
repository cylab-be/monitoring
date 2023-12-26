<table class='table table-sm'>
<tr>
    <th></th>
    <th></th>
    <th></th>
    <th class="text-right">Usage</th>
</tr>
@foreach ($partitions as $partition)
<tr>
    <td>{{ $partition->filesystem }}</td>
    <td>{{ $partition->mounted }}</td>
    <td class="text-right">
        {{ $partition->usedGB() }} / {{ $partition->sizeGB() }}GB
    </td>
    <td class="text-right">
        {{ $partition->usedPercent() }}%
    </td>
</tr>
@endforeach
</table>