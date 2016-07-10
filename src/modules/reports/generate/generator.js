function updateModules()
{
    clearSelectionList("module");
    addModules(document.getElementById("product").value);
    updateColumns();
}

function updateColumns()
{
    click_counter.length > 0 ? clearColumns() : null;
    addColumns(document.getElementById("module").value);   
    document.getElementById("module").value ? createColumnDivisions(1) : null;
}


function createColumnDivisions(index)
{
    click_counter[index] = 0;
    division = document.createElement('div');
    division.style.border = "1px solid black";
    division.style.width = "33%";
    division.style.height = "18vh";
    division.id = "col_div_" + index;
    division.style.float = "left";
    division.onclick = function(){click_counter[index] === 0 ? addModuleDetails(index) : null;};
       
    document.getElementById('column_div').appendChild(division);
}


function addModules(prod) 
{
    for (var key in data)
    {
        if (key === prod)  
        {
            product = prod;
            for (var index in data[key])
            {
                var option = document.createElement("option");
                option.text = index;
                document.getElementById("module").add(option);
            }
        }
    }
}

function addColumns(model)
{
    for (var key in data[product])
    {
        if (key === model)  
        {
            module = model;
            for (var token in data[product][key][1])
            {
                columns.push(token);
            }
        }
    }
}

function addModuleDetails(index)
{
    ++click_counter[index];
    
    document.getElementById('col_div_'+index).appendChild(addGroup(index));
    document.getElementById('col_div_'+index).appendChild(addColumnName(index));
    document.getElementById('col_div_'+index).appendChild(addSelectionList(index));
    document.getElementById('col_div_'+index).appendChild(addWidth(index));
    document.getElementById('col_div_'+index).appendChild(addTotals(index));
    
    addContDate(index);

    addGroupList();
    addClear(index);
    autoAdjustWidths();
    generateReportJson();
    createColumnDivisions(++index);
}

function addSelectionList(index)
{
    var selectList = document.createElement("select");
    selectList.id = "col_sel_" + index;
    selectList.className = "updater";
    selectList.style.width = "90%";
    selectList.style.position = 'static'; 
    selectList.onchange = function(){
        document.getElementById('col_but_' + index).value = 'Add Filter';  
        document.getElementById('col_from_' + index).style.display = 'none';
        document.getElementById('col_to_' + index).style.display = 'none';  
        document.getElementById('col_cont_' + index).style.display = 'none';  
        document.getElementById('col_check_' + index).style.display = 'none';  
        
        document.getElementById('col_tot_' + index).selectedIndex = 0;
        generateReportJson();
    };
    
    //Create and append the options
    for (var i = 0; i < columns.length; i++) {
        var option = document.createElement("option");
        option.value = columns[i];
        option.text = columns[i];
        selectList.appendChild(option);
    }
    
    return selectList;
}

function addColumnName(index)
{
    var input = document.createElement("input");
    input.setAttribute('type', 'text');
    input.style.width = "90%";
    input.setAttribute('placeholder', 'column name');
    input.style.position = 'static';
    input.onkeyup = function(){addSortList(document.getElementById('sort').selectedIndex);};
    input.onkeydown = function(){addSortList(document.getElementById('sort').selectedIndex);};
    input.onchange = function(){addSortList(document.getElementById('sort').selectedIndex);};
    
    input.id = "col_name_" + index;
    return input;
}

function addContDate(index)
{
    var button = document.createElement("input");
    button.setAttribute('type', 'button');
    button.setAttribute('value', 'Add Filter');
    button.id = "col_but_" + index;
    button.style.width = "60%";
    button.style.position = 'static';
    button.onclick = function(){toggleType(index);};
    
    document.getElementById('col_div_'+index).appendChild(button);
    
    var from = document.createElement("input");
    from.setAttribute('type', 'date');
    from.style.width = "75%";
    from.id = "col_from_" + index;
    from.style.display = "none";
    from.style.position = 'static';
    document.getElementById('col_div_'+index).appendChild(from);

    var to = document.createElement("input");
    to.setAttribute('type', 'date');
    to.style.width = "75%";
    to.id = "col_to_" + index;
    to.style.display = "none";
    to.style.position = 'static';
    document.getElementById('col_div_'+index).appendChild(to);
    
    var input = document.createElement("input");
    input.setAttribute('type', 'text');
    input.setAttribute('placeholder', 'contains');
    input.style.width = "75%";
    input.style.display = "none";
    input.style.position = 'static';
    input.id = "col_cont_" + index;
    
    document.getElementById('col_div_'+index).appendChild(input);
     
    var checkBox = document.createElement("input");
    checkBox.setAttribute('type', 'checkbox');
    checkBox.id = "col_check_" + index;
    checkBox.style.display = "none";
    checkBox.style.position = 'static';
    
    document.getElementById('col_div_'+index).appendChild(checkBox);
}

