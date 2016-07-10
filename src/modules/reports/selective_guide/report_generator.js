function updateOptionFieldData()
{
    for ( var key = 0; key < data;  key ++)
    {
        if (document.getElementById(key + '_opt').value === '')
        {
            document.getElementById(key + '_option_1').value = '';
            document.getElementById(key + '_option_2').value = '';
            document.getElementById(key + '_option_1').style.visibility = 'hidden';
            document.getElementById(key + '_option_2').style.visibility = 'hidden';
        }

        else if (document.getElementById(key + '_opt').value === '4')
        {
            document.getElementById(key + '_option_1').style.visibility = 'visible';
            document.getElementById(key + '_option_2').style.visibility = 'visible';
        }

        else
        {
            document.getElementById(key + '_option_2').value = '';
            document.getElementById(key + '_option_1').style.visibility = 'visible';
            document.getElementById(key + '_option_2').style.visibility = 'hidden';
        }
    }
    
    updateSubtitle();
}

function updateGrouping(prevent)
{
    var found = false;
    var details = getGroupingDetails();
    for (var key = 0; key < data;  key ++)
    {
        var type = document.getElementById(key + '_type').value;
        
        if (document.getElementById(key + '_group').value !== '')
        {
            found = true;
            document.getElementById(key + '_width').value = '4';
            document.getElementById(key + '_margin').value = '3';
            document.getElementById(key + '_width').style.visibility = 'hidden';
            document.getElementById(key + '_total').style.visibility = 'visible';
            document.getElementById(key + '_margin').style.visibility = 'visible';
            
            document.getElementById(key + '_addon').disabled = '';
            document.getElementById(key + '_prefix').disabled = '';
            document.getElementById(key + '_substitute').disabled = '';
        }
        
        else
        {
            document.getElementById(key + '_margin').value = '';
            document.getElementById(key + '_total').style.visibility = 'hidden';
            document.getElementById(key + '_width').style.visibility = 'visible';
            document.getElementById(key + '_margin').style.visibility = 'hidden';
            
            document.getElementById(key + '_addon').value = '';
            document.getElementById(key + '_prefix').value = '';
            document.getElementById(key + '_substitute').value = '';
            
            document.getElementById(key + '_addon').disabled = true;
            document.getElementById(key + '_prefix').disabled = true;
            document.getElementById(key + '_substitute').disabled = true;
        }
        
        if (type === 'double' || type === 'Number' || type === 'integer')
        {
            type === 'double' ? document.getElementById(key + '_total').value = 'true' : null;
            document.getElementById(key + '_total').style.visibility = 'visible';
        }
        
        if (details && document.getElementById(key + '_group').value === details[0])
        {
            document.getElementById(key + '_margin').value = '3';
        }
        
        if (details && document.getElementById(key + '_group').value === details[1])
        {
            document.getElementById(key + '_total').value = 'true';
        }
        
        if (document.getElementById(key + '_group').value === '1')
        {
            var first = document.getElementById(key + '_total').value;
        }
    }
    
    if((!found || document.getElementById('groups_paging').checked) || first !== 'true')
    {
        document.getElementById('summarize').checked = '';
        document.getElementById('summarize').disabled = true; 
    }

    else
    {
        document.getElementById('summarize').disabled = false;
    }

    if (!document.getElementById('groups_paging').checked)
    {
        document.getElementById('repeat_logos').checked = '';
        document.getElementById('repeat_logos').disabled = true;
    }
    
    else
    {
        document.getElementById('repeat_logos').disabled = false;
    }

    updateWidth();
    !prevent ? preventives() : null;
}

function updateGroupSelection(selected)
{
    if(FaL[1] || groups === 0)
    {
        var last = parseInt(FaL[1]);
        var group = document.getElementById(selected + '_group').value;
        if((ex !== last && Number(group) !== last + 1) || ((group && ex === last && (ex !== 0 )) || (Number(group) > groups + 1)))
        {
            alert('Group must follow an ascending integral consecutive trend starting from 1');
            document.getElementById(selected + '_group').value = ex;
            throw new Error("Something went badly wrong!");
        }
    }
        
    getGroupValue(selected);
    updateGrouping();
}

function updateWidth()
{
    var width = document.getElementById('number').checked ? 4 : 0;

    for ( var key = 0; key < data;  key ++)
    {
        if (document.getElementById(key + '_group').value !== '')
        {
            continue;
        }
        
        width += Number(document.getElementById(key + '_width').value);
    }
    
    document.getElementById('width').value = width.toFixed(0);
}

