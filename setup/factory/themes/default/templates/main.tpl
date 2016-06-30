<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<META http-equiv="Default-Style" content="main">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    {$styles}
    {$scripts}
    <title>{$title}</title>
    <script type='text/javascript'>
        {literal}
        function resize()
        {
            $('#header').width($(window).width());
            $('#user-menu').css({left:$(window).width() - $('#user-menu').width()});
        }

        $(function(){
            resize();
            $(window).resize(resize);
            $("body").bind("overflowchanged", resize);
            setTimeout("resize()", 200);
            $(body).click(function(){$('#user-menu').fadeOut()});
            $('#side-menu').css({minHeight: ($(window).height() - 80) + 'px' });
        });
        {/literal}
    </script>
</head>
<body onload="wyf.init()">
<div id="header">
    <div id='menu-section'>
    <a href='/'><img src='/app/themes/default/images/home.png'/></a><span id='top-menu'>{$top_menu}</span>
    </div>
    <div id='user-section'>
        <div id='user-info'><a href='#' onclick="$('#user-menu').fadeIn()"><i class="fa fa-user"></i> &nbsp;{$firstname} {$lastname}</a></div>
    </div>
</div>

    <div id='user-menu'>
        <ul>
            <li><a href='/system/change_password'><i class="fa fa-key"></i> &nbsp; Change Password</a></li>
            <li><a href='/system/my_trail'><i class="fa fa-location-arrow"></i> &nbsp; Audit Trail</a></li>
            <li><a href="/system/my_ip"><i class="fa fa-globe"></i> &nbsp; My IP Address</a></li>
            <li><a href='/system/logout'><i class="fa fa-sign-out"></i> &nbsp; Logout</a></li>
        </ul>
    </div>

<div id="wrapper">
{if $side_menu_hidden eq false}
<div id="side-menu">
{$side_menu}
</div>
{/if}

<div id="body" {if $side_menu_hidden eq true} style="width:100%" {/if}>
<div id="body-top">
<h2>{$module_name}</h2>
{$module_description}
</div>
{$notification}
<div id="body-internal">
{$content}
</div>
</div>
<div id="footer">
    <p>Copyright &copy; 2015, CBS</p>
    <p><span>IP Address : <span class = 'emphasis-text'>{$ip_add} </span> </span>  <b><span>&nbsp;&nbsp;
    |&nbsp;&nbsp;</span></b> <span>Location : <span class = 'emphasis-text'>{if $branch neq null} {$branch} {else} Unknown {/if}</span></span> </p>

</div>
</div>
</body>
</html>
