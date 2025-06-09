<table class='table table-sm'>
    <tr>
        <th>Name</th>
        <th>Status</th>
        <th class="text-right">Location</th>
    </tr>

    @foreach ($stacks as $stack)
    <tr>
        <td><b>{{ $stack->Name }}</b></td>
        <td>{{ $stack->Status }}</td>
        <td class="text-right">{{ $stack->ConfigFiles }}</td>
    </tr>
    @endforeach
</table>