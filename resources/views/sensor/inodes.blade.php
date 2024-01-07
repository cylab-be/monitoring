<table class='table table-sm'>
<tr>
    <th></th>
    <th></th>
    <th>Usage</th>
</tr>

@foreach ($disks as $disk)
<tr>
    <td>{{ $disk->filesystem }}</td>
    <td>{{ $disk->mounted }}</td>
    <td>{{ $disk->usedPercent() }}%</td>
</tr>
@endforeach
</table>