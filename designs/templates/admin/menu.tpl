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
      <a class="navbar-brand" href="{$admin_file}">PMS</a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="#">Link</a></li>
        <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"><i class="fa fa-user-circle-o fa-fw fa-lg" aria-hidden="true"></i> Staff <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="{$admin_file}?task=staff_info"><i class="fa fa-users" aria-hidden="true"></i> Infomation</a></li>
            <li><a href="{$admin_file}?task=staff_role"><i class="fa fa-registered" aria-hidden="true"></i> Staff Role</a></li>
            <li><a href="{$admin_file}?task=staff_permission"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> Permission</a></li>
            <li><a href="{$admin_file}?task=staff_function"><i class="fa fa-retweet" aria-hidden="true"></i> Function</a></li>
          </ul>
        </li>
      </ul>
      <form class="navbar-form navbar-left">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-cog fa-lg" aria-hidden="true"></i> Setting <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="{$admin_file}?task=category"><i class="fa fa-bars" aria-hidden="true"></i>&nbsp;{if $multiLang.menu_category}{$multiLang.menu_category}{else}No Translate (Key Lang: menu_category){/if}</a></li>
            <li><a href="{$admin_file}?task=add_language"><i class="fa fa-language" aria-hidden="true"></i>&nbsp; Add Language</a></li>
            <li><a href="{$admin_file}?task=multi_language"><i class="fa fa-language" aria-hidden="true"></i>&nbsp; Translate Language</a></li>
            <li role="separator" class="divider"></li>
            <li class="header_menu"><a><i class="fa fa-sort-alpha-desc" aria-hidden="true"></i>&nbsp; Default Language</a></li>
						{foreach from=$getLanguage item=v}
						<li><a href="{$admin_file}?deflang={$v.lang_name}&amp;lid={$v.id}"> &nbsp;&nbsp;&nbsp;{$v.title} {if $lang_name eq $v.lang_name}<i class="fa fa-check" aria-hidden="true"></i>{/if} </a></li>
						{/foreach}
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-flag" aria-hidden="true"></i> language <span class="caret"></span></a>
          <ul class="dropdown-menu">
            {foreach from=$getLanguage item=v}
            <li><a href="{$admin_file}?lang={$v.lang_name}"><i class="fa fa-angle-right" aria-hidden="true"></i>&nbsp; {$v.title}</a></li>
            {/foreach}
          </ul>
        </li>
        <li><a href="{$admin_file}?task=logout"><i class="fa fa-sign-out fa-lg" aria-hidden="true"></i> Logout</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
