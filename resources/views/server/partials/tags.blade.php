
<div class="card">
    <div class="card-header">Tags</div>
    <div class="card-body">

        <form action="{{ route("servers.tags.add", ["server" => $server]) }}" method="POST">
            @csrf

            <div class="input-group">
                <select name="tag_id" class="form-control">
                    @foreach ($organization->tags->sortBy("name") as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Add</button>
                </div>
            </div>
        </form>


        <table class="table table-sm mt-3">
            @foreach ($server->tags as $tag)
            <tr>
                <td>{{ $tag->name }}</td>
                <td class="text-right">
                    <form method="POST"
                          class="d-inline-block"
                          action="{{ route('servers.tags.remove', ['server' => $server, 'tag' => $tag]) }}">
                        {{ csrf_field() }}
                        {{ method_field("DELETE") }}
                        <button class="btn btn-danger btn-sm">
                            <i class="fa fa-times-circle" aria-hidden="true"></i> Remove
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>