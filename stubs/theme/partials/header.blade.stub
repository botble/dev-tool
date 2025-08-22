<!DOCTYPE html>
<html {!! Theme::htmlAttributes() !!}>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {!! Theme::header() !!}
    </head>
    <body {!! Theme::bodyAttributes() !!}>
        {!! apply_filters(THEME_FRONT_BODY, null) !!}

        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="{{ BaseHelper::getHomepageUrl() }}">
                    @if($logo = Theme::getLogo())
                        {{ Theme::getLogoImage(maxHeight: 50) }}
                    @else
                        {{ theme_option('site_title', 'Your Site') }}
                    @endif
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    {!! Menu::renderMenuLocation('main-menu', [
                        'options' => ['class' => 'navbar-nav ms-auto'],
                        'view' => 'main-menu',
                    ]) !!}
                </div>
            </div>
        </nav>
