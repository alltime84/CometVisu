/* group.js (c) 2012 by Christian Mayer [CometVisu at ChristianMayer dot de]
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

define( ['_common'], function( design ) {
  var basicdesign = design.basicdesign;
  
design.basicdesign.addCreator('group', {
  maturity: design.Maturity.development,
  create: function( element, path, flavour, type ) {
    var $e = $(element);
    var classes = 'clearfix group ' + basicdesign.setWidgetLayout( $e, path );
    if ( $e.attr('class') ) {
      classes += ' custom_' + $e.attr( 'class' );
    }
    if ($e.attr('nowidget')!=='true') classes = 'widget ' + classes;
    if( $e.attr('flavour') ) flavour = $e.attr('flavour');// sub design choice
    if( flavour ) classes += ' flavour_' + flavour;
    var hstyle  = '';                                     // heading style
    if( $e.attr('align') ) hstyle += 'text-align:' + $e.attr('align') + ';';
    if( hstyle != '' ) hstyle = 'style="' + hstyle + '"';
    var childs = $e.children().not('layout');
    var container = $( '<div class="clearfix"/>' );
    if( $e.attr('name') ) container.append( '<h2 ' + hstyle + '>' + $e.attr('name') + '</h2>' );
                              
    var collector = '';
    $( childs ).each( function(i){
      var subelement = templateEngine.create_pages( childs[i], path + '_' + i, flavour );
      if( 'string' === typeof subelement )
        collector += subelement;
      else
      {
        if( '' !== collector )
          container.append( collector );
        container.append( subelement );
        collector = '';
      }
    } );
    if( '' !== collector )
      container.append( collector );

    var ret_val = $('<div class="' + classes + '" />');
    if ( $e.attr('target') )  {
      var target = $e.attr('target') ? $e.attr('target') : '0';
      ret_val.addClass('clickable');
      var data = templateEngine.widgetDataInsert( path, {
        'target'  : target
      } );
      templateEngine.setWidgetStyling(ret_val, target, data.styling );
    }

    ret_val.append( container );
    return ret_val;
  },
  action: function( path, actor, isCaneled ) {
    var data = templateEngine.widgetDataGet( path );
    if (data.target != 0) templateEngine.scrollToPage( data.target );
  } 
});

}); // end define