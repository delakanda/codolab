<form onsubmit="return check()" {$form}</form>
<div id="buttons">
    <input type="button" id="override" value="Override" onclick="save(1)">
    <input type="button" id="save_new" value="Save New"  onclick="save(2)">
</div>

<script type='text/javascript' >
var FaL = new Array(0,0);
var previous = {$actual};
var data = {$key};
var groups = 0;
var ex = 0;

$("#head_fonts_bottom_margin").on('keyup keydown change', function(){
    previous = document.getElementById('head_fonts_bottom_margin').value;
{*    preventives('head_fonts_bottom_margin');*}
});

$("#sub_fonts_bottom_margin").on('keyup keydown change', function(){
    preventives('sub_fonts_bottom_margin');
});

$("#body_fonts_bottom_margin").on('keyup keydown change', function(){
    preventives('body_fonts_bottom_margin');
});

$("#grp_fonts_bottom_margin").on('keyup keydown change', function(){
    preventives('grp_fonts_bottom_margin');
});


$("#head_fonts_size").on('keyup keydown change', function(){
    preventives('head_fonts_size', 1);
});

$("#sub_fonts_size").on('keyup keydown change', function(){
    preventives('sub_fonts_size', 1);
});

$("#body_fonts_size").on('keyup keydown change', function(){
    preventives('body_fonts_size', 1);
});

$("#grp_fonts_size").on('keyup keydown change', function(){
    preventives('grp_fonts_size', 1);
});


updateOptionFieldData();
updateSubtitle();
updateGrouping();
updateWidth();
updateWrap();
</script>
