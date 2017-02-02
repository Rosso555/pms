<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content=""/>
  <meta name="keywords" content="Psychology Management Syste"/>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="/css/index_style.css" type="text/css"/>
  <title>PMS</title>
</head>
<body>
<div class="container" style="margin-top: 80px;">
  <div class="row">
    <div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
      <ul class="breadcrumb">
        <li><a href="{$index_file}"><span class="label label-success">PMS</span></a></li>
        <li class="active">New</li>
      </ul>

      <div class="panel panel-primary">
        <div class="panel-heading pms_panel_heading">
          <h4 class="panel-title text-center">New Password</h4>
        </div>
        <div class="panel-body">
          <form action="{$index_file}?task=new_password&amp;secretkey={$smarty.get.secretkey}&amp;user_role={$smarty.get.user_role}&amp;id={$smarty.get.id}" method="post"​ class="form">
            <p style="font-size: 22px; color: #4e4e4e;">Please input your new password.</p>
            <!-- <p class="text-left">
              <label class="radio-inline"><input type="radio" name="user_role" value="1" {if $smarty.session.forget.user_role eq 1}checked{/if}>Patient</label>
              <label class="radio-inline"><input type="radio" name="user_role" value="2" {if $smarty.session.forget.user_role eq 2}checked{/if}>Psychologist</label>
            </p> -->
            <input type="hidden" name="id" value="{$smarty.get.id}">
            <input type="hidden" name="secretkey" value="{$smarty.get.secretkey}">
            <input type="hidden" name="user_role" value="{$smarty.get.user_role}">

            <div class="form-group" id="error_pwd">
              <label for="pwd"><span style="color:red">*</span> Password:</label>
              <span style="color:red" id="txt_error_pwd">{if $error.password}Please enter password !!!{/if} {if $error.not_match_password}Your password do not match. Please try again.{/if} {if $error.less_password_not_letter}Your password less than 8 or not with number and letter.{/if}</span>

              <input type="password" class="form-control" id="pwd" name="password" onkeyup="checkPassword('pwd');" value="{$smarty.session.new_password.password}">
              <span style="color:red">More than 8 characters with number and letter</span>
            </div>
            <div class="form-group" id="error_re_pwd">
              <label for="re_pwd"><span style="color:red">*</span> Re-Password:</label>
              <span style="color:red" id="txt_error_re_pwd">{if $error.re_password}Please enter re_password !!!{/if}</span>

              <input type="password" class="form-control" id="re_pwd" name="re_password" onchange="checkPassword('re_pwd');">
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><i class="fa fa-sign-in"></i> SUBMIT</button>
            </div>
          </form>

        </div>
      </div>
      <span style="font-size: 13px" class="text-muted"> © {$smarty.now|date_format:"%Y"} PSYCHOLOGY MANAGEMENT SYSTEM.</span>
    </div>
  </div>
</div>

<script>

  function checkPassword(value){
    var pwd = $("#pwd").val();
    var re_pwd = $("#re_pwd").val();

    if(value == 'pwd'){

      if(pwd == ''){
        $("#error_pwd").attr("class", "form-group has-error");
        $("#txt_error_pwd").text("Please enter password !!!");
      }else {
        $("#error_pwd").attr("class", "form-group has-success");
        $("#txt_error_pwd").text("");
      }

    }else {

      if(pwd != '' && re_pwd != '' && pwd != re_pwd){
        $("#error_re_pwd").attr("class", "form-group has-error");
        $("#txt_error_re_pwd").text("Your password do not match. Please try again.");
      }else {

        if(re_pwd == ''){
          $("#error_re_pwd").attr("class", "form-group has-error");
          $("#txt_error_re_pwd").text("Please enter re-password !!!");
        }else {
          $("#error_re_pwd").attr("class", "form-group has-success");
          $("#txt_error_re_pwd").text("");
        }

      }

    }

  }

$("form").submit(function( event ) {
  var pwd     = $("#pwd").val();
  var re_pwd  = $("#re_pwd").val();

  if(pwd == ''){
    $("#error_pwd").attr("class", "form-group has-error");
    $("#txt_error_pwd").text("Please enter password !!!");
  }else {
    $("#error_pwd").attr("class", "form-group has-success");
    $("#txt_error_pwd").text("");
  }

  if(re_pwd == ''){
    $("#error_re_pwd").attr("class", "form-group has-error");
    $("#txt_error_re_pwd").text("Please enter re-password !!!");
  }else {
    $("#error_re_pwd").attr("class", "form-group has-success");
    $("#txt_error_re_pwd").text("");
  }

  if(pwd != '' && re_pwd != ''){

    if(pwd != re_pwd){
      $("#error_re_pwd").attr("class", "form-group has-error");
      $("#txt_error_re_pwd").text("Your password do not match. Please try again.");
    }else {
      $("#error_re_pwd").attr("class", "form-group");
      $("#txt_error_re_pwd").text("");
      //if ture is submit
      return;
    }

  }

  event.preventDefault();
});


</script>

</body>
</html>
