# Kirby Pure

> **Experimental Early Alpha Release**

### About

Kirby Pure is a plugin that establishes an infrastructure for keeping reusable components and entire projects **within the plugin layer** while keeping Kirby's Plainkit clean. It is intended to be used with [Kirby Pure Blocks](https://github.com/felix-rabe/kirby-pure-blocks).

### Intended strategy

```text
site/
└── plugins/
    ├── kirby-pure/
    ├── kirby-pure-blocks/
    └── your-project/
```
- `Kirby CMS Plainkit` provides the foundation and content organization.
- `Kirby Pure` provides header and footer snippets to utilize site and SEO settings as well as reusable variables.
- `Kirby Pure Blocks` provides image and video rendering markups as well as a set of blocks.
- `Your Project` provides your fonts, custom blocks, layout and all other project-specific functionality.

### Recommended AI Workflow

Feed `kirby-pure.zip` and `kirby-pure-blocks.zip` to your AI agent and instruct it to build your project as a dedicated plugin and give it to you as `your-project.zip` file. Then start iterating. 

## Requirements

- Kirby 5

## Installation

Copy this repository to:

```text
site/plugins/kirby-pure
```

## Integration

Kirby Pure automatically loads its core CSS and JavaScript assets.

### Site Blueprint

Pure provides the following site blueprint tabs for your site.yml:

```yaml
tabs:
  siteSettings:
    extends: tabs/pure-site-settings

  seo:
    extends: tabs/pure-seo
```

### Header and Footer

Add the Pure header and footer snippets to your project template to utilize the site- and seo-settings:

```php
<?php
snippet('pure-header');
snippet('pure-footer');
?>
```

`pure-header` should be rendered at the beginning of the page template and `pure-footer` at the end, for they provide a `html`, `head`, `body`, and `footer` markup.

## Core Assets

### Font

Kirby Pure includes `TeX Gyre Heros` as its open source default font. The webfont files are part of the plugin and are loaded through `pure-fonts.css`.

### On-Scroll Transitions

Kirby Pure includes an optional on-scroll transition system that can be configured in the Panel site settings. Available effects are `None`, `Stagger`, `Fade In`, and `Fade In + Slide Up`. Elements can participate in the transition system using the global helper:

```html
<div <?= pureOnScrollAttribute() ?>>
```

When using such a renderer inside an element that already handles the transition, its own transition can be disabled to avoid applying the transition twice:

```php
snippet('render/pure-image', [
    'image' => $image,
    'onScrollTransition' => false,
]);
```
```html
<div <?= pureOnScrollAttribute($onScrollTransition) ?>>
```

## SEO and Indexing

Kirby Pure provides the basic infrastructure for search-engine indexing and machine-readable site information.

### Global SEO

Global SEO settings are configured through the Pure SEO site tab we already integrated above. 

### Page SEO

Kirby Pure provides a reusable SEO section for individual page blueprints that can override the global SEO settings:

```yaml
tabs:
  seo:
    label: SEO
    icon: search
    columns:
      main:
        width: 1/2
        sections:
          seo:
            extends: sections/pure-page-seo
```

### Sitemap, robots.txt, llms.txt & Schema.org

Pure automatically provides site-level `sitemap.xml`, `robots.txt`, `llms.txt`, and `Schema.org` structured data.

The sitemap includes indexable pages only. `robots.txt` follows the site's indexing settings, while `llms.txt` provides basic information about the website. Schema.org metadata is generated automatically from the site's content and settings.

Custom `robots.txt`, `llms.txt` and `Schema.org` content can optionally be provided through the site's SEO settings.

## Recommended Plugins

Pure works without these plugins, but the following additions are recommended for a typical setup:

- [Kirby Pure Blocks](https://github.com/felix-rabe/kirby-pure-blocks) – Flexible blocks and layout tools designed to work with Pure.

- [Kirby Pure Stats](https://github.com/felix-rabe/kirby-pure-stats) – Simple, privacy-friendly page-view statistics.

- [Fingerprint](https://github.com/bnomei/kirby3-fingerprint) – Optional asset cache busting. Pure automatically falls back to Kirby's standard asset helpers when unavailable. Disable HTTPS enforcement when hosting in an HTTP environment via `config.php`

```php
// config.php
'bnomei.fingerprint.forceHttps' => false,
```

- [Kirby Backups](https://github.com/sylvainjule/kirby-backups) – Create and manage content backups from the Panel.

- [Visual Block Selector](https://github.com/junohamburg/kirby-visual-block-selector) – Visual block selection in the Panel.

- [Kirby Trash](https://github.com/sigtrygg-space/kirby-trash) – Adds a trash workflow for deleted pages.

- [Video Thumbnail](https://github.com/yolu-ch/kirby-video-thumbnail) – Generate thumbnails for uploaded videos.

## Structure

```text
kirby-pure/
├── assets/
│   ├── css/
│   │   ├── pure.css
│   │   ├── pure-layout.css
│   │   ├── pure-fonts.css
│   │   └── pure-on-scroll-transitions.css
│   ├── fonts/
│   │   └── TeX-Gyre-Heros/
│   └── js/
│       └── pure-on-scroll-transitions.js
│
├── blueprints/
│   ├── sections/
│   │   └── pure-page-seo.yml
│   └── tabs/
│       ├── pure-site-settings.yml
│       └── pure-seo.yml
│
├── helpers/
│   └── on-scroll-transition.php
│
├── snippets/
│   ├── pure-header.php
│   ├── pure-footer.php
│   ├── pure-variables.php
│   ├── pure-seo.php
│   ├── pure-custom-head-content.php
│   └── pure-sitemap.php
│
├── .gitignore
├── LICENSE
├── composer.json
├── index.css
└── index.php
```

## License

MIT © 2026 Felix Rabe
