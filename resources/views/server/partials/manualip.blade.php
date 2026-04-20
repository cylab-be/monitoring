<div class="card">
    <div class="card-header">Manual IP addresses</div>
    <div class="card-body">
        <table class="table table-sm">
            @foreach ($server->ips as $ip)
            <tr>
                <td>{{ $ip->ip }}</td>
                <td>{{ $ip->comment }}</td>
                <td class="text-right">
                    <form method="POST"
                          class="d-inline-block"
                          action="{{ route('ips.destroy', ['ip' => $ip]) }}">
                        {{ csrf_field() }}
                        {{ method_field("DELETE") }}
                        <button class="btn btn-danger btn-sm">
                            <i class="fa fa-times-circle" aria-hidden="true"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>

        <a class="btn btn-primary btn-sm"
            href="{{ route("ips.create", ["server" => $server]) }}">
               New
        </a>
    </div>
</div>