function addWidth(index)
{
    var input = document.createElement("input");
    input.setAttribute('type', 'number');
    input.setAttribute('min', '0');
    input.setAttribute('placeholder', 'width');
    input.style.float = "left";
    input.style.width = "40%";
    
    input.id = "col_width_" + index;
    input.disabled = 'disabled';
    return input;
}

function addTotals(index)
{
    var selectList = document.createElement("select");
    selectList.id = "col_tot_" + index;
    selectList.style.float = "left";
    selectList.style.width = "30%";
    selectList.style.position = 'static';

    var option = document.createElement("option");
    option.value = "";
    option.text = "";
    selectList.appendChild(option);
    
    return selectList;
}

function addClear(index)
{
    var button = document.createElement("input");
    button.setAttribute('type', 'button');
    button.setAttribute('value', 'Clear');
    button.id = "col_clear_" + index;
    button.style.width = "45%";
    button.style.position = 'absolute'; 
    button.style.fontWeight = 'bold';
    button.style.bottom = '0';
    button.style.right = '0'; 
    button.style.borderRadius = '50px';
    button.onclick = function(){clearColumns(index);};
    document.getElementById('col_div_'+index).style.position = 'relative';
    document.getElementById('col_div_'+index).appendChild(button);
}

function addGroup(index)
{
    var selectList = document.createElement("select");
    selectList.setAttribute('type', 'checkbox');
    selectList.id = "col_grp_" + index;
    selectList.onchange = function(){autoAdjustWidths(index);};
    
    var option = document.createElement("option");
    option.value = "";
    option.text = "";
    selectList.appendChild(option);
    
    return selectList;
}

function addGroupList()
{
    for(var index = 0; index < click_counter.length; index ++)
    {
        if (!document.getElementById('col_grp_' + index)) continue;
    
        var counter = 0;
        clearSelectionList('col_grp_' + index);
        for(var key = 0; key < click_counter.length; key ++)
        {
            if (!document.getElementById('col_grp_' + key)) continue;
            
            ++counter;
            var option = document.createElement("option");
            option.value = counter;
            option.text = counter;
            document.getElementById('col_grp_' + index).appendChild(option);
        }
    }
}

function addSortList(selected)
{
    clearSelectionList("sort");
    
    for(var key = 1; key < click_counter.length - 1; key ++)
    {
        if (!document.getElementById('col_div_' + key) || !document.getElementById('col_name_' + key).value) continue;
    
        var option = document.createElement("option");
        option.value = document.getElementById('col_sel_' + key).value;
        option.text = document.getElementById('col_name_' + key).value;
        document.getElementById('sort').appendChild(option);
    }
    
    document.getElementById('sort').selectedIndex = selected;
}

function autoAdjustWidths(index)
{
    var width = 0;
    var actuals = 0;
    var numbered = document.getElementById('number').checked ? 4 : 0;
    index ? document.getElementById('col_width_' + index).value = '' : null;
    
    for(var key = 1; key < click_counter.length; key ++)
    {
        if (!document.getElementById('col_width_' + key) || document.getElementById('col_grp_' + key).value !== '') continue;
        ++ actuals;
    }
    
    for(var key = 1; key < click_counter.length; key ++)
    {
        if (!document.getElementById('col_width_' + key) || document.getElementById('col_grp_' + key).value !== '') continue;
        document.getElementById('col_width_' + key).value = Number((100 - numbered) / (actuals));
        width += Number(document.getElementById('col_width_' + key).value);
    }
    
    document.getElementById('width').value = Number(width + numbered).toFixed(0);
    generateReportJson();
}

function clearColumns(index)
{
    if(index)
    {
        document.getElementById('column_div').removeChild(document.getElementById('col_div_' + index));
    }
    
    else
    {
        columns = [];
        for(var key = 1; key < click_counter.length; key ++)
        {
            if (!document.getElementById('col_div_' + key)) continue;
            document.getElementById('column_div').removeChild(document.getElementById('col_div_' + key));
        }
        click_counter = [];
    }
    
    addGroupList();
    addSortList(0);
    autoAdjustWidths();
    generateReportJson();
}


function generateReportJson()
{
    var model = columns[0] ?  '"model":["' + data[product][module][0] + '"],' : '';
    var name = '"name":["' + document.getElementById('report_name').value + '"],';
    var header = '"heading":["' + document.getElementById('heading').value + '"],';
    var sortField = '"sort_field":["' + document.getElementById('sort').value + '"],';
    var orderField = '"order_field":["' + document.getElementById('order').value + '"],';
    var limit = '"limit":["' + document.getElementById('limit').value + '"],';
    var numbering = '"numbering":["' + document.getElementById('number').checked + '"],';
    
    var fields = '"fields":[' + getFieldAttributes('col_sel_') + '],';
    var group = '"field_group":[' + getFieldAttributes('col_grp_') + '],';
    var fieldNames = '"field_names":[' + getFieldAttributes('col_name_') + '],';
    var fieldTypes = '"field_types":[' + getFieldTypes() + '],';
    var fieldTotals = '"field_totals":[' + getFieldAttributes('col_tot_') + '],';
    var fieldWidths = '"field_widths":[' + getFieldAttributes('col_width_') + '],';
    var fieldFilters = '"field_filters":[' + getFieldFilters() + ']';
    
    document.getElementById('preview').innerHTML = '{' + model + name + header + sortField + group + numbering +
            orderField + limit + fields + fieldNames + fieldTypes + fieldTotals + fieldWidths + fieldFilters +
    '}';
    document.getElementById('report_json').value = '{' + model + name + header + sortField + group + numbering +
            orderField + limit + fields + fieldNames + fieldTypes + fieldTotals + fieldWidths + fieldFilters +
    '}';
}