function updateWrap()
{
    var wrap = document.getElementById('wrap_cell').checked;

    if(wrap)
    {
        document.getElementById('cell_height').disabled = '';
    }
        
    else
    {
        document.getElementById('cell_height').disabled = true;
    }
}

function updateSubtitle()
{
    if (document.getElementById('hider').checked)
    {
        document.getElementById('sub_heading').value = '';
        document.getElementById('sub_heading').style.visibility = 'hidden';
    }
    
    else
    {
        document.getElementById('sub_heading').value = getSubtitle();
        document.getElementById("sub_heading").style.fontSize = "10px";
        document.getElementById('sub_heading').style.visibility = 'visible';
    }
    
    getGroupingDetails();
}


function getSubtitle()
{
    var ret = "";
    for (var key = 0; key < data;  key ++)
    {
        var type = document.getElementById(key + '_type').value;
        var operand = document.getElementById(key + '_opt').value;
        var first = document.getElementById(key + '_option_1').value;
        var second = document.getElementById(key + '_option_2').value;
        var field = document.getElementById(key + '_field_names').value;
        
        var filters = getNarration(operand, type);
        
        ret = ret && first? ret + " and " : ret;
        ret = first ? ret + field + " " + filters[0] + " " + first : ret;
        ret = second ? ret + " " + filters[1] + " " + second : ret; 
    }
    
    return ret;
}

function getGroupValue(key)
{
    ex = Number(document.getElementById(key + '_group').value);   
}

function getGroupingDetails()
{
    var count = 0;
    var details = new Array();
    for (var key = 0; key < data;  key ++)
    {
        if (document.getElementById(key + '_group').value !== '')
        {
            details[count++] = document.getElementById(key + '_group').value;
        }
    }
    
    groups = count;
    details.sort();
    
    var number = document.getElementById('sub_heading').value ? 0 : previous;
    document.getElementById('head_fonts_bottom_margin').value = details[0] ? number : 0;
    var $return = details[0] ? new Array(details[0], details[details.length - 1]) : null;
    FaL = $return ? $return : new Array(0,0);

    return $return;
}

function getNarration(option, type)
{
    if (option === '1' && (type === "string" || type === "text"))
    {
        return new Array('containing');
    }

    if (option === '2' && (type === "string" || type === "text"))
    {
        return new Array('is exactly');
    }
    
    if (option === '1' && (type === "date" || type === "datetime"))
    {
        return new Array('on');
    }

    if (option === '2' && (type === "date" || type === "datetime"))
    {
        return new Array('after');
    }
    
    if (option === '3' && (type === "date" || type === "datetime"))
    {
        return new Array('before');
    }
    
    if (option === '4' && (type === "date" || type === "datetime"))
    {
        return new Array('from','to');
    }

    if (option === '1' && (type === "double" || type === "integer"))
    {
        return new Array('equal');
    }

    if (option === '2' && (type === "double" || type === "integer"))
    {
        return new Array('greater than');
    }
    
    if (option === '3' && (type === "double" || type === "integer"))
    {
        return new Array('less than');
    }
    
    if (option === '4' && (type === "double" || type === "integer"))
    {
        return new Array('between','and');
    }
}

function getFieldAttributes(id)
{
    var fields = "";
    for (var key = 0; key < data;  key ++)
    {
        fields = fields === "" ? "" : fields + "," ;
        var value = document.getElementById(key + id).value ? document.getElementById(key + id).value : '';
        fields += '"' + value + '"';
    }
    
    return fields;
}

function getFieldAttributesChecked(id)
{
    var fields = "";
    for (var key = 0; key < data;  key ++)
    {
        fields = fields === "" ? "" : fields + "," ;
        var value = document.getElementById(key + id).checked ? document.getElementById(key + id).checked : '';
        fields += '"' + value + '"';
    }
    
    return fields;
}

