<table class='table table-sm'>
    @foreach ($unused_commands as $label => $command)
    <tr>
        <td>{{ $label }}</td>
        <td>{{ $command }}</td>
    </tr>
    @endforeach
</table>
