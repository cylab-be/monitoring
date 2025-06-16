<div class="card">
    <div class="card-header">
        {{ $report->title() }}

        <div class="float-right">
            <a class="badge badge-secondary"
               href="{{ action("RecordController@show", ["record" => $report->record_id]) }}">
                <i class="fas fa-search"></i>
            </a>
            <span class="badge badge-secondary">
                <i class="far fa-clock"></i> {{ $report->time() }}
            </span>
            {!! $report->status()->badge() !!}
        </div>
    </div>
    <div class="card-body">
        {!! $report->html !!}
    </div>
</div>
