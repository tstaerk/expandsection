<?php
/**
* Expandsection Plugin
*
* @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
* @author Thorsten Staerk <dev@staerk.de>
*
* A plugin for dokuwiki that allows for expanding and collapsing text sections.
*/
 
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
 
/**
* All DokuWiki plugins to extend the parser/rendering mechanism
* need to inherit from this class
*/
class syntax_plugin_expandsection_expand extends DokuWiki_Syntax_Plugin
{

  function getType() 
  {
  // source: http://github.com/splitbrain/dokuwiki/blob/master/inc/parser/parser.php#L12
    return 'formatting';
  }

  function getSort()
  {
  // emphasis has a sort of 80. Set this to 70 and it will be active.
  // Set it to 90 and it will not be active.
    return 1;
  }
  
  function getAllowedTypes()
  {
    return array('formatting', 'substition', 'disabled', 'protected');
  }
  
  function connectTo($mode)
  {
    $this->Lexer->addEntryPattern(
      '<expand>',
      $mode,
      'plugin_expandsection_expand'
    );
  }
  
  function postConnect()
  {
    $this->Lexer->addExitPattern(
      '</expand>',
      'plugin_expandsection_expand'
    );
  }
  
  function handle($match, $state, $pos, &$handler)
  {
    if ($state == DOKU_LEXER_UNMATCHED)
    {
    }
    return array($state, $pos, $match);
  }
  
  function render($mode, &$renderer, $data)
  // expandtext$pos is the text on position $pos that can be expanded and collapsed.
  // expandsign$pos is the link on that you click to expand the text on position $pos. expandsign$pos can typically be a "+" sign.
  {
    GLOBAL $oldpos;
    if($mode == 'xhtml')
    {
      list($state,$pos,$match) = $data;
      switch ($state) 
      {
        case DOKU_LEXER_ENTER : 
          $renderer->doc.="<div id=expandtext$pos style='display:inline'>";    
          $oldpos=$pos;
          break;
        case DOKU_LEXER_UNMATCHED :  $renderer->doc .= $renderer->_xmlEntities($match)."<a href=\"javascript:expand$oldpos(false)\"><b>-</b></a>"; break;
        case DOKU_LEXER_EXIT :       $renderer->doc .= "</div><script language=\"JavaScript\" type=\"text/javascript\">
          <!--
          function expand$oldpos(b) 
          {
            var expandtext = document.getElementById('expandtext$oldpos');
            var expandsign = document.getElementById('expandsign$oldpos');
            if (b) 
            {
              expandtext.style.display = 'inline';
              expandsign.style.display = 'none';
            }
            if (!b) 
            {
              expandtext.style.display = 'none';
              expandsign.style.display = 'inline';
            }
          }
          expand$oldpos(false);
          // -->
          </script>
          <div id=expandsign$oldpos style='display:inline'><a href=\"javascript:expand$oldpos(true)\"><b>+</b></a></div>";break;
      }
      return true;
    }
    return false;
  }
}
