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
  <title>PMS Register</title>
</head>
<body>
<div class="container" style="margin-top: 22px;">
  <div class="row">
    <div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
      <ul class="breadcrumb">
        <li><a href="{$index_file}?task=login"><span class="label label-success">PMS</span></a></li>
        <li class="active">Register</li>
      </ul>

      <div class="panel panel-primary">
        <div class="panel-heading pms_panel_heading">
          <h4 class="panel-title text-center">Psychologist Register</h4>
        </div>
        <div class="panel-body">
          <form action="{$index_file}?task=user_register" method="post">
            <div class="form-group" id="error_user">
              <label for="user"><span style="color:red">*</span> Username:</label>
              <span style="color:red" id="txt_error_user">{if $error.username}Please enter username !!!{/if}</span>

              <input type="text" class="form-control" id="user" name="username" value="{$smarty.session.user_register.username}">
            </div>
            <div class="form-group" id="error_pwd">
              <label for="pwd"><span style="color:red">*</span> Password:</label>
              <span style="color:red" id="txt_error_pwd">{if $error.password}Please enter password !!!{/if} {if $error.not_match_password}Your password do not match. Please try again.{/if} {if $error.less_password}Your password less than 8 or not with number and letter.{/if}</span>

              <input type="password" class="form-control" id="pwd" name="password" onkeyup="checkPassword('pwd');" value="{$smarty.session.user_register.password}">
              <span style="color:red">More than 8 characters with number and letter</span>
            </div>
            <div class="form-group" id="error_re_pwd">
              <label for="re_pwd"><span style="color:red">*</span> Re-Password:</label>
              <span style="color:red" id="txt_error_re_pwd">{if $error.re_password}Please enter re_password !!!{/if}</span>

              <input type="password" class="form-control" id="re_pwd" name="re_password" onchange="checkPassword('re_pwd');">
            </div>
            <div class="form-group" id="error_email">
              <label for="email"><span style="color:red">*</span> Email:</label>
              <span style="color:red" id="txt_error_email">{if $error.email}Please enter email !!!{/if} {if $error.invalid_email}Your email is not valid !!!{/if} {if $error.exist_email}Your email is existed !!!{/if}</span>

              <input type="email" class="form-control" id="email" name="email" placeholder="example@domain.com" value="{$smarty.session.user_register.email}">
            </div>
            <div class="form-group" id="error_re_email">
              <label for="email"><span style="color:red">*</span> Re-Email:</label>
              <span style="color:red" id="txt_error_re_email">{if $error.re_email}Please enter re-email !!!{/if} {if $error.email_not_match}Your email does not match !!!{/if} {if $error.exist_email}Your email is existed !!!{/if}</span>

              <input type="email" class="form-control" id="re_email" name="re_email" placeholder="example@domain.com" value="{$smarty.session.user_register.re_email}" onchange="checkEmail();">
            </div>
            <div class="form-group" id="error_job">
              <label for="job"><span style="color:red">*</span> Job:</label>
              <span style="color:red" id="txt_error_job">{if $error.job}Your email is job !!!{/if}</span>

              <input type="text" class="form-control" id="job" name="job" value="{$smarty.session.user_register.job}">
            </div>
            <div class="form-group" id="error_address">
              <label for="address"><span style="color:red">*</span> Address:</label>
              <span style="color:red" id="txt_error_address">{if $error.address}Your email is address !!!{/if}</span>

              <textarea class="form-control" rows="4" id="address" name="address">{$smarty.session.user_register.address}</textarea>
            </div>
            <a href="{$index_file}?task=login" class="btn btn-warning"><i class="fa fa-step-backward" aria-hidden="true"></i> Back</a>
            <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Submit</button>
          </form>
        </div>
      </div>
      <span style="font-size: 13px" class="text-muted"> © {$smarty.now|date_format:"%Y"} PSYCHOLOGY MANAGEMENT SYSTEM.</span>
      <br><br>
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
function checkEmail(){
        var email = $("#email").val();
        var re_email = $("#re_email").val();
        if (email == '') {
                $("#error_email").attr("class", "form-group has-error");
                $("#txt_error_email").text("Please enter email !!!");
        }else {
                $("#error_email").attr("class", "form-group has-success");
                $("#txt_error_email").text("");
        }
        if (email != '' && re_email != '' && email != re_email) {
                $("#error_re_email").attr("class", "form-group has-error");
                $("#txt_error_re_email").text("Your email do not match. Please try again.");
        }else {
                if(re_email == ''){
                  $("#error_re_email").attr("class", "form-group has-error");
                  $("#txt_error_re_email").text("Please enter re-email !!!");
                }else {
                  $("#error_re_email").attr("class", "form-group has-success");
                  $("#txt_error_re_email").text("");
                }
        }
}
$("form").submit(function( event ) {
  var user    = $("#user").val();
  var pwd     = $("#pwd").val();
  var re_pwd  = $("#re_pwd").val();
  var email   = $("#email").val();
  var re_email= $("#re_email").val();
  var job     = $("#job").val();
  var address = $("#address").val();

  if(user == ''){
    $("#error_user").attr("class", "form-group has-error");
    $("#txt_error_user").text("Please enter username !!!");
  }else {
    $("#error_user").attr("class", "form-group has-success");
    $("#txt_error_user").text("");
  }

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

  if(email == ''){
    $("#error_email").attr("class", "form-group has-error");
    $("#txt_error_email").text("Please enter email !!!");
  }else {
    $("#error_email").attr("class", "form-group has-success");
    $("#txt_error_email").text("");
  }
  if(re_email == ''){
    $("#error_re_email").attr("class", "form-group has-error");
    $("#txt_error_re_email").text("Please enter re-email !!!");
  }else {
    $("#error_re_email").attr("class", "form-group has-success");
    $("#txt_error_re_email").text("");
  }

  if(job == ''){
    $("#error_job").attr("class", "form-group has-error");
    $("#txt_error_job").text("Please enter job !!!");
  }else {
    $("#error_job").attr("class", "form-group has-success");
    $("#txt_error_job").text("");
  }

  if(address == ''){
    $("#error_address").attr("class", "form-group has-error");
    $("#txt_error_address").text("Please enter address !!!");
  }else {
    $("#error_address").attr("class", "form-group has-success");
    $("#txt_error_address").text("");
  }

  if(user != '' && pwd != '' && re_pwd != '' && email != '' && job != '' && address !=''){

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
