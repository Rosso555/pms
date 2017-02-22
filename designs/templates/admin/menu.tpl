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
        <!-- <li><a href="#">Link</a></li> -->
        <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"><i class="fa fa-user-circle-o fa-fw fa-lg" aria-hidden="true"></i> {if $multiLang.menu_staff}{$multiLang.menu_staff}{else}No Translate (Key Lang:menu_staff){/if} <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="{$admin_file}?task=staff_info"><i class="fa fa-users" aria-hidden="true"></i> {if $multiLang.menu_information}{$multiLang.menu_information}{else}No Translate (Key Lang:menu_information){/if}</a></li>
            <li><a href="{$admin_file}?task=staff_role"><i class="fa fa-registered" aria-hidden="true"></i> {if $multiLang.menu_staff_role}{$multiLang.menu_staff_role}{else}No Translate (Key Lang:menu_staff_role){/if}</a></li>
            <li><a href="{$admin_file}?task=staff_permission"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> {if $multiLang.menu_permission}{$multiLang.menu_permission}{else}No Translate (Key Lang:menu_permission){/if}</a></li>
            <li><a href="{$admin_file}?task=staff_function"><i class="fa fa-retweet" aria-hidden="true"></i> {if $multiLang.menu_function}{$multiLang.menu_function}{else}No Translate (Key Lang:menu_function){/if}</a></li>
          </ul>
        </li>
        <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"><i class="fa fa-users" aria-hidden="true"></i> User <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="{$admin_file}?task=psychologist"><i class="fa fa-user-plus" aria-hidden="true"></i> Psychologist</a></li>
            <li><a href="{$admin_file}?task=patient"><i class="fa fa-user-md" aria-hidden="true"></i> Patient</a></li>
          </ul>
        </li>
        <li><a href="{$admin_file}?task=question"><i class="fa fa-question-circle" aria-hidden="true"></i> {if $multiLang.menu_question}{$multiLang.menu_question}{else}No Translate (Key Lang: menu_question){/if}</a></li>
        <li><a href="{$admin_file}?task=test"><i class="fa fa-clone fa-fw"></i> {if $multiLang.menu_test}{$multiLang.menu_test}{else}No Translate (Key Lang: menu_test){/if}</a></li>
        <li><a href="{$admin_file}?task=test_question"><i class="fa fa-clone fa-fw"></i> {if $multiLang.menu_test_question}{$multiLang.menu_test_question}{else}No Translate (Key Lang: menu_test_question){/if}</a></li>
        <li><a href="{$admin_file}?task=test_group"><i class="fa fa-clone fa-fw"></i> Test Group</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown" id="animated">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-cog fa-lg" aria-hidden="true"></i> {if $multiLang.menu_setting}{$multiLang.menu_setting}{else}No Translate (Key Lang:menu_setting){/if} <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="{$admin_file}?task=category"><i class="fa fa-bars" aria-hidden="true"></i>&nbsp;{if $multiLang.menu_category}{$multiLang.menu_category}{else}No Translate (Key Lang: menu_category){/if}</a></li>
            <li><a href="{$admin_file}?task=add_language"><i class="fa fa-language" aria-hidden="true"></i>&nbsp; {if $multiLang.menu_add_language}{$multiLang.menu_add_language}{else}No Translate (Key Lang: menu_add_language){/if}</a></li>
            <li><a href="{$admin_file}?task=multi_language"><i class="fa fa-language" aria-hidden="true"></i>&nbsp; {if $multiLang.menu_translate_language}{$multiLang.menu_translate_language}{else}No Translate (Key Lang:menu_translate_language){/if}</a></li>
            <li role="separator" class="divider"></li>
            <li class="header_menu"><a><i class="fa fa-sort-alpha-desc" aria-hidden="true"></i>&nbsp; {if $multiLang.menu_default_language}{$multiLang.menu_default_language}{else}No Translate (Key Lang:menu_default_language){/if}</a></li>
						{foreach from=$getLanguage item=v}
						<li><a href="{$admin_file}?deflang={$v.lang_name}&amp;lid={$v.id}"> &nbsp;&nbsp;&nbsp;{$v.title} {if $lang_name eq $v.lang_name}<i class="fa fa-check" aria-hidden="true"></i>{/if} </a></li>
						{/foreach}
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-flag" aria-hidden="true"></i> {if $multiLang.menu_language}{$multiLang.menu_language}{else}No Translate (Key Lang: menu_language){/if} <span class="caret"></span></a>
          <ul class="dropdown-menu">
            {foreach from=$getLanguage item=v}
            <li><a href="{$admin_file}?lang={$v.lang_name}"><i class="fa fa-angle-right" aria-hidden="true"></i>&nbsp; {$v.title}</a></li>
            {/foreach}
          </ul>
        </li>
        <li><a href="{$admin_file}?task=logout"><i class="fa fa-sign-out fa-lg" aria-hidden="true"></i> {if $multiLang.menu_logout}{$multiLang.menu_logout}{else}No Translate (Key Lang:menu_logout){/if}</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
