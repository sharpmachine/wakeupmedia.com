//-------------------------------------------------
//		youtube playlist jquery plugin
//		Created by dan@geckonm.com
//		www.geckonewmedia.com
//
//		v1.1 - updated to allow fullscreen 
//			 - thanks Ashraf for the request
//      v1.2 - updated to allow for mixed yt/image galleries
//           - for Markus Thyberg
//-------------------------------------------------

String.prototype.startsWith = function(str){
    return (this.indexOf(str) === 0);
}


jQuery.fn.ytplaylist = function(options) {
 
  // default settings
  var options = jQuery.extend( {
    holderId: 'ytvideo',
	playerHeight: 310,
	playerWidth: 551,
	addThumbs: false,
	thumbSize: 'small',
	showInline: false,
	autoPlay: true,
	showRelated: true,
	allowFullScreen: false
  },options);
 
  return this.each(function() {
							
   		var jQueryel = jQuery(this);
		
		var autoPlay = "";
		var showRelated = "&rel=0";
		var fullScreen = "";
		if(options.autoPlay) autoPlay = "&autoplay=1"; 
		if(options.showRelated) showRelated = "&rel=1"; 
		if(options.allowFullScreen) fullScreen = "&fs=1"; 
		
		//throw a youtube player in
		function playOld(id) {
		   var html  = '';
	
		   html += '<object height="'+options.playerHeight+'" width="'+options.playerWidth+'">';
		   html += '<param name="movie" value="http://www.youtube.com/v/'+id+autoPlay+showRelated+fullScreen+'"> </param>';
		   html += '<param name="wmode" value="transparent"> </param>';
		   if(options.allowFullScreen) { 
		   		html += '<param name="allowfullscreen" value="true"> </param>'; 
		   }
		   html += '<embed src="http://www.youtube.com/v/'+id+autoPlay+showRelated+fullScreen+'"';
		   if(options.allowFullScreen) { 
		   		html += ' allowfullscreen="true" '; 
		   	}
		   html += 'type="application/x-shockwave-flash" wmode="transparent"  height="'+options.playerHeight+'" width="'+options.playerWidth+'"></embed>';
		   html += '</object>';
			
		   return html;
		};
		
		
		function playNew (id) {
		  var html = '';
		  html += '<iframe width="'+ options.playerWidth +'" height="'+ options.playerHeight +'"';
		  html += ' src="http://www.youtube.com/embed/'+ id +'" frameborder="0"';
		  html += ' allowfullscreen></iframe>';

		  return html;
		}
		
		
		//grab a youtube id from a (clean, no querystring) url (thanks to http://jquery-howto.blogspot.com/2009/05/jyoutube-jquery-youtube-thumbnail.html)
		function youtubeid(url) {
			var ytid = url.match("[\\?&]v=([^&#]*)");
			ytid = ytid[1];
			return ytid;
		};
		
		
		
		//
		jQueryel.children('li').each(function() {
            jQuery(this).find('a.video-thumb').each(function() {
                var thisHref = jQuery(this).attr('href');
                
                //old-style youtube links
                if (thisHref.startsWith('http://www.youtube.com')) {
                    jQuery(this).addClass('yt-vid');
                    jQuery(this).data('yt-id', youtubeid(thisHref) );
                }
                //new style youtu.be links
                else if (thisHref.startsWith('http://youtu.be')) {
                    jQuery(this).addClass('yt-vid');
                    var id = thisHref.substr(thisHref.lastIndexOf("/") + 1);
                    jQuery(this).data('yt-id', id );
                }
                else {
                    //must be an image link (naive)
                    jQuery(this).addClass('img-link');
                }
                
               // alert(thisHref);
            });
		});
		
		
		//load video on request
		jQueryel.children("li").children("a.yt-vid").click(function() {
			
			if(options.showInline) {
				jQuery("li.currentvideo").removeClass("currentvideo");
				jQuery(this).parent("li").addClass("currentvideo").html(playNew(jQuery(this).data("yt-id")));
			}
			else {
				jQuery("#"+options.holderId+"").html(playNew(jQuery(this).data("yt-id")));
				jQuery(this).parent().parent("ul").find("li.currentvideo").removeClass("currentvideo");
				jQuery(this).parent("li").addClass("currentvideo");
			}	
			return false;
		});

		jQueryel.find("a.img-link").click(function() {
		    var jQueryimg = jQuery('<img/>');
		    jQueryimg.attr({
		            src:jQuery(this).attr('href') })
		        .css({
		            display: 'none',
		            position: 'absolute',
		            left: '0px',
		            top: '50%'});

		    if(options.showInline) {
		        jQuery("li.currentvideo").removeClass("currentvideo");
		        jQuery(this).parent("li").addClass("currentvideo").html(jQueryimg);
	        }
	        else {
	            
	            jQuery("#"+options.holderId+"").html(jQueryimg);
				jQuery(this).closest("ul").find("li.currentvideo").removeClass("currentvideo");
				jQuery(this).parent("li").addClass("currentvideo");
				
	        }
            //wait for image to load (webkit!), then set width or height
            //based on dimensions of the image
            setTimeout(function() {
                if ( jQueryimg.height() < jQueryimg.width() ) {
                    jQueryimg.width(options.playerWidth).css('margin-top',parseInt(jQueryimg.height()/-2, 10)).css({
                        height: 'auto'
                    });
                }
                else {
                    jQueryimg.css({
                        height: options.playerHeight,
                        width: 'auto',
                        top: '0px',
                        position: 'relative'
                    });
                }
                jQueryimg.fadeIn();
            }, 100);
            
            
		    return false;
	    });
		
		
		//do we want thumns with that?
		if(options.addThumbs) {
			
			jQueryel.children().each(function(i){
				
				//replace first link
				var jQuerylink = jQuery(this).find('a:first');
				var replacedText = jQuery(this).text();
				
				if (jQuerylink.hasClass('yt-vid')) {
				    
				    if(options.thumbSize == 'small') {
    					var thumbUrl = "http://img.youtube.com/vi/"+jQuerylink.data("yt-id")+"/2.jpg";
    				}
    				else {
    					var thumbUrl = "http://img.youtube.com/vi/"+jQuerylink.data("yt-id")+"/0.jpg";
    				}

    				var thumbHtml = "<img src='"+thumbUrl+"' alt='"+replacedText+"' />";
    				jQuerylink.empty().html(thumbHtml+replacedText).attr("title", replacedText);
				    
				}
				else {
				    //is an image link
				    var jQueryimg = jQuery('<img/>').attr('src',jQuerylink.attr('href'));
				    jQuerylink.empty().html(jQueryimg).attr("title", replacedText);
				}	
				
			});	
			
		}
		
		//load inital video
		var firstVid = jQueryel.children("li:first-child").addClass("currentvideo").children("a.video-thumb").click();
        
			
		
   
  });
 
};