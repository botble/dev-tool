@if (Theme::breadcrumb()->getCrumbs())
    <nav aria-label="{{ __('Breadcrumb') }}">
        <ol class="breadcrumb">
            @foreach (Theme::breadcrumb()->getCrumbs() as $i => $crumb)
                @if ($crumb['label'])
                    <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                        @if (!$loop->last && $crumb['url'])
                            <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                        @else
                            {{ $crumb['label'] }}
                        @endif
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif