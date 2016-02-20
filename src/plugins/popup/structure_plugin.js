/* structure_plugin.js (c) 
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

/**
 * <popup>
 *  <popuptrigger>
 * 
 *  </popuptrigger>
 *  <popupcontainer>
 *  
 *  </popupcontainer>
 * </popup>
 */

define( ['structure_custom', 'css!plugins/popup/popup.css'  ], function( VisuDesign_Custom ) {
  "use strict";
 
  VisuDesign_Custom.prototype.addCreator("popup", {
      create: function(element, path, flavour, type) {
        var $e = $(element);
        
        //widgets in popup
        var childWidgets = $e.find("popupcontainer").children().not('layout');
        var containerClasses = 'clearfix popupcontent actor' + VisuDesign_Custom.prototype.setWidgetLayout( $e, path + '_1' );
        
        var container = '<div class="' + containerClasses + '" id="' + path + '_1" data-type="text" style="display:none">';
        
        $( childWidgets ).each( function(i){
          container += templateEngine.create_pages( childWidgets[i], path + '_1_' + i, flavour );
        } );

        container += '</div>';
        $('#popupcontainer').append(container);
        
        //widgets in trigger
        var classes = 'clearfix popupTrigger actor' + VisuDesign_Custom.prototype.setWidgetLayout( $e, path );
        var childTriggerWidgets = $e.find("popuptrigger").children().not('layout');
        var triggerContainer = '<div class="clearfix">';                         
        $( childTriggerWidgets ).each( function(i){
          triggerContainer += templateEngine.create_pages( childTriggerWidgets[i], path + '_0_' + i, flavour );
        } );
        triggerContainer += '</div>';
        
        return '<div class="' + classes + '" style="width: 100%;">' + triggerContainer + '</div>';
      },
      action: function( path, actor, isCanceled ) {
        $('#popupcontainer .active').hide();
        $('#popupcontainer .active').removeClass('active');
        
        $('#popupcontainer #' + path + '_1').addClass('active');
        $('#popupcontainer #' + path + '_1').show();
        $('#popupcontainer').fadeIn(600);
        $('#popup').show("clip", 10);
      } 
    });
});
