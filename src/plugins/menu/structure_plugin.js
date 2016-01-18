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
			ret_val += '</div>';
			
			templateEngine.bindActionForLoadingFinished(function () {
				buildMenu( $p, path );
			});
			
			//for mobile devices hide navbarLeft on scrolltopage
			if (window.innerWidth <= templateEngine.maxMobileScreenWidth){
				$(window).bind('scrolltopage', function( $p, path ){
					$('#id_left_navbar').hide();
					$('#navbarLeft').hide();
				});
				
				var navbarLeft = document.getElementById('navbarLeft')
				swipedetect(navbarLeft, function(swipedir){
					if (swipedir =='left'){
						$('#id_left_navbar').hide("slide", { direction: "left" }, 200);
						$('#navbarLeft').fadeOut(400);
					}
				})
				
				// var pages = document.getElementById('pages')
				// swipedetect(pages, function(swipedir){
					// if (swipedir =='right'){
						// $('#navbarLeft').show();
						// $('#id_left_navbar').show("slide", { direction: "left" }, 200);
					// }
				// })
			}
      
			return ret_val;
    },
		downaction: function( path, actor, isCanceled ) {
			if (!$(actor).parent().hasClass("info")) {
				actor.classList.remove('switchUnpressed');
				actor.classList.add('switchPressed');
				//design.basicdesign.defaultButtonDownAnimationInheritAction( path, actor );
			}
		},
		action: function( path, actor, isCanceled ) {
			if (!$(actor).parent().hasClass("info")) {
				actor.classList.remove('switchPressed');
				actor.classList.add('switchUnpressed');
				//templateEngine.design.basicdesign.defaultButtonUpAnimationInheritAction( path, actor );
			}
			if( isCanceled ) return;
			
			//click on menubutton
			if($(actor).hasClass('logo')){
				if (window.innerWidth <= templateEngine.maxMobileScreenWidth){
					$('#id_left_navbar').hide("slide", { direction: "left" }, 200);
					$('#navbarLeft').fadeOut(200);
				}
			} else {
				// $('.menuheader').each( function(){
					// var $menuheader = $(this);
					// $menuheader.removeClass('selected');
				// });
				
				$('.menuitem.selected').removeClass('selected');
				
				//click on menuitem level1
				if($(actor).parent().hasClass('level1')){
					var $menuitemlevel1 = $(actor).parent();
					var $submenu = $(actor).parent().parent().children('.submenu');

					$menuitemlevel1.addClass('selected');
					
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
					
					$menuitemlevel2.addClass('selected');
					$submenu.show();
					
					templateEngine.scrollToPage($page.attr('id'));
				}
				
				//click on menuitem tabs
				if($(actor).parent().hasClass('tabs')){
					var $submenu = $(actor).parent().parent();
					var $menuitemlevel2 = $(actor).parent();
					var $menuitemlevel1 = $(actor).parent().parent().parent().children('.menuitem.level1');
					var $page = $('#id_' + $menuitemlevel2.attr('id').substring($menuitemlevel2.attr('id').indexOf('_') + 1, $menuitemlevel2.attr('id').length) + '_');
					var $page_tabs = $('#id_' + $menuitemlevel2.attr('id').substring($menuitemlevel2.attr('id').indexOf('_') + 1, $menuitemlevel2.attr('id').length) + '_1_');
					
					$menuitemlevel2.addClass('selected');
					$submenu.show();
					
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
			nav += '<img src=\'icon/CometVisu-Icon_v4_white.png\' width=\'36px\' height=\'36px\'/>';
			nav += '&nbsp; CometVisu &nbsp; ';
			nav += '<img src=\'config/media/material/ic_chevron_left_black_24dp_2x.png\' width=\'24px\' height=\'24px\' style=\'float:right; margin-top:12px\'/>';
			nav += '</div>';
			nav += '</div>';
			nav += '</div>';
			nav += '<hr/>';
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
				if (name.indexOf(']') > 0){
					name = name.substring(name.lastIndexOf(']') + 1, name.length);
				}
				
				if ($('#id_' + i + '_ h1').text().indexOf('[Settings]') == -1) {
					
					if (window.innerWidth <= templateEngine.maxMobileScreenWidth) {
						if ($('#id_' + i + '_ h1').text().indexOf('[MobileTabs') >= 0){
							nav += '<div class="menucontainer" id="menucontainerid_'+ i + '">';
							
							var parentPage = templateEngine.getParentPage($('.page.activePage'));
							var parentId = null;
							
							if (parentPage != null){
								parentId = parentPage.attr('id');
							}
							
							if (parentId == 'id_' + i + '_'){
								nav += '<div class="menuitem level1 tabs active" id="menuitemid_'+ i + '">';
							} else {
								nav += '<div class="menuitem level1 tabs" id="menuitemid_'+ i + '">';
							}
							nav += '<div class="actor switchUnpressed">';
							nav += '<div class="value">' + name + '</div>';
							nav += '</div>';
							nav += '</div>';
							
						} else if ($('#id_' + i + '_ h1').text().indexOf('[Desktop') == -1 && $('#id_' + i + '_ h1').text().indexOf('[Mobile') == -1){
							nav += '<div class="menucontainer" id="menucontainerid_'+ i + '">';
							
							var pageId = $('.page.activePage').attr('id');
							
							if (pageId == 'id_' + i + '_'){
								nav += '<div class="menuitem level1 active" id="menuitemid_'+ i + '">';
							} else {
								nav += '<div class="menuitem level1" id="menuitemid_'+ i + '">';
							}
							nav += '<div class="actor switchUnpressed">';
							nav += '<div class="value">' + name + '</div>';
							nav += '</div>';
							nav += '</div>';
						}
					} else {
						if ($('#id_' + i + '_ h1').text().indexOf('[Mobile') == -1){
							nav += '<div class="menucontainer" id="menucontainerid_'+ i + '">';
							
							var pageId = $('.page.activePage').attr('id');
							
							if (pageId == 'id_' + i + '_'){
								nav += '<div class="menuitem level1 active" id="menuitemid_'+ i + '">';
							} else {
								nav += '<div class="menuitem level1" id="menuitemid_'+ i + '">';
							}
							
							nav += '<div class="actor switchUnpressed">';
							nav += '<div class="value">' + name + '</div>';
							nav += '</div>';
							nav += '</div>';
						}
					}
					
					var parentPage = templateEngine.getParentPage($('.page.activePage'));
					var parentId = null;
							
					if (parentPage != null){
						parentId = parentPage.attr('id');
					}
			
					if ($('#' + parentId + ' h1').text().indexOf('[MobileTabs') > -1){
						parentPage = templateEngine.getParentPage(parentPage);
					}
					
					if (parentPage != null){
						parentId = parentPage.attr('id');
					}
			
					if (parentId == 'id_' + i + '_'){
						nav += '<div class="submenu" id="submenuid_'+ i + '">';
					} else {
						nav += '<div class="submenu" id="submenuid_'+ i + '" style="display:none">';
					}

					for ( var j = 0; j < 20; j++) {
						if ($('#id_' + i + '_' + j + '_').hasClass('page')) {
							name = $('#id_' + i + '_' + j + '_ h1').text();
							if (name.indexOf(']') > 0){
								name = name.substring(name.lastIndexOf(']') + 1, name.length);
							}
							if (window.innerWidth <= templateEngine.maxMobileScreenWidth){
								if ($('#id_' + i + '_' + j + '_1_').hasClass('page')) {
									var parentPage = templateEngine.getParentPage($('.page.activePage'));
									var parentId = null;
							
									if (parentPage != null){
										parentId = parentPage.attr('id');
									}
							
									if (parentId == 'id_' + i + '_' + j + '_'){
										nav += '<div class="menuitem level2 tabs active" id="menuitemid_' + i + '_' + j + '">';
									} else {
										nav += '<div class="menuitem level2 tabs" id="menuitemid_' + i + '_' + j + '">';
									}
									
									nav += '<div class="actor switchUnpressed">';
									nav += '<div class="value">' + name + '</div>';
									nav += '</div>';
									nav += '</div>';
								} else if ($('#id_' + i + '_' + j + '_ h1').text().indexOf('[Desktop') == -1 && $('#id_' + i + '_ h1').text().indexOf('[MobileTabs') == -1){
									var pageId = $('.page.activePage').attr('id');
							
									if (pageId == 'id_' + i + '_' + j + '_'){
										nav += '<div class="menuitem level2 active" id="menuitemid_' + i + '_' + j + '">';
									} else {
										nav += '<div class="menuitem level2" id="menuitemid_' + i + '_' + j + '">';
									}
									nav += '<div class="actor switchUnpressed">';
									nav += '<div class="value">' + name + '</div>';
									nav += '</div>';
									nav += '</div>';
								}
							} else {
								if ($('#id_' + i + '_' + j + '_ h1').text().indexOf('[Mobile') == -1){
									var pageId = $('.page.activePage').attr('id');
							
									if (pageId == 'id_' + i + '_' + j + '_'){
										nav += '<div class="menuitem level2 active" id="menuitemid_' + i + '_' + j + '">';
									} else {
										nav += '<div class="menuitem level2" id="menuitemid_' + i + '_' + j + '">';
									}
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
						if ($('#id_' + i + '_ h1').text().indexOf('[Desktop') == -1){
							nav += '</div>';
						}
					} else {
						if ($('#id_' + i + '_ h1').text().indexOf('[Mobile') == -1){
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
	
	function swipedetect(el, callback){
  
    var touchsurface = el,
    swipedir,
    startX,
    startY,
    distX,
    distY,
    threshold = 150, //required min distance traveled to be considered swipe
    restraint = 100, // maximum distance allowed at the same time in perpendicular direction
    allowedTime = 1000, // maximum time allowed to travel that distance
    elapsedTime,
    startTime,
    handleswipe = callback || function(swipedir){}
  
    touchsurface.addEventListener('touchstart', function(e){
        var touchobj = e.changedTouches[0]
        swipedir = 'none'
        distX = 0
				distY = 0
        startX = touchobj.pageX
        startY = touchobj.pageY
        startTime = new Date().getTime() // record time when finger first makes contact with surface
        e.preventDefault()
    }, false)
  
    touchsurface.addEventListener('touchmove', function(e){
        e.preventDefault() // prevent scrolling when inside DIV
    }, false)
  
    touchsurface.addEventListener('touchend', function(e){
        var touchobj = e.changedTouches[0]
        distX = touchobj.pageX - startX // get horizontal dist traveled by finger while in contact with surface
        distY = touchobj.pageY - startY // get vertical dist traveled by finger while in contact with surface
        elapsedTime = new Date().getTime() - startTime // get time elapsed
        if (elapsedTime <= allowedTime){ // first condition for awipe met
            if (Math.abs(distX) >= threshold && Math.abs(distY) <= restraint){ // 2nd condition for horizontal swipe met
                swipedir = (distX < 0)? 'left' : 'right' // if dist traveled is negative, it indicates left swipe
            }
            else if (Math.abs(distY) >= threshold && Math.abs(distX) <= restraint){ // 2nd condition for vertical swipe met
                swipedir = (distY < 0)? 'up' : 'down' // if dist traveled is negative, it indicates up swipe
            }
        }
        handleswipe(swipedir)
        e.preventDefault()
    }, false)
	}
	
})();

$(window).bind('scrolltopage', function( event, page_id ){
	var page = $('#' + page_id);
	
	$('.menuitem.active').removeClass('active');
	
	// and set the new active ones
	$('.menuitem').each( function(){
		var $menuitem = $(this);
		var parentPage = templateEngine.getParentPage(page);
		var parentId = parentPage.attr('id');
		
		if ($menuitem.hasClass('tabs') && 'menuitem' + parentId == $menuitem.attr('id') + '_'){
			$menuitem.addClass('active');
		} else if( 'menuitem' + page_id == $menuitem.attr('id') + '_' ){
			$menuitem.addClass('active');
		}
	});
});

});