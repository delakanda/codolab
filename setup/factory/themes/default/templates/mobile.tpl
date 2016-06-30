<!DOCTYPE html>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<html lang="en">
<head>
{$styles}
{$scripts}
<style>
{literal}
h1{
    padding:2px;
    margin:0px;
}
    
body{
    background-color:#ebffe6;
    background-image:none;
}

.fapi-form{
    padding:0px;
    background-color:transparent;
    box-shadow:none;
    padding:10px;
}

.fapi-label{
    text-shadow:none;
    font-weight:bold;
    font-size:x-large;
}

.fapi-textfield{
    width:95%;
    padding:2%;
    font-size:large;
    border:2px solid #63a756;
    box-shadow:none;
}

.fapi-submit{
    font-size:xx-large;
    padding:10px;
    background-image:none;
}

.fapi-select-table td{
    font-size:large;
    padding:10px;
}

#map_canvas{
    width:100%;
    height:300px;
}
{/literal}
</style>
<title>{$title}</title>
</head>
<body onload="wyf.init()">
<div id="header"><h1>NTHC Mobile</h1></div>
<div id="wrapper">
{$content}
</div>
</body>
</html>