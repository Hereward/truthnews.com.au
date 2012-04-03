function selectFormText(vForm,vFld){
      
      document[vForm][vFld].select();
      document[vForm][vFld].focus();
}


/*
window.fCopyToClipboard = function(rSource){ rSource.select() if(window.clipboardData){ var r=clipboardData.setData('Text',rSource.value); return 1; } else return 0 } 

function CopyToClipboard() {
  CopiedTxt = document.selection.createRange();
  CopiedTxt.execCommand("Copy");
}
*/


 function popWindow(vURL,vW,vH,wName) {
  vParams =  "location=0,status=1,scrollbars=0,width="+vW+",height="+vH;
   mywindow = window.open (vURL, wName, vParams);
   //mywindow.moveTo(0,0);
   mywindow.focus();
  return false;
 } 


 function popCalWindow(vURL,vW,vH,wName) {
  wName ='calendar'
  vParams =  "scrollbars=1,resizable=1,toolbar=1,menubar=1,location=0,status=1,scrollbars=0,width="+vW+",height="+vH;
   mywindow = window.open (vURL, wName, vParams);
   //mywindow.moveTo(0,0);
  mywindow.focus();
  return false;
 } 

function load_video_player_object_generic(id,url,duration) {
 // alert("id = " + id);
 // alert("duration = " + duration);
  var so = new SWFObject('/player/player.swf','mpl','560','315','9');
  so.addParam('allowfullscreen','true');
  so.addParam('allowscriptaccess','always');
  so.addParam('wmode','opaque');
  so.addVariable('duration',duration);
  so.addVariable('file',url);
  so.addVariable('frontcolor','cccccc');
  so.addVariable('lightcolor','66cc00');
  so.addVariable('backcolor','111111');
  so.addVariable('skin','http://www.longtailvideo.com/jw/upload/stylish.swf');
  so.write('mediaspace'+'_'+id);
}

function load_player_object_generic(id,url,duration) {
 // alert("id = " + id);
 // alert("duration = " + duration);
  var so = new SWFObject('/player/player.swf','mpl','470','60','9');
  so.addParam('allowfullscreen','true');
  so.addParam('allowscriptaccess','always');
  so.addParam('wmode','opaque');
  so.addVariable('duration',duration);
  so.addVariable('file',url);
  so.addVariable('frontcolor','cccccc');
  so.addVariable('lightcolor','66cc00');
  so.addVariable('backcolor','111111');
  so.addVariable('skin','http://www.longtailvideo.com/jw/upload/stylish.swf');
  so.write('mediaspace'+'_'+id);
}


function load_player_object(entry_id,media_date,duration,source,segment) {
  //alert("entry_id = " + entry_id);
  var so = new SWFObject('/player/player.swf','mpl','470','60','9');
  var media_url = '';
  var media_id = 'mediaspace'+'_'+entry_id;
  if (segment>1) {
     media_id = media_id+'_'+segment;
  }
  so.addParam('allowfullscreen','true');
  so.addParam('allowscriptaccess','always');
  so.addParam('wmode','opaque');
  so.addVariable('duration',duration);
  if (segment>1) {
     media_url = 'http://www.truthnews.com.au/radio/export/'+source+'_'+media_date+'_'+segment+'.mp3';
  } else {
     media_url = 'http://www.truthnews.com.au/radio/export/'+source+'_'+media_date+'.mp3';
  }
  //alert("media URL = " + media_url);
  so.addVariable('file',media_url);
  
  
  so.addVariable('frontcolor','cccccc');
  so.addVariable('lightcolor','66cc00');
  so.addVariable('backcolor','111111');
  so.addVariable('skin','http://www.longtailvideo.com/jw/upload/stylish.swf');
  //so.write('mediaspace'+'_'+entry_id);
  so.write(media_id);
}

function load_player_object_simple(entry_id,url) {
  var so = new SWFObject('/player/player.swf','mpl','470','60','9');
  so.addParam('allowfullscreen','true');
  so.addParam('allowscriptaccess','always');
  so.addParam('wmode','opaque');
  //so.addVariable('duration',duration);
  so.addVariable('file',url);
  so.addVariable('frontcolor','cccccc');
  so.addVariable('lightcolor','66cc00');
  so.addVariable('backcolor','111111');
  so.addVariable('skin','http://www.longtailvideo.com/jw/upload/stylish.swf');
  so.write('mediaspace'+'_'+entry_id);
}