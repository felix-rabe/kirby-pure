<!DOCTYPE html>
<html lang="<?= kirby()->multilang() ? $kirby->language()->code() : ($site->siteLanguage()->isNotEmpty() ? $site->siteLanguage()->value() : 'en') ?>">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO -->

    <?php snippet('pure-seo') ?>

    <!-- Pure -->

    <?php snippet('pure-variables') ?>
    <?php if (kirby()->plugin('kirbypure/blocks')): ?>
        <?php snippet('pure-blocks-variables') ?>
    <?php endif ?>
    <?php snippet('pure-custom-head-content') ?>

</head>

<body class="<?= $page->slug() ?><?= $page->parent() ? ' ' . $page->parent()->slug() . '-subpage' : '' ?> <?= Str::slug($page->blueprint()->title()) ?>-template">
    <!-- Site Header -->

    <?php if (kirby()->plugin('kirbypure/blocks')): ?>
        <?php snippet('pure-site-header') ?>
    <?php endif ?>

    <!-- Main Content -->

    <main>