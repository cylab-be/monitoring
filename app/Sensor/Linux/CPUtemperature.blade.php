<table class='table table-sm'>
    <tr>
        <th>Name</th>
        <th class="text-right">Temperature (°C)</th>
        <th class="text-right">T°crit (°C)</th>
    </tr>

@foreach ($cpus as $CPU) 
    <tr>
        <td><b>CPU {{ $CPU->name }}</b></td>
        <td class="text-right"><b> {{ $CPU->value }}</b></td>
        <td class="text-right"><b>{{ $CPU->critvalue }}</b></td>
    </tr>
    
    @foreach ($CPU->cores as $core) 
    <tr>
        <td>{{ $core->name }}</td>
        <td class="text-right">{{ $core->value  }}</td>
        <td class="text-right">{{ $CPU->critvalue }}</td>
    </tr>
    @endforeach
@endforeach

</table>