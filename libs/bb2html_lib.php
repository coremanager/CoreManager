<?php
/*
// A simple FAST parser to convert BBCode to HTML
// Trade-in more restrictive grammar for speed and simplicty
//
// (please do not remove credit)
// author: Louai Munajim
// website: http://elouai.com
// date: 2004/Apr/18
*/

/*
// Modified for CoreManager
*/

function bb2html($text)
{
  $bbcode = array("<", ">",
                "[list]", "[*]", "[/list]", 
                "[img]", "[/img]", 
                "[b]", "[/b]", 
                "[u]", "[/u]", 
                "[i]", "[/i]",
                "[strike]", "[/strike]",
                '[color="', "[/color]",
                '[size="', "[/size]",
                '[font="', "[/font]",
                '[url="', "[/url]",
                "[mail=\"", "[/mail]",
                "[code]", "[/code]",
                "[quote]", "[/quote]",
                "[center]", "[/center]",
                ":)",
                ":D",
                ";)",
                "8)",
                ":(",
                ":mad:",
                ":|",
                "=)",
                ":´(",
                ":?",
                ":]",
                ":S",
                ":P",
                ":O",
                ":lol:",
                "]",
                '"]');
  $htmlcode = array("&lt;", "&gt;",
                "<ul>", "<li>", "</ul>", 
                "<img src=\"", "\">", 
                "<b>", "</b>", 
                "<u>", "</u>", 
                "<i>", "</i>",
                "<del>", "</del>",
                "<span style=\"color:", "</span>",
                "<span style=\"font-size:", "</span>",
                "<span style=\"font-family:", "</span>",
                '<a href="', "</a>",
                "<a href=\"mailto:", "</a>",
                "<table width=100% bgcolor=lightgray><tr><td bgcolor=white><code>", "</code></td></tr></table>",
                "<table width=100% bgcolor=CornflowerBlue><tr><td bgcolor=white>", "</td></tr></table>",
                "<center>", "</center>",
                "<img src='img/emoticons/smile.gif' />",
                "<img src='img/emoticons/razz.gif' />",
                "<img src='img/emoticons/wink.gif' />",
                "<img src='img/emoticons/cool.gif' />",
                "<img src='img/emoticons/sad.gif' />",
                "<img src='img/emoticons/angry.gif' />",
                "<img src='img/emoticons/neutral.gif' />",
                "<img src='img/emoticons/happy.gif' />",
                "<img src='img/emoticons/cry.gif' />",
                "<img src='img/emoticons/hmm.gif' />",
                "<img src='img/emoticons/roll.gif' />",
                "<img src='img/emoticons/smm.gif' />",
                "<img src='img/emoticons/tongue.gif' />",
                "<img src='img/emoticons/yikes.gif' />",
                "<img src='img/emoticons/lol.gif' />",
                '>',
                '">');
  $newtext = str_replace($bbcode, $htmlcode, $text);
  $newtext = nl2br($newtext);//second pass
  return $newtext;
}


