/* menu plugin (c) 2015 by Tim Russland [tim.russland@gmail.com]
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 3 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA
 */

define( ['structure_custom', 'css!plugins/menu/menu' ], function( VisuDesign_Custom ) {
  "use strict";

(function() {
  VisuDesign_Custom.prototype.addCreator("menu", {
    create : function(page, path) {
      var $p = $(page);
      var sidemenu_id = "menu" + uniqid();

      var classes = 'widget clearfix menu';
      if ($p.attr('class')) {
        classes += ' custom_'+$p.attr('class');
      }
      //classes += templateEngine.design.setWidgetLayout( $p, path );
			
      var ret_val = '<div id="menu">';
			ret_val += '<div class="'+ classes + '"></div>';
			ret_val += '</div>'
			
			templateEngine.bindActionForLoadingFinished(function () {
				buildMenu( page, path );
			});
			
			$(window).bind('scrolltopage', function( page, path ){
				//buildMenu( page, path );
				
				//for mobile devices hide navbarLeft on scrolltopage
				if (window.innerWidth <= templateEngine.maxMobileScreenWidth){
					$('#navbarLeft').hide();
				}
			});	
      
			return ret_val;
    },
		downaction: function( path, actor, isCanceled ) {
			if (!$(actor).parent().hasClass("info")) {
				templateEngine.design.defaultButtonDownAnimationInheritAction( path, actor );
			}
		},
		action: function( path, actor, isCanceled ) {
			if( isCanceled ) return;
			
			//click on menubutton
			if($(actor).hasClass('logo')){
				if (window.innerWidth <= templateEngine.maxMobileScreenWidth){
					$('#navbarLeft').hide();
				}
			} else {
				$('.menuheader').each( function(){
					var $menuheader = $(this);
					$menuheader.removeClass('active');
				});
				
				$('.menuitem').each( function(){
					var $menuitem = $(this);
					$menuitem.removeClass('active');
				});
				
				//click on menuitem level1
				if($(actor).parent().hasClass('level1')){
					var $menuitemlevel1 = $(actor).parent();
					var $submenu = $(actor).parent().parent().children('.submenu');

					$menuitemlevel1.addClass('active');
					
					if (!$submenu.children().length > 0) {
						if($(actor).parent().hasClass('tabs')){
							templateEngine.scrollToPage('id_' + $menuitemlevel1.attr('id').substring($menuitemlevel1.attr('id').indexOf('_') + 1, $menuitemlevel1.attr('id').length) + '_1_');
						} else {
							templateEngine.scrollToPage('id_' + $menuitemlevel1.attr('id').substring($menuitemlevel1.attr('id').indexOf('_') + 1, $menuitemlevel1.attr('id').length) + '_');
						}
					} else {
						if($submenu.is(":visible")){
							$submenu.hide("slide", { direction: "up" }, 200);
						} else {
							$('.submenu').each( function(){
								var $currentsubmenu = $(this);
								$currentsubmenu.hide();
							});
							$submenu.show("slide", { direction: "up" }, 200);
						}
					}
				}
				
				//click on menuitem level2
				if($(actor).parent().hasClass('level2') && !$(actor).parent().hasClass('tabs')){
					var $submenu = $(actor).parent().parent();
					var $menuitemlevel2 = $(actor).parent();
					var $menuitemlevel1 = $(actor).parent().parent().parent().children('.menuitem.level1');
					var $page = $('#id_' + $menuitemlevel2.attr('id').substring($menuitemlevel2.attr('id').indexOf('_') + 1, $menuitemlevel2.attr('id').length) + '_');
					
					$menuitemlevel2.addClass('active');
					$submenu.show();
					$menuitemlevel1.addClass('active');
					
					templateEngine.scrollToPage($page.attr('id'));
				}
				
				//click on menuitem tabs
				if($(actor).parent().hasClass('tabs')){
					var $submenu = $(actor).parent().parent();
					var $menuitemlevel2 = $(actor).parent();
					var $menuitemlevel1 = $(actor).parent().parent().parent().children('.menuitem.level1');
					var $page = $('#id_' + $menuitemlevel2.attr('id').substring($menuitemlevel2.attr('id').indexOf('_') + 1, $menuitemlevel2.attr('id').length) + '_');
					var $page_tabs = $('#id_' + $menuitemlevel2.attr('id').substring($menuitemlevel2.attr('id').indexOf('_') + 1, $menuitemlevel2.attr('id').length) + '_1_');
					
					$menuitemlevel2.addClass('active');
					$submenu.show();
					$menuitemlevel1.addClass('active');
					
					templateEngine.scrollToPage($page_tabs.attr('id'));
				}
			}
		}
  });
	
	function buildMenu( page, path ) {
		var path_array = path.split('_');
		var id = 'id_'; // path[0];
		var nav = '';
		var name = '';
		
		if (window.innerWidth <= templateEngine.maxMobileScreenWidth) {
		nav = '<div class="logo">';
		nav += '<div class="actor switchUnpressed logo">';
		nav += '<div class="value" align="Left">';
		nav += '<img src=\'icon/CometVisu-Icon_v4_white.png\' width=\'72px\' height=\'72px\'/>';
		nav += '&nbsp; CometVisu &nbsp; ';
		nav += '<img src=\'config/media/material/ic_arrow_back_white_24dp_2x.png\' width=\'24px\' height=\'24px\'/>';
		nav += '</div>';
		nav += '</div>';
		nav += '</div>';
		}	else{
			nav = '<div class="logo">';
			nav += '<div class="actor switchUnpressed logo">';
			nav += '<div class="value" align="Left">';
			nav += '<img src=\'icon/CometVisu-Icon_v4_white.png\' width=\'36px\' height=\'36px\'/>';
			nav += '&nbsp; CometVisu &nbsp; ';
			nav += '</div>';
			nav += '</div>';
			nav += '</div>';
		}

		for ( var i = 0; i < 10; i++) {
			if ($('#id_' + i + '_').hasClass("page")) {
				name = $('#id_' + i + '_ h1').text();
				name = name.substring(name.lastIndexOf(']') + 1, name.length);
				
				if ($('#id_' + i + '_ h1').text().indexOf('[Settings]') == -1) {
					
					if (window.innerWidth <= templateEngine.maxMobileScreenWidth) {
						if ($('#id_' + i + '_ h1').text().indexOf('[MobileTabs') >= 0){
							nav += '<div class="menucontainer" id="menucontainerid_'+ i + '">';
							nav += '<div class="menuitem level1 tabs" id="menuitemid_'+ i + '">';
							nav += '<div class="actor switchUnpressed">';
							nav += '<div class="value">' + name + '</div>';
							nav += '</div>';
							nav += '</div>';
						} else if ($('#id_' + i + '_ h1').text().indexOf('[Menu') >= 0){
							nav += '<div class="menucontainer" id="menucontainerid_'+ i + '">';
							nav += '<div class="menuitem level1" id="menuitemid_'+ i + '">';
							nav += '<div class="actor switchUnpressed">';
							nav += '<div class="value">' + name + '</div>';
							nav += '</div>';
							nav += '</div>';
						} else if ($('#id_' + i + '_ h1').text().indexOf('[MobileHeader') >= 0){
							nav += '<div class="menucontainer" id="menucontainerid_'+ i + '">';
							nav += '<div class="menuitem level1" id="menuitemid_'+ i + '">';
							nav += '<div class="actor switchUnpressed">';
							nav += '<div class="value">' + name + '</div>';
							nav += '</div>';
							nav += '</div>';
						}
					} else {
						if ($('#id_' + i + '_ h1').text().indexOf('[Menu') >= 0 || $('#id_' + i + '_ h1').text().indexOf('[DesktopHeader') >= 0){
							nav += '<div class="menucontainer" id="menucontainerid_'+ i + '">';
							nav += '<div class="menuitem level1" id="menuitemid_'+ i + '">';
							nav += '<div class="actor switchUnpressed">';
							nav += '<div class="value">' + name + '</div>';
							nav += '</div>';
							nav += '</div>';
						}
					}
					
					nav += '<div class="submenu" id="submenuid_'+ i + '" style="display:none">';
					
					for ( var j = 0; j < 20; j++) {
						if ($('#id_' + i + '_' + j + '_').hasClass('page')) {
							if (window.innerWidth <= templateEngine.maxMobileScreenWidth){
								if ($('#id_' + i + '_' + j + '_ h1').text().indexOf('[MobileHeader]') >= 0) {
									name = $('#id_' + i + '_' + j + '_ h1').text();
									name = name.substring(name.lastIndexOf(']') + 1, name.length);
									nav += '<div class="menuitem level2" id="menuitemid_' + i + '_' + j + '">';
									nav += '<div class="actor switchUnpressed">';
									nav += '<div class="value">' + name + '</div>';
									nav += '</div>';
									nav += '</div>';
								}
								
								if ($('#id_' + i + '_' + j + '_ h1').text().indexOf('[MobileTabs]') >= 0) {
									name = $('#id_' + i + '_' + j + '_ h1').text();
									name = name.substring(name.lastIndexOf(']') + 1, name.length);
									nav += '<div class="menuitem level2 tabs" id="menuitemid_' + i + '_' + j + '">';
									nav += '<div class="actor switchUnpressed">';
									nav += '<div class="value">' + name + '</div>';
									nav += '</div>';
									nav += '</div>';
								}
							} else {
								if ($('#id_' + i + '_' + j + '_ h1').text().indexOf('[DesktopHeader]') >= 0) {
									name = $('#id_' + i + '_' + j + '_ h1').text();
									name = name.substring(name.lastIndexOf(']') + 1, name.length);
									nav += '<div class="menuitem level2" id="menuitemid_' + i + '_' + j + '">';
									nav += '<div class="actor switchUnpressed">';
									nav += '<div class="value">' + name + '</div>';
									nav += '</div>';
									nav += '</div>';
								}
								
								
							}
						}
					}
					
					nav += '</div>';
					
					if (window.innerWidth <= templateEngine.maxMobileScreenWidth) {
						if ($('#id_' + i + '_ h1').text().indexOf('[MobileTabs') >= 0){
							nav += '</div>';
						} else if ($('#id_' + i + '_ h1').text().indexOf('[Menu') >= 0){
							nav += '</div>';
						}
					} else {
						if ($('#id_' + i + '_ h1').text().indexOf('[Menu') >= 0 || $('#id_' + i + '_ h1').text().indexOf('[DesktopHeader') >= 0){
							nav += '</div>';
						}
					}
				}
			}
		}
		
		$('#menu .menu').html(nav);
		
		return false;
	};

  var internalCounter = 0;
  function uniqid() {
    return internalCounter++;
  }
	
})();

});