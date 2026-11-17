<?php
$multilang = kirby()->multilang();
$languages = $multilang ? kirby()->languages() : [];
?>
<?= '<?xml version="1.0" encoding="utf-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"<?= $multilang ? ' xmlns:xhtml="http://www.w3.org/1999/xhtml"' : '' ?>>
<?php foreach ($pages as $p): ?>
    <?php
    $alternates = [];
    if ($multilang) {
        foreach ($languages as $language) {
            $code = $language->code();
            if ($language->isDefault() || $p->translation($code)->exists()) {
                $alternates[$code] = $p->url($code);
            }
        }
    } else {
        $alternates[''] = $p->url();
    }
    ?>
    <?php foreach ($alternates as $url): ?>
    <url>
        <loc><?= html($url) ?></loc>
        <?php if ($multilang): ?>
        <?php foreach ($alternates as $code => $alternateUrl): ?>
        <xhtml:link rel="alternate" hreflang="<?= html($code) ?>" href="<?= html($alternateUrl) ?>" />
        <?php endforeach ?>
        <xhtml:link rel="alternate" hreflang="x-default" href="<?= html($p->url(kirby()->defaultLanguage()->code())) ?>" />
        <?php endif ?>
        <lastmod><?= $p->modified('c', 'date') ?></lastmod>
        <priority><?= $p->isHomePage() ? 1 : number_format(0.5 / max(1, $p->depth()), 1) ?></priority>
    </url>
    <?php endforeach ?>
<?php endforeach ?>
</urlset>
