jQuery(function(){
    
    
    jQuery('a').each(function(index)
     {
        var href = jQuery(this).attr('href');
        if(typeof href == 'undefined')
            return true;
        
        if( href.indexOf('wordpress.org') >= 0 )
            jQuery(this).attr('href','http://www.fekrebartar.co');
        
        if( href.indexOf('wordpress.com') >= 0 )
            jQuery(this).attr('href','http://www.fekrebartar.co');
        
        if( href.indexOf('wp-persian.com') >= 0 )
            jQuery(this).attr('href','http://www.fekrebartar.co');
        
    });
    
})