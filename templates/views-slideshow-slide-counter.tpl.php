<!-- NOTE: Original template file is div includes span, which causes new line issue. -->
<span id="views_slideshow_slide_counter_<?php print $variables['vss_id']; ?>" class="<?php print $classes; ?>">
  <span class="num">1</span> <?php print t('of'); ?> <span class="total"><?php print count($rows); ?></span>
</span>