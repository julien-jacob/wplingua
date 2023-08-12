
jQuery(document).ready(function($){var $_GET=[];var parts=window.location.search.substr(1).split("&");for(var i=0;i<parts.length;i++){var temp=parts[i].split("=");$_GET[decodeURIComponent(temp[0])]=decodeURIComponent(temp[1]);}
console.log($_GET);if($_GET["wplingua-visual-editor"]!==undefined&&$_GET["wplingua-list"]!==undefined){$("body").addClass("wplingua-modal-open");}});