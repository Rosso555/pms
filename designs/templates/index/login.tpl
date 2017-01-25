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
        <li class="active">User</li>
      </ul>

      {if $error}
        <div class="alert alert-danger" data-dismiss="alert">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {if $error.username eq 1} <i class="fa fa-exclamation"></i>&nbsp;Please enter your name. <br />{/if}
          {if $error.password eq 1} <i class="fa fa-exclamation"></i>&nbsp;Please enter your password. <br />{/if}
          {if $error.user_role eq 1}<i class="fa fa-exclamation"></i>&nbsp;Please check patient or psychologist. <br>{/if}
          {if $error.login_error eq 1} <i class="fa fa-exclamation"></i>&nbsp;Wrong name or password or no permission. <br />{/if}
        </div>
      {/if}
      <div class="panel panel-primary">
        <div class="panel-heading pms_panel_heading">
          <h4 class="panel-title text-center">USER LOGIN</h4>
        </div>
        <div class="panel-body">
          <form action="{$staff_file}?task=login" method="post"​ class="form">
            <p class="text-left">
              <label class="radio-inline"><input type="radio" name="user_role" value="1" {if $smarty.session.user_login.user_role eq 1}checked{/if}>Patient</label>
              <label class="radio-inline"><input type="radio" name="user_role" value="2" {if $smarty.session.user_login.user_role eq 2}checked{/if}>Psychologist</label>
            </p>
            <div class="form-group {if $error.username eq 1}has-error{/if}">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                <input type="text" class="form-control" name="username" placeholder="Username" value="{$smarty.session.user_login.username}" autofocus>
              </div>
            </div>
            <div class="form-group {if $error.password eq 1}has-error{/if}">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
                <input type="password" class="form-control" name="password" placeholder="Password" value="{$smarty.session.user_login.password}">
              </div>
            </div>
            <p><small><i class="fa fa-info-circle" aria-hidden="true"></i> <a href="{$index_file}?task=forget">Forgot Password.</a></small></p>
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-block" style="border-radius: 0px;"><i class="fa fa-sign-in"></i> LOGIN</button>
            </div>
          </form>
          <br>
          <small>Don't have a PMS psychologist account yet? <a href="{$index_file}?task=user_register">Create your account now.</a></small>
          <br><br>
        </div>
      </div>
      <span style="font-size: 13px" class="text-muted"> © {$smarty.now|date_format:"%Y"} PSYCHOLOGY MANAGEMENT SYSTEM.</span>
    </div>
  </div>
</div>
</body>
</html>
