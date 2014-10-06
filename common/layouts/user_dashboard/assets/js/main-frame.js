
/***
 * function to handle the popup windows
 **/
function popWindow(pageUrl,ptitle,pwidth,pheight,presizable,pscrollbars,pmenubar,ptoolbar,pstatus,plocation,ptop,pleft) {
    strWindowOpts="width="+pwidth+",height="+pheight;
        
    if (presizable && (presizable=='yes' || presizable==1)) {
        strWindowOpts+=',resizable=yes';
    }
    if (pscrollbars && (pscrollbars=='yes' || pscrollbars==1)) {
        strWindowOpts+=',scrollbars=yes';
    }
    if (pmenubar && (penubar=='yes' || pmenubar==1)) {
        strWindowOpts+=',menubar=yes';
    }
    if (ptoolbar && (ptoolbar=='yes' || ptoolbar==1)) {
        strWindowOpts+=',toolbar=yes';
    }
    if (pstatus && (pstatus=='yes' || pstatus==1)) {
        strWindowOpts+=',status=yes';
    }
    if (plocation && (plocation=='yes' || plocation==1)) {
        strWindowOpts+=',location=yes';
    }

        
    var left = Number((screen.width/2)-(pwidth/2));
    var top = Number((screen.height/2)-(pheight/2+40));
        
    strWindowOpts+=',screenX='+top+',top='+top;
    strWindowOpts+=',screenY='+left+',left='+left;

    var targetWin=window.open(pageUrl,ptitle,strWindowOpts);
}


function popupFS(purl, ptitle) 
{
    strWindowOpts  = 'width='+screen.width;
    strWindowOpts += ', height='+screen.height;
    strWindowOpts += ', top=0, left=0'
    strWindowOpts += ', toolbar=no, scrollbars=yes'
    strWindowOpts += ', fullscreen=yes';
 


    newwin=window.open(purl, ptitle, strWindowOpts);
    if (window.focus) {
        newwin.focus()
        }
    return false;
}
