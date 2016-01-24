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
    create : function(element, path, flavour, type) {
      var $e = $(element);
      var $showtitle = true;
      
      var header_id = "header" + uniqid();

      var classes = 'header';
      if ($e.attr('class')) {
        classes += ' custom_'+$e.attr('class');
      }
      
      if ($e.attr('showtitle') == 'false'){
        $showtitle = false;
      }
      
      //classes += templateEngine.design.setWidgetLayout( $p, path );
      var ret_val = '<div id="header">';
      ret_val += '<div class="'+ classes + '"></div>';
      ret_val += '</div>'
      
      createHeader( $e, path, $showtitle );
      
      $(window).bind('scrolltopage', function( $e, path, $showtitle ){
        createHeader( $e, path, $showtitle );
      });

      return ret_val;
    },
    
    action: function( path, actor, isCanceled ) {
      if( isCanceled ) return;
      $('#navbarLeft').show();
      $('#id_left_navbar').show("slide", { direction: "left" }, 200);
    }

  });
  
  function createHeader( element, path, showtitle ) {
    var $e = $(element);
    var path_array = path.split('_');
    var id = 'id_'; // path_array[0];
                
    var active_page = '';
    var header = '';
    
    for ( var i = 1; i < path_array.length; i++) { // element 0 is id_ (JNK)
      id += path_array[i] + '_';
      if ($('#' + id ).hasClass("page")) { // FIXME is this still needed?!?
        if ($('#' + id + ' h1').text().indexOf('Header]') > -1 || $('#' + id + ' h1').text().indexOf('Tabs]') > -1){
          active_page = $('#' + id + ' h1').text();
        }
      }
    }

    if (active_page.indexOf(']') > -1){
      active_page = active_page.substring(active_page.lastIndexOf(']') + 1, active_page.length);
    }
    
    //link = '<a href="javascript:templateEngine.scrollToPage(\'id_2_\')"><img src=\'icon/ic_menu_white_24dp_1x.png\'/></a>';
    //for mobile devices hide navbarLeft on scrolltopage
    if (window.innerWidth <= templateEngine.maxMobileScreenWidth){
      header += '<div class="actor switchUnpressed">';
      header += '<div class="value"><img src=\'designs/material/images/ic_menu_white_24dp_2x.png\'/></div>';
      header += '</div>';
    }
    
    //todo: hide if showheader=false
    header += '<div class="header title">';
    header += active_page;
    header += '</div>';
    
    $('#header .header').html(header);
    return false;
  };

  var internalCounter = 0;
  function uniqid() {
    return internalCounter++;
  }
  
})();

});