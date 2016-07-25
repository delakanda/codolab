function process(url, browser, locate)
{
    browser = browser ? browser : null;
    locate = locate ? locate : '_self';
    
    var value = url.split("/");
    
    for(var i = 0; i < value.length; i ++)
    {
        if (value[i] === "true")
        {
            if (confirm("Are you sure you want to CONTINUE?") === true)
            {
                break;
            }
            
            else
            {
                return;
            }
        }
            
    }  
    newwindow = window.open(url, locate, browser);
}

function CurrencyFormatted(amount)
{
	var i = parseFloat(amount);
	if(isNaN(i)) { i = 0.00; }
	var minus = '';
	if(i < 0) { minus = '-'; }
	i = Math.abs(i);
	i = parseInt((i + .005) * 100);
	i = i / 100;
	s = new String(i);
	if(s.indexOf('.') < 0) { s += '.00'; }
	if(s.indexOf('.') === (s.length - 2)) { s += '0'; }
	s = minus + s;
	return s;
}

function CommaFormatted(amount)
{
	var delimiter = ","; // replace comma if desired
	amount = new String(amount);
	var a = amount.split('.',2);
	var d = a[1];
	var i = parseInt(a[0]);
	if(isNaN(i)) { return ''; }
	var minus = '';
	if(i < 0) { minus = '-'; }
	i = Math.abs(i);
	var n = new String(i);
	var a = [];
	while(n.length > 3)
	{
            var nn = n.substr(n.length-3);
            a.unshift(nn);
            n = n.substr(0,n.length-3);
	}
	if(n.length > 0) { a.unshift(n); }
	n = a.join(delimiter);
	if(d.length < 1) { amount = n; }
	else { amount = n + '.' + d; }
	amount = minus + amount;
	return amount;
}

function removeCommas(amount)
{
    amount.replace(/\,/g,"");
    return amount.replace(/\,/g,"");
}