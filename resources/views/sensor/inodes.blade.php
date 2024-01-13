<table class='table table-sm'>
<tr>
    <th></th>
    <th></th>
    <th class="text-right">Usage</th>
</tr>

@foreach ($disks as $disk)
<tr>
    <td>{{ $disk->filesystem }}</td>
    <td>{{ $disk->mounted }}</td>
    <td class="text-right">{{ $disk->usedPercent() }}%</td>
</tr>
@endforeach
</table>