/* imagetrigger.js (c) 2012 by Christian Mayer [CometVisu at ChristianMayer dot de]
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

basicdesign.addCreator('imagetrigger', {
  create: function( element, path, flavour, type ) { 
    var $e = $(element);
    var ret_val = $('<div class="widget clearfix image" />');
    ret_val.setWidgetLayout($e);
    ret_val.addClass ('imagetrigger');
    var value = $e.attr('value') ? $e.attr('value') : 0;
    ret_val.append( extractLabel( $e.find('label')[0] ) );
    var address = makeAddressList($e);
    var layout = $e.children('layout')[0];
    var style = layout ? 'style="' + extractLayout( layout, type, {width:'100%'} ) + '"' : '';

    var actor = '<div class="actor">';
    if ( $e.attr('type')=='show' )
      actor += '<img src="' + $e.attr('src') + '.' + $e.attr('suffix') + '" ' + style + ' />';
    else
      actor += '<img src="" ' + style + ' />';
    actor += '</div>';
        
    actor += '</div>';
    var refresh = $e.attr('refresh') ? $e.attr('refresh')*1000 : 0;
    var $actor = $(actor).data( {
      'address':   address, 
      'refresh':   refresh,
      'src':       $e.attr('src'),
      'suffix':    $e.attr('suffix'),
      'type':      $e.attr('type'),
      'mapping':   map,
      'sendValue': $e.attr('sendValue') || ""
    } )
      .each(setupRefreshAction) // abuse "each" to call in context... refresh is broken with select right now
      .bind( 'click', this.action );
    for( var addr in address ) {
      $actor.bind( addr, this.update );
    }
    ret_val.append( $actor );
    return ret_val;
  },
  update: function(e,d) {
    var data = $(this).data();
    if ( data.address[e.type][1].writeonly == "true")
      return; // skip writeonly FIXME: writeonly shouldnt bind to update at all
    var val = transformDecode(data.address[e.type][0], d);
    if (data.type == "show")
      if (val == 0)
        $(this).children().hide();
      else
        $(this).children().attr("src", data.src + '.' + data.suffix ).show();
    else if (data.type == "select")
      if (val == 0)
        $(this).children().hide();
      else
        $(this).children().attr("src", data.src + val + '.' + data.suffix ).show();
        
    //FIXME: add value if mapping exists 
    //FIXME: get image name from mapping
    //FIXME: add bitmask for multiple images
    //FIXME: add SVG-magics
  },
  action: function() {
    var data = $(this).data();
    sendValue = data.sendValue;
    for( var addr in data.address ) {
      if( !(data.address[addr][1] & 2) )
        continue; // skip when write flag not set
      if( data.sendValue == "" )
        continue; // skip empty
      visu.write( addr.substr(1), transformEncode( data.address[addr][0], sendValue ) );
    }
  },
  attributes: {
    src:        { type: 'uri'    , required: true  },
    refresh:    { type: 'numeric', required: false },
    colspan:    { type: 'numeric', required: false },
    rowspan:    { type: 'numeric', required: false },
    sendValue:  { type: 'numeric', required: false },
    type:       { type: 'list'   , required: true , list: {'show': 'show', 'select': 'select' /* , 'bitmask': 'bitmask' */ } },
    mapping:    { type: 'mapping', required: false },
    suffix:     { type: 'list'   , required: false, list: {'png': '.png', 'jpg': '.jpg', 'gif': '.gif', 'svg': '.svg', 'bmp': '.bmp'} }
  },
  elements: {
    layout:     { type: 'layout' , required: false, multi: false },
    label:      { type: 'string' , required: false, multi: false },
    address:    { type: 'address', required: true , multi: true  }
  },
  content:      false
});