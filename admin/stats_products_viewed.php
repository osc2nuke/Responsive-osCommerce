<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/template_top.php');
?>

<div class="page-header">
	<h1><?php echo HEADING_TITLE; ?></h1>
</div>
<div class="row">
	<div class="col-md-8">	
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr class="dataTableHeadingRow">
					<th class="dataTableHeadingContent"><?php echo TABLE_HEADING_NUMBER; ?></th>
					<th class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></th>
					<th class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_VIEWED; ?>&nbsp;</th>
				</tr>
			</thead>
<?php
  if (isset($_GET['page']) && ($_GET['page'] > 1)) $rows = $_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS - MAX_DISPLAY_SEARCH_RESULTS;
  $rows = 0;
  $products_query_raw = "select p.products_id, pd.products_name, pd.products_viewed, l.name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_LANGUAGES . " l where p.products_id = pd.products_id and l.languages_id = pd.language_id order by pd.products_viewed DESC";
  $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_query_raw, $products_query_numrows);
  $products_query = tep_db_query($products_query_raw);
  while ($products = tep_db_fetch_array($products_query)) {
    $rows++;

    if (strlen($rows) < 2) {
      $rows = '0' . $rows;
    }
?>
			<tr class="dataTableRow" onclick="document.location.href='<?php echo tep_href_link('categories.php', 'action=new_product_preview&read=only&pID=' . $products['products_id'] . '&origin=stats_products_viewed.php?page=' . $_GET['page']); ?>'">
				<td class="dataTableContent"><?php echo $rows; ?>.</td>
				<td class="dataTableContent"><?php echo '<a href="' . tep_href_link('categories.php', 'action=new_product_preview&read=only&pID=' . $products['products_id'] . '&origin=stats_products_viewed.php?page=' . $_GET['page']) . '">' . $products['products_name'] . '</a> (' . $products['name'] . ')'; ?></td>
				<td class="dataTableContent" align="center"><?php echo $products['products_viewed']; ?>&nbsp;</td>
			</tr>
<?php
  }
?>
		</table>
		<nav>
			<ul class="pagination float-left"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></ul>
            <?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
		</nav>
	</div>
<?php
  echo '</div>';//row end
  
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
