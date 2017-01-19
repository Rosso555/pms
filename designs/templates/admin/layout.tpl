<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content=""/>
<meta name="keywords" content=""/>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="/css/admin_style.css" type="text/css"/>
<link rel="stylesheet" href="/css/style_select2.css" type="text/css"/>
<title>PMS-ADMIN</title>
</head>
<body>
{if $mode eq "admin" }{include file="admin/menu.tpl" }{/if}
<div class="container">
	<div class="row">
		<div class="col-md-12">
			{block name="main"}
			{/block}
		</div>
	</div>
</div>
{include file="common/footer.tpl"}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script type="text/javascript">
	function readURL(input) {
		if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#blah').attr('src', e.target.result);
				}
			reader.readAsDataURL(input.files[0]);
		}
	}
</script>
{block name="javascript"}
{/block}
<script>
$(document).ready(function(){
		// tooltip
    $('[data-toggle1="tooltip"]').tooltip();
		// select2 form
		$(".select2").select2();

		$(".select2_second_topic").select2({
			  placeholder: "Analysis Topic",

			});
});
function NumAndTwoDecimals(e , field)
{
  var val = field.value;
  var reg = /^([0-9]+[\.]?[0-9]?[0-9]?|[0-9]+)/g;
  val = reg.exec(val);
  if (val) {
    field.value = val[0];
  }
  else
  {
    field.value = "";
  }
}
</script>
</body>
</html>