function bbcode_add_editor()
{
  global $output;
  $bbcode_fonts = bbcode_fonts();
  $bbcode_sizes = bbcode_sizes();
  $bbcode_colors = bbcode_colors();
  $bbcode_emoticons = bbcode_emoticons();
  $output .= '
        <script type="text/javascript" src="libs/js/bbcode.js"></script>
        <div style="display:block">
          <select>
            <option>'.$bbcode_fonts[0].'</option>';
  for ( $i=0; $i<count($bbcode_fonts); $i++ )
  {
    $output .= '
            <option onclick="addbbcode(\'msg\',\'font\',\''.$bbcode_fonts[$i].'\');" style="font-family:\''.$bbcode_fonts[$i].'\';">'.$bbcode_fonts[$i].'</option>';
  }
  $output .= '
          </select>
          <select>
            <option>Size</option>';
  for ( $i=0; $i<count($bbcode_sizes); $i++ )
  {
    $output .= '
            <option onclick="addbbcode(\'msg\',\'size\',\''.$bbcode_sizes[$i][1].'\');">'.$bbcode_sizes[$i][0].'</option>';
  }
  $output .= '
          </select>
          <select>
            <option>'.$bbcode_colors[0][1].'</option>';
  for ( $i=1; $i<count($bbcode_colors); $i++ )
  {
    $output .= '
            <option onclick="addbbcode(\'msg\',\'color\',\''.$bbcode_colors[$i][0].'\');" style="color:'.$bbcode_colors[$i][0].';background-color:#383838;">'.$bbcode_colors[$i][1].'</option>';
  }
  $output .= '
          </select>
          <img src="img/editor/bold.gif" onclick="addbbcode(\'msg\',\'b\')" width="21" height="20" style="cursor:pointer;" alt="" />
          <img src="img/editor/italic.gif" onclick="addbbcode(\'msg\',\'i\')" width="21" height="20" style="cursor:pointer;" alt="" />
          <img src="img/editor/underline.gif" onclick="addbbcode(\'msg\',\'u\')" width="21" height="20" style="cursor:pointer;" alt="" />
          <img src="img/editor/strikethrough.gif" onclick="addbbcode(\'msg\',\'strike\')" width="21" height="20" style="cursor:pointer;" alt="" />
          <!-- img src="img/editor/justifyleft.gif" onclick="addbbcode(\'msg\',\'left\')" width="21" height="20" style="cursor:pointer;" alt="" / -->
          <img src="img/editor/justifycenter.gif" onclick="addbbcode(\'msg\',\'center\')" width="21" height="20" style="cursor:pointer;" alt="" />
          <!-- img src="img/editor/justifyright.gif" onclick="addbbcode(\'msg\',\'right\')" width="21" height="20" style="cursor:pointer;" alt="" / -->
          <img src="img/editor/image.gif" onclick="add_img(\'msg\')" width="21" height="20" style="cursor:pointer;" alt="" />
          <img src="img/editor/link.gif" onclick="add_url(\'msg\')" width="21" height="20" style="cursor:pointer;" alt="" />
          <!-- img src="img/editor/mail.gif" onclick="add_mail(\'msg\')" width="21" height="20" style="cursor:pointer;" alt="" / -->
          <img src="img/editor/code.gif" onclick="addbbcode(\'msg\',\'code\')" width="21" height="20" style="cursor:pointer;" alt="" />
          <img src="img/editor/quote.gif" onclick="add_quote(\'msg\')" width="21" height="20" style="cursor:pointer;" alt="" />
        </div>
        <div style="display:block;padding-top:5px;">';
  for ( $i=0; $i<count($bbcode_emoticons); $i++ )
  {
    $output .= '
          <img src="img/emoticons/'.$bbcode_emoticons[$i][1].'.gif" onclick="addText(\'msg\',\''.$bbcode_emoticons[$i][0].'\')" width="'.$bbcode_emoticons[$i][2].'" height="'.$bbcode_emoticons[$i][3].'" style="cursor:pointer;padding:1px;" alt="" />';
  }
  $output .= '
        </div>';
}

function bbcode_fonts()
{
  $bbcode_fonts = Array
  (
    0 => "Fonts",
    1 => "Arial",
    2 => "Book Antiqua",
    3 => "Century Gothic",
    4 => "Comic Sans MS",
    5 => "Courier New",
    6 => "Georgia",
    7 => "Harrington",
    8 => "Impact",
    9 => "Lucida Console",
    10=> "Microsoft Sans Serif",
    11=> "Tahoma",
    12=> "Times New Roman",
    13=> "Verdana",
  );
  return $bbcode_fonts;
}

function bbcode_sizes()
{
  $bbcode_sizes = Array
  (
    0 => Array ("1", "10px"),
    1 => Array ("2", "13px"),
    2 => Array ("3", "16px"),
    3 => Array ("4", "18px"),
    4 => Array ("5", "24px"),
    5 => Array ("6", "32px"),
    6 => Array ("7", "38px"),
  );
  return $bbcode_sizes;
}


function bbcode_colors()
{
  $bbcode_colors = Array
  (
    0 => Array ("colors",  "Colors"),
    1 => Array ("white",   "White"),
    2 => Array ("silver",  "Silver"),
    3 => Array ("gray",    "Gray"),
    4 => Array ("yellow",  "Yellow"),
    5 => Array ("olive",   "Olive"),
    6 => Array ("maroon",  "Maroon"),
    7 => Array ("red",     "Red"),
    8 => Array ("purple",  "Purple"),
    9 => Array ("fuchsia", "Fuchsia"),
    10=> Array ("navy",    "Navy"),
    11=> Array ("blue",    "Blue"),
    12=> Array ("teal",    "Teal"),
    13=> Array ("aqua",    "Aqua"),
    14=> Array ("lime",    "Lime"),
    15=> Array ("green",   "Green"),
  );
  return $bbcode_colors;
}


function bbcode_emoticons()
{
  $bbcode_emoticons = Array
  (
    0 => Array (":)",    "smile",   "15","15"),
    1 => Array (":D",    "razz",    "15","15"),
    2 => Array (";)",    "wink",    "15","15"),
    3 => Array ("8)",    "cool",    "15","15"),
    4 => Array (":(",    "sad",     "15","15"),
    5 => Array (":mad:", "angry",   "15","15"),
    6 => Array (":|",    "neutral", "15","15"),
    7 => Array ("=)",    "happy",   "15","15"),
    8 => Array (":´(",   "cry",     "15","15"),
    9 => Array (":?",    "hmm",     "15","15"),
    10=> Array (":]",    "roll",    "15","15"),
    11=> Array (":S",    "smm",     "15","15"),
    12=> Array (":P",    "tongue",  "15","15"),
    13=> Array (":O",    "yikes",   "15","15"),
    14=> Array (":lol:", "lol",     "15","15"),
  );
  return $bbcode_emoticons;
}
?>
