{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$mode_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li class="active">Permission Error</li>
</ul>

<div class="panel panel-danger">
  <div class="panel-heading">
    <h3 class="panel-title"><i class="fa fa-warning"></i> Error</h3>
  </div>
  <div class="panel-body" style="padding: 50px 15px 70px 15px; text-align: center; font-size: 20px; color: #a94442;">
    <span style="font-size: 50px;"><i class="fa fa-ban" aria-hidden="true"></i> </span><br>
    You have no permission to perform this task.
  </div>
</div>
{/block}
