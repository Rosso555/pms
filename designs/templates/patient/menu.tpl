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
      <a class="navbar-brand" href="{$patient_file}">PMS</a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="{$patient_file}?task=test_uncompleted"><i class="fa fa-question-circle" aria-hidden="true"></i> Uncompleted <span class="badge" style="background-color: #fff; color: #278ed2;" id="unCompleted">{$unCompleted}</span></a></li>
        <!-- <li><a href="{$patient_file}?task=test_response"><i class="fa fa-recycle" aria-hidden="true"></i> Test Response</a></li> -->
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-language" aria-hidden="true"></i> {if $multiLang.menu_language}{$multiLang.menu_language}{else}No Translate (Key Lang: menu_language){/if} <span class="caret"></span></a>
          <ul class="dropdown-menu">
            {foreach from=$getLanguage item=v}
            <li><a href="{$patient_file}?lang={$v.lang_name}"><i class="fa fa-angle-right" aria-hidden="true"></i>&nbsp; {$v.title}</a></li>
            {/foreach}
          </ul>
        </li>
        <li class="dropdown">
	        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user-o" aria-hidden="true"></i>
						{$smarty.session.is_patient_username} <span class="caret"></span>
					</a>
	        <ul class="dropdown-menu">
						<li><a href="#"><i class="fa fa-key" aria-hidden="true"></i> {$smarty.session.is_patient_code}</a></li>
            <li><a href="{$patient_file}?task=logout"><i class="fa fa-sign-out" aria-hidden="true"></i> logout</a></li>
					</ul>
				</li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