function preventives(selected, size)
{
    if(selected)
    {
        var value = size ? 8 : 0; 
        if (isNaN(document.getElementById(selected).value))
        {
            alert('Must be a number');
            document.getElementById(selected).value = size ? '8' : '0';
            throw new Error("Something went badly wrong!");
        }
        
        if (Number(document.getElementById(selected).value) < value)
        {
            alert('Cannot be less than ' + value );
            document.getElementById(selected).value = value;
            throw new Error("Something went badly wrong!");
        }
    }
    
    for (var key = 0; key < data;  key ++)
    {
        if (isNaN(document.getElementById(key + '_width').value))
        {
            updateWidth();
            alert('Must be a number');
            document.getElementById(key + '_width').value = '4';
            throw new Error("Something went badly wrong!");
        }
        
        if (Number(document.getElementById(key + '_width').value) < 4 && document.getElementById(key + '_width').value)
        {
            updateWidth();
            alert('Cannot be less than 4');
            document.getElementById(key + '_width').value = '4';
            throw new Error("Something went badly wrong!");
        }
        
        if (isNaN(document.getElementById(key + '_margin').value))
        {
            alert('Must be a number');
            document.getElementById(key + '_margin').value = '3';
            throw new Error("Something went badly wrong!");
        }
        
        if (FaL && document.getElementById(key + '_group').value === FaL[0] && Number(document.getElementById(key + '_margin').value) < 1)
        {
            alert('Cannot be less than 1');
            document.getElementById(key + '_margin').value = '1';
            throw new Error("Something went badly wrong!");
        }
        
        if (FaL && document.getElementById(key + '_group').value === FaL[1] && document.getElementById(key + '_total').value !== 'true')
        {
            alert('Cannot change a strict requirement');
            document.getElementById(key + '_total').value = 'true';
            throw new Error("Something went badly wrong!");
        }
        
        if (document.getElementById(key + '_group').value === '1')
        {
           updateGrouping(true);
        }
        
        updateWidth();
    }
}


function check()
{
    preventives();
    if(document.getElementById('width').value > 100) 
    {
        alert('Check WIDTH, Total Width should be 100%');
        throw new Error("Something went badly wrong!");
    } 
    
    for (var key = 0; key < data;  key ++)
    {
        if(document.getElementById(key + '_width').value < 4 && document.getElementById(key + '_group').value === '')
        {
            alert('Individual WIDTH cannot be les than 4%');
            throw new Error("Something went badly wrong!");
        }
    }
}


