@foreach($activities as $activity)
    @php
        if(!isset($created_at) || Carbon::parse($activity->created_at)->format('d-M-Y') < $created_at->format('d-M-Y')) {
            $created_at = $activity->created_at;
            $next_date = true;
        } else {
            $next_date = false;
        }
    @endphp
    @if($next_date)
        <li class="time-label">
            <span class="bg-green">
            {{ $created_at->format('d-M-Y') }}
            </span>
        </li>
    @endif
    <li>
        {!! $activity->getIconMarkup() !!}
        <div class="timeline-item">
            <span class="time"><i class="fa fa-clock"></i> {{ Carbon::parse($activity->created_at)->format('g:i a') }}</span>
            <h3 class="timeline-header">Activity By - <a href="#">{{ $activity->user->name ?? "unknown" }}</a></h3>
            <div class="timeline-body">
                {!! $activity->getHtmlDescription() !!}
            </div>
        </div>
    </li>
@endforeach

<li>
    <i class="fa fa-clock bg-gray"></i>
</li>
<div class="text-center">
<button class="crm-load-more btn btn-link btn-sm" page-number>load more</button>
</div>