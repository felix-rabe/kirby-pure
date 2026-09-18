<?php

require_once __DIR__ . '/helpers/on-scroll-transition.php';

Kirby::plugin('kirbypure/core', [
    'blueprints' => [
        'tabs/pure-site-settings' =>
            __DIR__ . '/blueprints/tabs/pure-site-settings.yml',

        'tabs/pure-seo' =>
            __DIR__ . '/blueprints/tabs/pure-seo.yml',


        'sections/pure-page-seo' =>
            __DIR__ . '/blueprints/sections/pure-page-seo.yml',
    ],

    'snippets' => [
        'pure-variables' =>
            __DIR__ . '/snippets/pure-variables.php',

        'pure-seo' =>
            __DIR__ . '/snippets/pure-seo.php',

        'pure-custom-head-content' =>
            __DIR__ . '/snippets/pure-custom-head-content.php',

        'pure-sitemap' =>
            __DIR__ . '/snippets/pure-sitemap.php',

        'pure-header' =>
            __DIR__ . '/snippets/pure-header.php',
        
        'pure-footer' =>
        __DIR__ . '/snippets/pure-footer.php',
        
    ],

    'hooks' => [
        'page.render:after' => function (
            string $contentType,
            array $data,
            string $html
        ) {
            /*
             * Core styles
             */
            $stylesheets = [
                '/media/plugins/kirbypure/core/css/pure.css',
                '/media/plugins/kirbypure/core/css/pure-layout.css',
                '/media/plugins/kirbypure/core/css/pure-fonts.css',
            ];

            /*
             * Core JavaScript
             */
            $javascript = [];

            /*
             * Optional on-scroll transitions
             */
            if (site()->onScrollTransitions()->or('none')->value() !== 'none') {
                $stylesheets[] =
                    '/media/plugins/kirbypure/core/css/pure-on-scroll-transitions.css';

                $javascript[] =
                    '/media/plugins/kirbypure/core/js/pure-on-scroll-transitions.js';
            }

            $css = '';
            $js = '';

            foreach ($stylesheets as $path) {
                $css .= class_exists('Bnomei\\Fingerprint')
                    ? Bnomei\Fingerprint::css($path)
                    : css($path);

                $css .= PHP_EOL;
            }

            foreach ($javascript as $path) {
                $js .= class_exists('Bnomei\\Fingerprint')
                    ? Bnomei\Fingerprint::js($path)
                    : js($path);

                $js .= PHP_EOL;
            }

            $html = str_replace(
                '</head>',
                $css . '</head>',
                $html
            );

            if ($js !== '') {
                $html = str_replace(
                    '</body>',
                    $js . '</body>',
                    $html
                );
            }

            return $html;
        }
    ],

    'routes' => [

        // Sitemap
        [
            'pattern' => 'sitemap.xml',
            'action' => function () {

                $ignore = kirby()->option('sitemap.ignore', ['error']);

                // If global indexing is disabled, return an empty sitemap.
                if (!site()->indexPage()->toBool()) {
                    $pages = new Kirby\Cms\Pages([]);

                    $content = snippet(
                        'pure-sitemap',
                        compact('pages', 'ignore'),
                        true
                    );

                    return new Kirby\Cms\Response(
                        $content,
                        'application/xml'
                    );
                }

                $pages = site()
                    ->pages()
                    ->index()
                    ->filter(
                        function ($page) use ($ignore) {

                            // Exclude pages explicitly set to noindex.
                            if ($page->noindex()->toBool()) {
                                return false;
                            }

                            // Exclude ignored page IDs and templates.
                            if (
                                in_array(
                                    $page->id(),
                                    $ignore,
                                    true
                                ) ||
                                in_array(
                                    $page->intendedTemplate()->name(),
                                    $ignore,
                                    true
                                )
                            ) {
                                return false;
                            }

                            return true;
                        }
                    );

                // Add the home page when it is indexable.
                $homePage = site()->homePage();

                if (
                    $homePage &&
                    !$homePage->noindex()->toBool()
                ) {
                    $pages = new Kirby\Cms\Pages([
                        $homePage,
                        ...$pages->values()
                    ]);
                }

                $content = snippet(
                    'pure-sitemap',
                    compact('pages', 'ignore'),
                    true
                );

                return new Kirby\Cms\Response(
                    $content,
                    'application/xml'
                );
            }
        ],

        [
            'pattern' => 'sitemap',
            'action' => function () {
                return go('sitemap.xml', 301);
            }
        ],

        // robots.txt
        [
            'pattern' => 'robots.txt',
            'method' => 'GET',
            'action' => function () {

                $site = site();

                $customContent = trim(
                    (string)$site->robotsTxt()->value()
                );

                if ($customContent !== '') {
                    $content = $customContent;
                } else {
                    $content = implode("\n", [
                        'User-agent: *',
                        $site->indexPage()->toBool()
                            ? 'Allow: /'
                            : 'Disallow: /',
                        '',
                        'Sitemap: ' . url('sitemap.xml'),
                    ]);
                }

                return new Kirby\Cms\Response(
                    rtrim($content) . "\n",
                    'text/plain'
                );
            }
        ],

        // llms.txt
        [
            'pattern' => 'llms.txt',
            'method' => 'GET',
            'action' => function () {

                $site = site();

                $customContent = trim(
                    (string)$site->llmsTxt()->value()
                );

                if ($customContent !== '') {
                    $content = $customContent;
                } else {

                    $description = trim(
                        (string)$site
                            ->homePage()
                            ->seoDescription()
                            ->or($site->seoDescription())
                            ->value()
                    );

                    $lines = [
                        '# ' . $site->title()->value(),
                    ];

                    if ($description !== '') {
                        $lines[] = '';
                        $lines[] = '> ' . $description;
                    }

                    $lines[] = '';
                    $lines[] = '## Website';
                    $lines[] = '';

                    $lines[] =
                        '- [' .
                        $site->title()->value() .
                        '](' .
                        $site->url() .
                        ')';

                    $lines[] =
                        '- [Sitemap](' .
                        url('sitemap.xml') .
                        ')';

                    $content = implode("\n", $lines);
                }

                return new Kirby\Cms\Response(
                    rtrim($content) . "\n",
                    'text/plain'
                );
            }
        ],
    ],
]);