function save(signal)
{
    check();
    preventives();
    
    var model = '"model":["' + document.getElementById('model').value + '"],';
    var name = '"name":["' + document.getElementById('name').value + '"],';
    var format = '"report_format":["' + document.getElementById('report_format').value + '"],';
    var orientation = '"page_orientation":["' + document.getElementById('page_orientation').value + '"],';
    var size = '"paper_size":["' + document.getElementById('paper_size').value + '"],';
    var header = '"heading":["' + document.getElementById('heading').value + '"],'; 
    var subHeading = '"sub_heading":["' + document.getElementById('sub_heading').value + '"],';
    var hideSub = '"hide_sub":["' + document.getElementById('hider').checked + '"],';
    var repeatLogos = '"repeat_logos":["' + document.getElementById('repeat_logos').checked + '"],';
    var sortField = '"sort_field":["' + document.getElementById('sort_field').value + '"],';
    var orderField = '"order_field":["' + document.getElementById('order').value + '"],';
    var limit = '"limit":["' + document.getElementById('limit').value + '"],';
    var number = '"numbering":["' + document.getElementById('number').checked + '"],';
    var overall = '"overall":["' + document.getElementById('overall').checked + '"],';

    var wrapCell = '"wrap_cell":["' + document.getElementById('wrap_cell').checked + '"],';
    var cellHeight = '"cell_height":["' + document.getElementById('cell_height').value + '"],';
    var wrapReplace = '"wrap_replace":["' + document.getElementById('wrap_replace').value + '"],';
    
    var summarize = '"summarize":["' + document.getElementById('summarize').checked + '"],';
    var groupsPaging = '"groups_paging":["' + document.getElementById('groups_paging').checked + '"],';

    var headFont = '"h_font":["' + document.getElementById('head_fonts_font').value + '"],';
    var headSize = '"h_size":["' + document.getElementById('head_fonts_size').value + '"],';
    var headMargin = '"h_margin":["' + document.getElementById('head_fonts_bottom_margin').value + '"],';
    var headBold = '"h_bold":["' + document.getElementById('head_fonts_bold').value + '"],';
    var headItalics = '"h_italics":["' + document.getElementById('head_fonts_italics').value + '"],';
    var headUnderline = '"h_underline":["' + document.getElementById('head_fonts_underline').value + '"],';
    
    var subHeadFont = '"s_font":["' + document.getElementById('sub_fonts_font').value + '"],';
    var subHeadSize = '"s_size":["' + document.getElementById('sub_fonts_size').value + '"],';
    var subHeadMargin = '"s_margin":["' + document.getElementById('sub_fonts_bottom_margin').value + '"],';
    var subHeadBold = '"s_bold":["' + document.getElementById('sub_fonts_bold').value + '"],';
    var subHeadItalics = '"s_italics":["' + document.getElementById('sub_fonts_italics').value + '"],';
    var subHeadUnderline = '"s_underline":["' + document.getElementById('sub_fonts_underline').value + '"],';
    
    var bodyFont = '"b_font":["' + document.getElementById('body_fonts_font').value + '"],';
    var bodySize = '"b_size":["' + document.getElementById('body_fonts_size').value + '"],';
    var bodyMargin = '"b_margin":["' + document.getElementById('body_fonts_bottom_margin').value + '"],';
    var bodyBold = '"b_bold":["' + document.getElementById('body_fonts_bold').value + '"],';
    var bodyItalics = '"b_italics":["' + document.getElementById('body_fonts_italics').value + '"],';
    var bodyUnderline = '"b_underline":["' + document.getElementById('body_fonts_underline').value + '"],';
    
    var groupFont = '"g_font":["' + document.getElementById('grp_fonts_font').value + '"],';
    var groupSize = '"g_size":["' + document.getElementById('grp_fonts_size').value + '"],';
    var groupMargin = '"g_margin":["' + document.getElementById('grp_fonts_bottom_margin').value + '"],';
    var groupBold = '"g_bold":["' + document.getElementById('grp_fonts_bold').value + '"],';
    var groupItalics = '"g_italics":["' + document.getElementById('grp_fonts_italics').value + '"],';
    var groupUnderline = '"g_underline":["' + document.getElementById('grp_fonts_underline').value + '"],';
    
    var group = '"field_group":[' + getFieldAttributes('_group') + '],';
    var fieldNames = '"field_names":[' + getFieldAttributes('_field_names') + '],';
    var fieldTypes = '"field_types":[' + getFieldAttributes('_type') + '],';
    var fieldTotals = '"field_totals":[' + getFieldAttributes('_total') + '],';
    var fieldWidths = '"field_widths":[' + getFieldAttributes('_width') + '],';
    var fieldMargins = '"field_margins":[' + getFieldAttributes('_margin') + '],';
    var fieldFilters = '"field_filters":[' + getFieldAttributes('_opt') + '],';
    var fieldFilterOne = '"field_filter_1":[' + getFieldAttributes('_option_1') + '],';
    var fieldFilterTwo = '"field_filter_2":[' + getFieldAttributes('_option_2') + '],';
    var fieldNulls = '"field_nulls":[' + getFieldAttributesChecked('_nulls') + '],';
    
    var fieldPrefixes = '"field_prefixes":[' + getFieldAttributesChecked('_prefix') + '],';
    var fieldSubstitutes = '"field_substitutes":[' + getFieldAttributes('_substitute') + '],';
    var fieldAddons = '"field_addons":[' + getFieldAttributes('_addon') + '],';
    var fields = '"fields":[' + getFieldAttributes('_fields') + ']';
    
    var json_data = '{' + 
        model + name + format + orientation + size + header + wrapCell + cellHeight + wrapReplace +
        subHeading + hideSub + repeatLogos + sortField + orderField + limit + number + overall + 
        headFont + headSize + headMargin + headBold + headItalics + headUnderline + 
        subHeadFont + subHeadSize + subHeadMargin + subHeadBold + subHeadItalics + subHeadUnderline + 
        bodyFont + bodySize + bodyMargin + bodyBold + bodyItalics + bodyUnderline + 
        groupFont + groupSize + groupMargin + groupBold +  groupItalics + groupUnderline + summarize +
        groupsPaging + group + fieldNames + fieldTypes + fieldTotals + fieldWidths + fieldPrefixes + fieldNulls +
        fieldMargins + fieldFilters + fieldFilterOne + fieldFilterTwo + fieldSubstitutes + fieldAddons + fields +
    '}';

    var id = document.getElementById('id').value;
    var product = document.getElementById('product').value;
    var properties = 'menubar=1,location=0,scrollbars=1,width=800,height=600,status=1,resizable=yes,top=0,left=0,dependent=1,alwaysRaised=1';
    
    if (signal === 1)
    {
        if (confirm("Are you sure you want to Overwrite?") === true)
        {
            var url = '../../generate/save/' + encodeURI(json_data) + "/" + id + "/" + product;
            var ret = window.open(url, '_blank', properties);
        }
    }
    
    else
    {
        var new_name = prompt("Enter a name", document.getElementById('name').value + "*");

        if (new_name === document.getElementById('name').value)
        {
            alert("Can not save new report with an existing name");
        }
        
        else if(new_name.search('/') !== -1 || new_name.search('\%') !== -1)
        {
            alert("'/' not allowed in names");
        }

        else
        {
            var url = '../../generate/save/' + encodeURI(json_data) + "/" + new_name + "/" + product;
            var ret = window.open(url, '_blank', properties);
        }
    }
    
    ret.opener = window;
    ret.focus();
}



