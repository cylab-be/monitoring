<table class='table table-sm'>
<tr>
    <th>Slot</th>
    <th>Type</th>
    <th>Size</th>
    <th>Status</th>
</tr>

@foreach ($drives as $disk)
<tr>
    <td>{{ $disk->slot }}</td>
    <td>{{ $disk->type }}</td>
    <td>{{ $disk->size }}</td>
    <td>{{ $disk->status }}</td>
</tr>
@endforeach

</table>