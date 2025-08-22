<ul {!! BaseHelper::clean($options) !!}>
    @foreach ($menu_nodes as $key => $row)
        <li @class([
            'nav-item',
            'dropdown' => $row->has_child,
            $row->css_class,
        ])>
            <a @class([
                'nav-link',
                'dropdown-toggle' => $row->has_child,
                'active' => $row->active,
               ])
               href="{{ url($row->url) }}"
               @if ($row->has_child)
                   data-bs-toggle="dropdown"
                   aria-expanded="false"
               @endif
               @if ($row->target !== '_self')
                   target="{{ $row->target }}"
               @endif>
                {!! $row->icon_html !!}
                {{ $row->title }}
            </a>

            @if ($row->has_child)
                {!! Menu::generateMenu([
                    'menu' => $menu,
                    'menu_nodes' => $row->child,
                    'view' => 'main-menu',
                    'options' => ['class' => 'dropdown-menu'],
                ]) !!}
            @endif
        </li>
    @endforeach
</ul>

