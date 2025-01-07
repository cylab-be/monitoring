<table class='table table-sm'>
    <tr>
        <th>Locator</th>
        <th class="text-right">Size</th>
        <th class="text-right">Type</th>
        <th class="text-right">Speed</th>
        <th class="text-right">Manufacturer</th>
        <th class="text-right">Part Number</th>
        <th class="text-right">Configured Speed</th>
    </tr>

@foreach ($dims as $dim)
    <tr>
        <td><b>{{ $dim->locator }}</b></td>
        <td class="text-right">{{ $dim->size }} GB</td>
        <td class="text-right">{{ $dim->type }}</td>
        <td class="text-right">{{ $dim->speed }}</td>
        <td class="text-right">{{ $dim->manufacturer }}</td>
        <td class="text-right">{{ $dim->part_number }}</td>
        <td class="text-right">{{ $dim->configured_speed }}</td>
    </tr>
@endforeach

</table>