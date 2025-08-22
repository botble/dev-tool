@if (is_plugin_active('blog'))
    @php
        $recentPosts = get_recent_posts($limit ?? 3);
    @endphp

    @if ($recentPosts->isNotEmpty())
        <div class="recent-posts">
            <h3 class="mb-3">{{ __('Recent Posts') }}</h3>
            <div class="row">
                @foreach ($recentPosts as $post)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            @if ($post->image)
                                <img src="{{ RvMedia::getImageUrl($post->image) }}"
                                     class="card-img-top"
                                     alt="{{ $post->name }}"
                                     @if(theme_option('lazy_load_images', 'no') == 'yes') loading="lazy" @endif>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ $post->url }}" class="text-decoration-none">
                                        {{ $post->name }}
                                    </a>
                                </h5>
                                @if ($post->description)
                                    <p class="card-text">
                                        {{ Str::limit($post->description, 100) }}
                                    </p>
                                @endif
                                <a href="{{ $post->url }}" class="btn btn-sm btn-primary">
                                    {{ __('Read more') }}
                                </a>
                            </div>
                            <div class="card-footer text-muted small">
                                <i class="bi bi-calendar"></i> {{ $post->created_at->format('M d, Y') }}
                                @if ($post->author)
                                    · <i class="bi bi-person"></i> {{ $post->author->name }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endif
