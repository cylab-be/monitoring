<table class='table table-sm'>
<tr>
    <th>Id</th>
    <th>Name</th>
    <th>GPU [%]</th>
    <th>Mem [%]</th>
    <th>Mem [MiB]</th>
    <th>Tot [MiB]</th>
    <th class="text-right">Temperature</th>
</tr>
@foreach ($gpus as $gpu)
<tr>
    <td>{{ $gpu["index"] }}</td>
    <td>{{ $gpu["name"] }}</td>
    <td>{{ $gpu["utilization_gpu_pct"] }}</td>
    <td>{{ $gpu["utilization_mem_pct"] }}</td>
    <td>{{ $gpu["memory_used_mib"] }}</td>
    <td>{{ $gpu["memory_total_mib"] }}</td>
    <td class="text-right">
        {{ $gpu["temperature_gpu"] }}
    </td>
</tr>
@endforeach
</table>