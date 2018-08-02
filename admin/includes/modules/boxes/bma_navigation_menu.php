<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  class bma_navigation_menu {
    var $code = 'bma_navigation_menu';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_ADMIN_BOXES_NAVIGATION_MENU_TITLE;
      $this->description = MODULE_ADMIN_BOXES_NAVIGATION_MENU_DESCRIPTION;

      if ( defined('MODULE_ADMIN_BOXES_NAVIGATION_MENU_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_BOXES_NAVIGATION_MENU_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_BOXES_NAVIGATION_MENU_STATUS == 'True');

        $this->group = 'boxes_column_left';
      }
    }
    private static function sort_admin_boxes($a, $b) {
      return strcasecmp($a['heading'], $b['heading']);
    }
    private static function sort_admin_boxes_links($a, $b) {
      return strcasecmp($a['title'], $b['title']);
    }
    
    function execute() {
      global $oscTemplate, $language, $cfgModules, $cfgAdminModules;
        $_JS ='<script>
            //Set the root
            $rootPath = "'. DIR_WS_ADMIN .'";
            
            //Keep state of collapse menu via localStorage
            var adminCollapseAppMenu = localStorage.getItem("adminCollapseAppMenu");
            if (!adminCollapseAppMenu) {
                adminCollapseAppMenu = [];
                localStorage.setItem("adminCollapseAppMenu", JSON.stringify(adminCollapseAppMenu));
            } else {
                adminCollapseAppMenuArray = JSON.parse(adminCollapseAppMenu);
                var arrayLength = adminCollapseAppMenuArray.length;
                for (var i = 0; i < arrayLength; i++) {
                    var panel = "#"+adminCollapseAppMenuArray[i];
                    $(panel).addClass("show");
                    $(panel).prev().find("a").attr("aria-expanded", "true");
                }
            }
            $("#adminAppMenu").on("shown.bs.collapse", ".card-collapse", function() {
                adminCollapseAppMenu = JSON.parse(localStorage.getItem("adminCollapseAppMenu"));
                if ($.inArray($(this).attr("id"), adminCollapseAppMenu) == -1) {
                    adminCollapseAppMenu.push($(this).attr("id"));
                };
                $collapse = $(this).prev().find("a").attr("data-target");
                $($collapse).attr("aria-expanded", "true");
                localStorage.setItem("adminCollapseAppMenu", JSON.stringify(adminCollapseAppMenu));
            });
            $("#adminAppMenu").on("hidden.bs.collapse", ".card-collapse", function() {
                adminCollapseAppMenu = JSON.parse(localStorage.getItem("adminCollapseAppMenu"));
                adminCollapseAppMenu.splice( $.inArray($(this).attr("id"), adminCollapseAppMenu), 1 ); 		
                localStorage.setItem("adminCollapseAppMenu", JSON.stringify(adminCollapseAppMenu));
            });	
            if ( window.location.pathname == $rootPath || window.location.pathname == $rootPath+"index.php"){ 
                //Close panels if navigate to index
                adminCollapseAppMenu = [];
                localStorage.setItem("adminCollapseAppMenu", JSON.stringify(adminCollapseAppMenu));
                $("#adminAppMenu .sidebar-heading a").attr("aria-expanded", "false");
                $("#adminAppMenu .card-collapse").removeClass("show");
            }	
        </script>';
    
        $cl_box_groups = array();

        if ($dir = @dir(DIR_FS_ADMIN . 'includes/boxes')) {
          $files = array();

          while ($file = $dir->read()) {
            if (!is_dir($dir->path . '/' . $file)) {
              if (substr($file, strrpos($file, '.')) == '.php') {
                $files[] = $file;
              }
            }
          }

          $dir->close();

          natcasesort($files);

          foreach ( $files as $file ) {
            if ( file_exists(DIR_FS_ADMIN . 'includes/languages/' . $language . '/modules/boxes/' . $file) ) {
              include(DIR_FS_ADMIN . 'includes/languages/' . $language . '/modules/boxes/' . $file);
            }
            include_once($dir->path . '/' . $file);
          }
        }

        usort($cl_box_groups, array('bma_navigation_menu', 'sort_admin_boxes'));

        foreach ( $cl_box_groups as &$group ) {
          usort($group['apps'], array('bma_navigation_menu', 'sort_admin_boxes_links'));
        }
	
	$data = '';
	
	$counter = 0;

        foreach ($cl_box_groups as $groups) {
        
            $data .= '  <h6 id="collapseListGroupHeading'.$counter.'" class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">';
            $data .= '    <a class="d-flex align-items-center text-muted" role="button" data-toggle="collapse"  data-target="#collapseListGroup'.$counter.'" aria-expanded="false" aria-controls="collapseListGroup'.$counter.'"">';
            $data .= '       <span class="ml-1">' . $groups['heading'] . '</span>';
            $data .= '    </a>';
            $data .= '  </h6>';
            
            $data .= '<div id="collapseListGroup'.$counter.'" class="card-collapse collapse" data-parent="#columnLeft" role="tabpanel" aria-labelledby="collapseListGroupHeading'.$counter.'">';
            $data .= '      <ul class="nav flex-column mb-2">';
            
            foreach ($groups['apps'] as $app) {               
                $data .= '<li class="nav-item"><a class="nav-link" href="' . $app['link'] . '">' . $app['title'] . '</a></li>';
            }
            
            $data .= '      </ul>';
            $data .= '</div>';
            
            $counter++;    
        }

        $oscTemplate->addBlock($data, $this->group);
        $oscTemplate->addBlock($_JS, 'footer_scripts');    
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_ADMIN_BOXES_NAVIGATION_MENU_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Information Module', 'MODULE_ADMIN_BOXES_NAVIGATION_MENU_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_ADMIN_BOXES_NAVIGATION_MENU_CONTENT_PLACEMENT', 'Left Column', 'Should the module be loaded in the left or right column?', '6', '1', 'tep_cfg_select_option(array(\'Left Column\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ADMIN_BOXES_NAVIGATION_MENU_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_ADMIN_BOXES_NAVIGATION_MENU_STATUS', 'MODULE_ADMIN_BOXES_NAVIGATION_MENU_CONTENT_PLACEMENT', 'MODULE_ADMIN_BOXES_NAVIGATION_MENU_SORT_ORDER');
    }
  }
