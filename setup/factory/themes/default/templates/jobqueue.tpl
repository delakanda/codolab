<div id="job-queue-status">

</div>
<script type='text/javascript'>
var path = "{$path}";
var code = "{$code}";
{literal}
$(function (){
	$('#job-queue-status').load(path + "/status/" + code);
	setInterval(
		function(){
		    $('#job-queue-status').load(path + "/status/" + code);
		},
		3000
	);
});
{/literal}
</script>