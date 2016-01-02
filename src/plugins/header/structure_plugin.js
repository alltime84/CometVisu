/* header plugin (c) 2015 by Tim Russland [tim.russland@gmail.com]
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

define( ['structure_custom', 'css!plugins/header/header' ], function( VisuDesign_Custom ) {
  "use strict";

(function() {
  VisuDesign_Custom.prototype.addCreator("header", {
    create : function(page, path) {
      var $p = $(page);
      var header_id = "header" + uniqid();

      var classes = 'header';
      if ($p.attr('class')) {
        classes += ' custom_'+$p.attr('class');
      }
      //classes += templateEngine.design.setWidgetLayout( $p, path );
      var ret_val = '<div id="header">';
			ret_val += '<div class="'+ classes + '"></div>';
			ret_val += '</div>'
			
			templateEngine.bindActionForLoadingFinished(function () {
					refreshHeader( page, path );
			});
			
			// var resizeTimer;
			
			// $(window).bind('resize', function( page, path ){
				// clearTimeout(resizeTimer);
				// resizeTimer = setTimeout(function() {

					// refreshHeader( page, path );
									
				// }, 250);
				
			// });
			
			$(window).bind('scrolltopage', function( page, path ){
				refreshHeader( page, path );
			});

      return ret_val;
    },
		
		action: function( path, actor, isCanceled ) {
			$('#navbarLeft').show("slide", { direction: "left" }, 200);
		}

  });
	
	function refreshHeader( page, path ) {
		var path_array = path.split('_');
		var id = 'id_'; // path_array[0];
								
		var active_page = '';
		var link = '';
		var link_id = '';
		
		for ( var i = 1; i < path_array.length; i++) { // element 0 is id_ (JNK)
			id += path_array[i] + '_';
			if ($('#' + id ).hasClass("page")) { // FIXME is this still needed?!?
				if ($('#' + id + ' h1').text().indexOf('[Menu]') > -1){
					link_id = id;
				}
				if ($('#' + id + ' h1').text().indexOf('Header]') > -1 || $('#' + id + ' h1').text().indexOf('Tabs]') > -1){
					active_page = $('#' + id + ' h1').text();
				} else if ($('#' + id + ' h1').text().indexOf('[Menu]') > -1){
					active_page = $('#' + id + ' h1').text();
				}
			}
		}

		if (active_page.indexOf('[Menu]') > -1){
			active_page = active_page.substring(active_page.lastIndexOf(']') + 1, active_page.length);
		} else if (active_page.indexOf('Header]') > -1 || active_page.indexOf('Tabs]') > -1){
			active_page = active_page.substring(active_page.lastIndexOf(']') + 1, active_page.length);
		} else {
			active_page = 'CometVisu';
		}
		
		//link = '<a href="javascript:templateEngine.scrollToPage(\'id_2_\')"><img src=\'icon/ic_menu_white_24dp_1x.png\'/></a>';
		//for mobile devices hide navbarLeft on scrolltopage
		if (window.innerWidth <= 1000){
			link += '<div class="actor switchUnpressed">';
			link += '<div class="value"><img src=\'icon/ic_menu_white_24dp_1x.png\'/></div>';
			link += '</div>';
		}
		
		link += '<div class="header title">';
		link += active_page;
		link += '</div>';
		
		$('#header .header').html(link);
		return false;
	};

  var internalCounter = 0;
  function uniqid() {
    return internalCounter++;
  }
	
})();

});