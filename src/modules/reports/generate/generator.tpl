<div style="width:100%">
    <table style="width:100%;table-layout:fixed;">
        <tr id="main_sel_div">
            <td style="width:50%; vertical-align:top;">
                <table style="width:100%;table-layout:fixed;">
                    <tr>
                        <td>
                            {$form}
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:white;" id="preview">
                            
                        </td>
                    </tr>
                </table>
            </td>
            <td style="background-color:white;width:50%;vertical-align:top;" id="column_data">
                <div style="" id="column_div">

                </div>
            </td>
        </tr>
    </table>
</div>

<script type='text/javascript' >
var data = {$data};
var columns = [];
var click_counter = [];
var product;
var module;


window_height = $(window).height();
header_height = $("#header").height();
footer_height = $("#footer").height();
$("#main_sel_div").height(window_height - header_height - 100);
$("#preview").height(header_height);

$(document).keypress(function(event){
    if (event.which == '13') {
        event.preventDefault();
    }
});

$(document).on('keyup keydown change', function(){
    toggler();
    addSortList(document.getElementById('sort').selectedIndex);
    generateReportJson();
});

updateModules();
updateColumns();
</script>
