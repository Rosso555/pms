<!DOCTYPE html>
<html lang="en">
<head>
  <title>PMS-LOGIN</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>
<body style="background-color: #DEDEDE;">
  <div class="container" style="margin-top: 80px;">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
        {if $error}
          <div class="alert alert-danger">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {if $error.username eq 1} <i class="fa fa-exclamation"></i>&nbsp;Please enter your username. <br />{/if}
            {if $error.password eq 1} <i class="fa fa-exclamation"></i>&nbsp;Please enter your password. <br />{/if}
            {if $error.login eq 1} <i class="fa fa-exclamation"></i>&nbsp;Wrong username or password or no permission. <br />{/if}
          </div>
        {/if}
        <div class="panel panel-primary">
          <div class="panel-heading pms_panel_heading">
            <h4 class="panel-title text-center" style="padding: 5px;">PMS - ADMIN LOGIN</h4>
          </div>
          <div class="panel-body">
            <form action="{$admin_file}?task=login" method="post"​ class="form">
              <div class="form-group">
                <label for="username"><span style="color:red">*</span> Username:</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                  <input type="text" class="form-control" name="username" placeholder="Enter your username" value="{$smarty.session.admin.username}" autofocus>
                </div>
              </div>
              <div class="form-group">
                <label for="username"><span style="color:red">*</span> Password:</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
                  <input type="password" class="form-control" name="password" placeholder="Password" value="">
                </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-info"><i class="fa fa-sign-in"></i> LOGIN</button>
              </div>
            </form>
          </div>
        </div>
        <span style="font-size: 13px" class="text-muted"> © {$smarty.now|date_format:"%Y"} PSYCHOLOGY MANAGEMENT SYSTEM.</span>
      </div>
    </div>
  </div>
  <!-- <div id="particles-js"></div> -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</body>
</html>
