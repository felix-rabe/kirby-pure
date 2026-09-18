<!-- Pure head content -->
 <?php if ($site->customHeadContent()->isNotEmpty()): ?>
  <?= $site->customHeadContent()->value() ?>
<?php endif ?>