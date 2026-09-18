<style>
:root{
    --font-family-primary: "TeX Gyre Heros", helvetica, arial, sans-serif;
    --font-weight-primary: 400;
    --letter-spacing-primary: 0.02em;

    --font-size-primary: <?= $site->fontSizePrimary()->isNotEmpty() ? htmlspecialchars($site->fontSizePrimary()) . 'px' : 'initial' ?>;
    --font-size-tablet: <?= $site->fontSizeTablet()->isNotEmpty() ? htmlspecialchars($site->fontSizeTablet()) . 'px' : 'var(--font-size-primary)' ?>;
    --font-size-desktop: <?= $site->fontSizeDesktop()->isNotEmpty() ? htmlspecialchars($site->fontSizeDesktop()) . 'px' : 'var(--font-size-tablet)' ?>;

    --size-xxs: calc(var(--size-s) / 1.618 / 1.618);
    --size-xs: calc(var(--size-s) / 1.309);
    --size-s: 1rem;
    --size-sm: calc(var(--size-s) * 1.309);
    --size-m: calc(var(--size-s) * 1.618);
    --size-ml: calc(var(--size-m) * 1.309);
    --size-l: calc(var(--size-m) * 1.618);
    --size-xl: calc(var(--size-l) * 1.618);
    --size-xxl: calc(var(--size-l) * 1.618);
    --size-3xl: calc(var(--size-l) * 1.618);
    --size-4xl: calc(var(--size-l) * 1.618);

    /* General site colors */
    --color-primary: <?= htmlspecialchars($site->colorPrimary()->or('#000000')) ?>;
    --color-secondary: <?= htmlspecialchars($site->colorSecondary()->or('#ffffff')) ?>;
    --color-tertiary: <?= htmlspecialchars($site->colorTertiary()->or('#0000FF')) ?>;
    --color-fourth: <?= htmlspecialchars($site->colorFourth()->or('#808080')) ?>;

    /* Global Button variables */
    --btn-color: <?= htmlspecialchars($site->buttonTextColor()->or('var(--color-primary)')) ?>;
    --btn-background-color: <?= htmlspecialchars($site->buttonBackgroundColor()->or('var(--color-secondary)')) ?>;
    --btn-border-color: <?= htmlspecialchars($site->buttonBorderColor()->or('var(--color-primary)')) ?>;

    --btn-hover-color: <?= htmlspecialchars($site->buttonHoverTextColor()->or('var(--color-secondary)')) ?>;
    --btn-hover-background-color: <?= htmlspecialchars($site->buttonHoverBackgroundColor()->or('var(--color-primary)')) ?>;
    --btn-hover-border-color: <?= htmlspecialchars($site->buttonHoverBorderColor()->or('var(--color-primary)')) ?>;

    /* Button padding */
    --btn-padding: <?= htmlspecialchars($site->buttonPadding()->or('var(--size-xs) var(--size-s) calc(var(--size-xs) + 2px) var(--size-s)')) ?>;

    /* Button shape */
    <?php
        $shape = $site->buttonShape()->or('pillshape');
        switch($shape) {
            case 'squared':
                echo '--btn-border-radius: 0;';
                break;
            case 'rounded':
                echo '--btn-border-radius: var(--size-xs);';
                break;
            case 'pillshape':
                echo '--btn-border-radius: 9999px;';
                break;
        }
    ?>
}

@media screen and (min-width: 992px) {
    :root{
        --size-xl: calc(var(--size-l) * 1.618);
        --size-xxl: calc(var(--size-xl) * 1.618);
        --size-3xl: calc(var(--size-xxl) * 1.618);
        --size-4xl: calc(var(--size-3xl) * 1.618);
    }
}
</style>