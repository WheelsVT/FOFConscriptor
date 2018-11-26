<?
include 'includes/classes.inc.php';
process_pick_queue();
$page = new page('');
echo $page->draft_status();
echo '<><>';
echo $page->extra_data;
?>