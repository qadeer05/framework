@if ($paginator->pages() > 1)
<?php
    $extremePagesLimit = 3;
    $nearbyPagesLimit = 2
?>

<div class="pagination">
    @if ($paginator->current() > 1)<a href="{{ $view['pagination']->path($route, $paginator->current() - 1, $params) }}">@lang('Previous')</a>@endif
</div>
<div style="clear: both"></div>
@endif