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
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="{$index_file}">PMS</a>
      </div>
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-language" aria-hidden="true"></i> {if $multiLang.menu_language}{$multiLang.menu_language}{else}No Translate (Key Lang: menu_language){/if} <span class="caret"></span></a>
            <ul class="dropdown-menu">
              {foreach from=$getLanguage item=v}
              <li><a href="{$index_file}?task=completed&amp;lang={$v.lang_name}"><i class="fa fa-angle-right" aria-hidden="true"></i>&nbsp; {$v.title}</a></li>
              {/foreach}
            </ul>
          </li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>

  <div class="container" style="margin-top: 80px;">
    <div class="jumbotron text-center" style=" background-color: #fcfcfc; color: #606060;">
      <i class="fa fa-check-circle" style="color: green; font-size:40px;"></i>
      {if $smarty.cookies.completed eq 'forgot_password'}
        <h2>Thank you!</h2>
        <p>Please check that email for reset password.</p>
        <a href="http://gmail.com/" class="btn btn-primary btn-sm" target="_blank">Click Here <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
      {/if}
      {if $smarty.cookies.completed eq 'new_password'}
        <h2>Thank you!</h2>
        <p>Your password has been change.</p>
        <a href="{$index_file}" class="btn btn-primary btn-sm">Login <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
      {/if}
      {if $smarty.cookies.completed eq 'user_register'}
        <h2>Thank you for registration!</h2>
        <p>We will send your register information to user via email. Please check that email for confirm email.</p>
        <a href="http://gmail.com/" class="btn btn-primary btn-sm" target="_blank">Click Here <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
      {/if}
      {if !$smarty.cookies.completed}
        <h2>Thank you!</h2>
        <a href="{$index_file}" class="btn btn-primary btn-sm">Login <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
      {/if}
    </div>

    <span style="font-size: 13px" class="text-muted"> © {$smarty.now|date_format:"%Y"} PSYCHOLOGY MANAGEMENT SYSTEM.</span>
  </div>
</body>
</html>
