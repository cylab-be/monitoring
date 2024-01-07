<table class='table table-sm'>
<tr>
    <th>Port</th>
    <th>Box</th>
    <th>Bay</th>
    <th>Type</th>
    <th>Size</th>
    <th>Status</th>
</tr>

@foreach ($disks as $disk)
<tr>
    <td>{{ $disk->port }}</td>
    <td>{{ $disk->box }}</td>
    <td>{{ $disk->bay }}</td>
    <td>{{ $disk->type }}</td>
    <td>{{ $disk->size }}</td>
    <td>{{ $disk->status }}</td>
</tr>
@endforeach

</table>