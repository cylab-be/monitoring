
<table class="table table-sm">
    @foreach ($records as $record)
    <tr>
        <td>
            <a class="badge badge-secondary"
                   href="{{ action("RecordController@show", ["record" => $record]) }}">
                    <i class="fas fa-search"></i>
                </a>
        </td>
        <td>{{ $record->time() }}</td>
        <td>{{ $record->label }}</td>
        <td>{{ substr($record->data, 0, 60) }}</td>
    </tr>
    @endforeach
</table>

<div class="text-center">
    {{ $records->links() }}
</div>