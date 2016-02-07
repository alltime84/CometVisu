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
 * 	<popuptrigger>
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

        // create the main structure
        var ret_val = templateEngine.design.createDefaultWidget('popup', $e, path, flavour, type);
        // and fill in widget specific data
        var data = templateEngine.widgetDataInsert( path, {
          content           : getWidgetElements($e, path)
        } );
        ret_val += data.content;
        
        return ret_val + '</div>';
      },
      action: function( path, actor, isCanceled ) {
        
        if($(actor).hasClass("popupcontainer")){
          $(actor).hide();
        } else {
          $(actor).parent().children('.popupcontainer').show();
        }
      } 
    });
   
    function getWidgetElements(xmlElement, path, flavour, type) {
      var popuptrigger = $('popuptrigger > *', xmlElement).first()[0];
      var popupcontainer = $('popupcontainer > *', xmlElement).first()[0];
      var data = templateEngine.widgetDataInsert( path+"_0", {
        containerClass           : "actor"
      } );
      var ret_val = templateEngine.create_pages(popuptrigger, path+"_0", flavour, popuptrigger.nodeName);
      
      var childs = $(xmlElement).find("popupcontainer").children().not('layout');
      var container = '<div class="popupcontainer actor" id="' + path + '_1" data-type="text" style="display:none">';
      
      $( childs ).each( function(i){
        container += templateEngine.create_pages( childs[i], path + '_1_' + i, flavour );
      } );
      container += '</div>';
      
      ret_val += container;
      return ret_val;
    }
});
