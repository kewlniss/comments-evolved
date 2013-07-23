<?php
/**
 * @author         Brandon Holtsclaw <me@brandonholtsclaw.com>
 * @copyright      2013 Brandon Holtsclaw
 * @license        GPL
 */
defined('ABSPATH') or exit;

?>
<!-- wp-tab -->
<div id="wordpress-tab" class="clearfix">
<?php
if (file_exists(TEMPLATEPATH . '/comments.php')) {
  include_once TEMPLATEPATH . '/comments.php';
} elseif (file_exists(TEMPLATEPATH . '/includes/comments.php')) {
  include_once TEMPLATEPATH . '/includes/comments.php';
}
?>
</div>
<!-- //wp-tab -->
