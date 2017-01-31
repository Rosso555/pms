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
        <li class="active">Forgot Password</li>
      </ul>

      {if $error}
        <div class="alert alert-danger" data-dismiss="alert">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {if $error.email eq 1} <i class="fa fa-exclamation"></i>&nbsp;Please enter your email. <br/>{/if}
          {if $error.user_role eq 1}<i class="fa fa-exclamation"></i>&nbsp;Please check patient or psychologist. <br>{/if}
          {if $error.invalid_email eq 1} <i class="fa fa-exclamation"></i>&nbsp;Your email is not valid. <br/>{/if}
          {if $error.wrong_email eq 1} <i class="fa fa-exclamation"></i>&nbsp;Your email is not registed. <br/>{/if}
        </div>
      {/if}
      <div class="panel panel-primary">
        <div class="panel-heading pms_panel_heading">
          <h4 class="panel-title text-center">Forgot Password</h4>
        </div>
        <div class="panel-body">
          <form action="{$index_file}?task=forget" method="post"​ class="form">
            <p style="font-size: 22px; color: #4e4e4e;">Please input your registered email.</p>
            <p class="text-left">
              <label class="radio-inline"><input type="radio" name="user_role" value="1" {if $smarty.session.forget.user_role eq 1}checked{/if}>Patient</label>
              <label class="radio-inline"><input type="radio" name="user_role" value="2" {if $smarty.session.forget.user_role eq 2}checked{/if}>Psychologist</label>
            </p>
            <div class="form-group {if $error.email eq 1}has-error{/if}">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                <input type="text" class="form-control" name="email" placeholder="example@domain.com" value="{$smarty.session.user_login.email}" autofocus>
              </div>
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
</body>
</html>
