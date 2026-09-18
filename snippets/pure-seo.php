<?php

// LANGUAGE
$languageCode = kirby()->multilang()
    ? kirby()->language()->code()
    : $site->siteLanguage()->or('en')->value();

$ogLocales = [
    'en' => 'en_US',
    'de' => 'de_DE',
    'fr' => 'fr_FR',
    'it' => 'it_IT',
    'es' => 'es_ES',
    'nl' => 'nl_NL',
    'pt' => 'pt_PT',
];

$ogLocale = $ogLocales[$languageCode]
    ?? str_replace('-', '_', $languageCode);

// GLOBAL INDEXING
$allowIndexing = $site->indexPage()->toBool();

// PAGE-LEVEL INDEXING OVERRIDE
$pageNoindex = $page->noindex()->toBool();
$shouldIndex = $allowIndexing && !$pageNoindex;

// PAGE TITLE
if ($page->seoTitle()->isNotEmpty()) {
    $pageTitle = $page->seoTitle()->value();
} elseif ($page->isHomePage() && $site->seoTitle()->isNotEmpty()) {
    $pageTitle = $site->seoTitle()->value();
} elseif ($page->isHomePage()) {
    $pageTitle = $site->title()->value();
} else {
    $pageTitle = $site->title()->value() . ' – ' . $page->title()->value();
}

// PAGE DESCRIPTION
$pageDescription = $page->seoDescription()->isNotEmpty()
    ? $page->seoDescription()->value()
    : $site->seoDescription()->value();

// OPEN GRAPH IMAGE
$seoImage = $page->ogImage()->toFile()
    ?? $site->ogImage()->toFile();

// CANONICAL URL
$canonicalUrl = $page->url();
?>

<title><?= html($pageTitle) ?></title>

<?php if ($pageDescription !== ''): ?>
<meta name="description" content="<?= esc($pageDescription, 'attr') ?>">
<?php endif ?>

<meta name="robots" content="<?= $shouldIndex ? 'index,follow' : 'noindex' ?>">

<?php if ($site->themeColor()->isNotEmpty()): ?>
<meta name="theme-color" content="<?= esc($site->themeColor()->value(), 'attr') ?>">
<?php endif ?>

<link rel="canonical" href="<?= esc($canonicalUrl, 'attr') ?>">

<!-- Open Graph -->
<meta property="og:title" content="<?= esc($pageTitle, 'attr') ?>">
<?php if ($pageDescription !== ''): ?>
<meta property="og:description" content="<?= esc($pageDescription, 'attr') ?>">
<?php endif ?>
<?php if ($seoImage): ?>
<meta property="og:image" content="<?= esc($seoImage->url(), 'attr') ?>">
<meta property="og:image:width" content="<?= esc($seoImage->width(), 'attr') ?>">
<meta property="og:image:height" content="<?= esc($seoImage->height(), 'attr') ?>">
<meta property="og:image:type" content="<?= esc($seoImage->mime(), 'attr') ?>">
<?php endif ?>
<meta property="og:url" content="<?= esc($canonicalUrl, 'attr') ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= esc($site->title()->value(), 'attr') ?>">
<meta property="og:locale" content="<?= esc($ogLocale, 'attr') ?>">

<!-- Twitter Card -->
<meta name="twitter:card" content="<?= $seoImage ? 'summary_large_image' : 'summary' ?>">
<meta name="twitter:title" content="<?= esc($pageTitle, 'attr') ?>">
<?php if ($pageDescription !== ''): ?>
<meta name="twitter:description" content="<?= esc($pageDescription, 'attr') ?>">
<?php endif ?>
<?php if ($seoImage): ?>
<meta name="twitter:image" content="<?= esc($seoImage->url(), 'attr') ?>">
<?php if ($seoImage->alt()->isNotEmpty()): ?>
<meta name="twitter:image:alt" content="<?= esc($seoImage->alt(), 'attr') ?>">
<?php endif ?>
<?php endif ?>

<!-- Favicons -->
<?php if ($faviconSvg = $site->faviconSvg()->toFile()): ?>
  <link rel="icon"
        href="<?= esc($faviconSvg->url(), 'attr') ?>"
        type="image/svg+xml">
<?php endif ?>

<?php if ($faviconPng = $site->faviconPng()->toFile()): ?>
  <link rel="icon"
        href="<?= esc($faviconPng->url(), 'attr') ?>"
        type="image/png"
        sizes="32x32">
<?php endif ?>

<?php if ($appleTouchIcon = $site->appleTouchIcon()->toFile()): ?>
<link rel="apple-touch-icon"
      href="<?= esc($appleTouchIcon->url(), 'attr') ?>">
<?php endif ?>

<!-- Schema.org structured data -->
<?php

$customSchema = trim((string)$site->schemaJson()->value());

if (
    $customSchema !== '' &&
    json_decode($customSchema) === null &&
    json_last_error() !== JSON_ERROR_NONE
) {
    $customSchema = '';
}

if ($customSchema !== ''):

?>

<script type="application/ld+json">
<?= $customSchema ?>
</script>

<?php else:

$description = trim((string)$site->homePage()->seoDescription()
    ->or($site->seoDescription())
    ->value());

$schema = [
    '@context' => 'https://schema.org',
    '@type'    => 'WebSite',
    'name'     => $site->title()->value(),
    'url'      => $site->url(),
];

if ($description !== '') {
    $schema['description'] = $description;
}

?>

<script type="application/ld+json">
<?= json_encode(
    $schema,
    JSON_PRETTY_PRINT |
    JSON_UNESCAPED_SLASHES |
    JSON_UNESCAPED_UNICODE
) ?>
</script>

<?php endif ?>
