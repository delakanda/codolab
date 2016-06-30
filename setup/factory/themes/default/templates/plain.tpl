<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<META http-equiv="Default-Style" content="main">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
{$styles}
{$scripts}
    <script type='text/javascript'>
        {literal}
        function resize()
        {
            $('#header').width($(window).width());
            $('#user-menu').css({left:$(window).width() - $('#user-menu').width()});
            if(typeof resizeExtension === 'function')
            {
                resizeExtension();
            }
        }
        
        $(function(){
            resize();
            $(window).resize(resize);
            setTimeout("resize()", 200);
            $(body).click(function(){$('#user-menu').fadeOut()});
            $('#side-menu').css({minHeight: ($(window).height() - 80) + 'px' });
        });
        {/literal}
    </script>
<title>{$title}</title>
</head>
<body onload="wyf.init()" style="background: none">
<div id="plain_wrapper">
{$content}
</div>
</body>
</html>