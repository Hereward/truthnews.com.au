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
    var so = new SWFObject('/player/player.swf','mpl','280','60','9');
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
    var so = new SWFObject('/player/player.swf','mpl','280','60','9');
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
        media_url = '/radio/export/'+source+'_'+media_date+'_'+segment+'.mp3';
    } else {
        media_url = '/radio/export/'+source+'_'+media_date+'.mp3';
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
    var so = new SWFObject('/player/player.swf','mpl','280','60','9');
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

function get_social_media($)
{
    url_fb =  '/assets/js/fb.js';
    url_tw =  '/assets/js/twitter.js';
    //alert('URL = ['+url+']');
    $.getScript(url_fb);
    $.getScript('http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4d2158e9345372da');
    $.getScript(url_tw);
}

function get_google_stuff() {
    $.getScript('/assets/js/analytics.js');
    
  //  $.getScript('http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js');
}