function getFieldAttributes(id)
{
    var fields = "";
    for(var key = 1; key < click_counter.length; key ++)
    {
        if (!document.getElementById(id + key)) continue;
        fields = fields === "" ? "" : fields + "," ;
        var data = document.getElementById(id + key).value;
        fields += '"' + data + '"';
    }
    
    return fields;
}

function getFieldTypes()
{
    var fields = "";
    for(var key = 1; key < click_counter.length; key ++)
    {
        if (!document.getElementById('col_sel_' + key)) continue;
        fields = fields === "" ? "" : fields + "," ;
        var type = data[product][module][1][document.getElementById('col_sel_' + key).value].type;
        fields += '"' + type + '"';
    }
    
    return fields;
}

function getFieldFilters()
{
    var fieldFilters = "";
    for(var key = 1; key < click_counter.length; key ++)
    {
        if (!document.getElementById('col_but_' + key)) continue;
        fieldFilters = fieldFilters === "" ? "" : fieldFilters + "," ;
        
        if(document.getElementById('col_but_' + key).value === "Remove Filter")
        {
            getDisplay('col_cont_'+key) ? fieldFilters += '"' + document.getElementById('col_cont_' + key).value + '"' : null;
            getDisplay('col_check_'+key) ? fieldFilters += '"' + document.getElementById('col_check_' + key).checked + '"' : null;
            getDisplay('col_from_'+key) ? 
                fieldFilters += '"' + document.getElementById('col_from_' + key).value + "-" + document.getElementById('col_to_' + key).value +'"' : null;
        }
    }
    
    return fieldFilters;
}

function getDisplay(element)
{
    return document.getElementById(element).style.display === 'block' ? true : false;
}


function toggler()
{
    for(var key = 1; key < click_counter.length; key ++)
    {
        if (!document.getElementById('col_sel_' + key)) continue;
        
        var type = data[product][module][1][document.getElementById('col_sel_' + key).value].type;
        var totals = document.getElementById('col_tot_' + key).selectedIndex;

        document.getElementById('col_tot_' + key).remove(2);
        document.getElementById('col_tot_' + key).remove(1);
        
        if(type === "double" || type === "integer")
        {
            var option = document.createElement("option");
            option.value = "false";
            option.text = "False";
            document.getElementById('col_tot_' + key).appendChild(option);

            var option = document.createElement("option");
            option.value = "true";
            option.text = "True";
            document.getElementById('col_tot_' + key).appendChild(option);
        }
        
        document.getElementById('col_tot_' + key).selectedIndex = totals;
    }
}

function toggleType(index)
{
    var type = data[product][module][1][document.getElementById('col_sel_' + index).value].type;
    
    var done = false;
    if(document.getElementById('col_but_'+index).value === "Add Filter")
    {
        document.getElementById('col_but_'+index).value = "Remove Filter";
        
        if(type === 'boolean')
        {
            document.getElementById('col_from_'+index).style.display = 'none';
            document.getElementById('col_to_'+index).style.display = 'none';  
            document.getElementById('col_cont_'+index).style.display = 'none';  
            document.getElementById('col_check_'+index).style.display = 'block';
            done = true;
        }

        else if(type === 'date' || type === 'datetime')
        {
            document.getElementById('col_from_'+index).style.display = 'block';
            document.getElementById('col_to_'+index).style.display = 'block';  
            document.getElementById('col_cont_'+index).style.display = 'none';  
            document.getElementById('col_check_'+index).style.display = 'none'; 
            done = true;
        }

        else if(type === 'string' || type === 'text' || type === 'double' || type === "integer")
        {
            document.getElementById('col_from_'+index).style.display = 'none';
            document.getElementById('col_to_'+index).style.display = 'none';  
            document.getElementById('col_cont_'+index).style.display = 'block';  
            document.getElementById('col_check_'+index).style.display = 'none'; 
            done = true;
        }
    }
    
    if(!done)
    {
        document.getElementById('col_but_'+index).value = "Add Filter";
        document.getElementById('col_from_'+index).style.display = 'none';
        document.getElementById('col_to_'+index).style.display = 'none';  
        document.getElementById('col_cont_'+index).style.display = 'none';  
        document.getElementById('col_check_'+index).style.display = 'none';  
    }
    
    generateReportJson();
}
