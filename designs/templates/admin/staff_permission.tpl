{extends file="admin/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file_name}"><i class="fa fa-fw fa-home"></i></a></li>
  <li class="active">{if $multiLang.text_staff_permision_header}{$multiLang.text_staff_permision_header}{else}No Translate (Key Lang:text_staff_permision_header){/if}</li>
</ul>
{if $error.exist_save eq 1}
  <div class="alert alert-danger alert-dismissible"  id="{if $error.exist_save eq 1}flash{/if}">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
    <strong>warning!</strong>Your have assign permisstion already please exit!
  </div>
{/if}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_staff_permision_header}{$multiLang.text_staff_permision_header}{else}No Translate (Key Lang:text_staff_permision_header){/if}</h4></div>
  <div class="panel-body">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                  <i class="fa fa-plus-circle"></i> {if $multiLang.text_new_staff_permission}{$multiLang.text_new_staff_permission}{else}No Translate (Key Lang:text_new_staff_permission){/if}
                </button>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="form-group">
                  <div class="col-md-5">
                    <select class="form-control" id="the_select">
                      <option value="">{if $multiLang.text_search_staff_role}{$multiLang.text_search_staff_role}{else}No Translate (Key Lang:text_search_staff_role){/if}</option>
                      <option value="">All staff role</option>
                      {foreach from = $search_by_role item = staff_role}
                      <option value="{$staff_role.staff_role_name}">{$staff_role.staff_role_name}</option>
                      {/foreach}
                    </select>
                  </div>
                  <div class="col-md-7">
                    <form class="form-inline" role="form" action="{$admin_file}" method="GET">
                      <div class="input-group" style="float: right;">
                        <input type="hidden" name="task" value="staff_permission">
                        <input id="search" type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="">
                        <span class="input-group-btn">
                          <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
                        </span>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="demo" class="collapse {if $error or $edit_staff_permission.id}in{/if}">
            <form  action="{$admin_file}?task=staff_permission" method="post">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="staff_function">{if $multiLang.text_staff_function}{$multiLang.text_staff_function}{else}No Translate (Key Lang:text_staff_function){/if}:</label>
                    <select class="form-control" id="staff_function" name="staff_function_id">
                      <option value=""> ---{if $multiLang.text_select}{$multiLang.text_select}{else}No Translate (Key Lang:text_select){/if} {if $multiLang.text_staff_function}{$multiLang.text_staff_function}{else}No Translate (Key Lang:text_staff_function){/if}--- </option>
                      {foreach from = $list_staff_function item = staff_function}
                      <option value="{$staff_function.id}"{if $edit_staff_permission.staff_function_id eq $staff_function.id}selected{else}{if $smarty.session.staff_permission.staff_function_id eq $staff_function.id}selected{/if}{/if}>{$staff_function.title}</option>
                      {/foreach}
                    </select>
                    <p class="help-block">{if $error.staff_function_id eq 1}<font style="color:red;">*&nbsp;{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate (Key Lang:text_please_select){/if} {if $multiLang.text_staff_function}{$multiLang.text_staff_function}{else}No Translate (Key Lang:text_staff_function){/if}</font>{/if}</p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="staff">{if $multiLang.text_staff_role}{$multiLang.text_staff_role}{else}No Translate (Key Lang:text_staff_role){/if}:</label>
                    <select class="form-control" id="staff" name="staff_role_id">
                      <option value="0"> ---{if $multiLang.text_select}{$multiLang.text_select}{else}No Translate (Key Lang:text_select){/if} {if $multiLang.text_staff_role}{$multiLang.text_staff_role}{else}No Translate (Key Lang:text_staff_role){/if}--- </option>
                      {foreach from = $list_staff_role item = staff_role}
                      <option value="{$staff_role.id}" {if $edit_staff_permission.staff_role_id eq $staff_role.id}selected{else}{if $smarty.session.staff_permission.staff_role_id eq $staff_role.id}selected{/if}{/if}>{$staff_role.name}</option>
                      {/foreach}
                    </select>
                    <p class="help-block">{if $error.staff_role_id eq 1}<font style="color:red;">*&nbsp;{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate (Key Lang:text_please_select){/if} {if $multiLang.text_staff_role}{$multiLang.text_staff_role}{else}No Translate (Key Lang:text_staff_role){/if}</font>{/if}</p>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    {if $edit_staff_permission.id}
                      <input type="hidden" name="id" value="{$edit_staff_permission.id}" />
                      <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> Update</button>
                      <a href="{$admin_file}?task=staff_permission" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> Cancel</a>
                    {else}
                      <button type="submit" name="submit" class="btn btn-info"><i class="fa fa-floppy-o"></i> Save</button>
                    {/if}
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div><!--panel panel-body-->
      </div><!--panel panel-default-->
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr bgcolor="#eeeeee">
            <th>{if $multiLang.text_staff_function}{$multiLang.text_staff_function}{else}No Translate (Key Lang:text_staff_function){/if}</th>
            <th>{if $multiLang.text_staff_role}{$multiLang.text_staff_role}{else}No Translate (Key Lang:text_staff_role){/if}</th>
            <th>{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate (Key Lang:text_action){/if}</th>
            </tr>
          </thead>
          {if $list_Staff_Permission|@count gt 0}
          {foreach from = $list_Staff_Permission item = staff_permission key=k}
          <tbody>
            <tr>
              <td>{$staff_permission.staff_function_title}</td>
              <td>
                {if $smarty.get.kwd}
                <a href="{$admin_file}?task=staff_permission">
                {else}
                <a href="{$admin_file}?task=staff_permission&amp;kwd={$staff_permission.staff_role_name}">
                {/if}
                {$staff_permission.staff_role_name}
                </a>
              </td>
              <td width="100px">
                <a href="{$admin_file}?task=staff_permission&amp;action=edit&amp;id={$staff_permission.id|escape}&amp;sr_id={$smarty.get.sr_id|escape}&amp;next={$smarty.get.next|escape}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate (Key Lang:button_edit){/if}"><i class="fa fa-edit"></i></a>
                <!-- Trigger the modal with a button -->
                <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$staff_permission.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate (Key Lang:button_delete){/if}"><i class="fa fa-trash-o"></i></button>
                <!-- Modal -->
                <div class="modal fade" id="myModal_{$staff_permission.id}" role="dialog">
                  <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="panel panel-primary modal-content">
                      <div class="panel-heading modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate (Key Lang:text_confirmation){/if}</h4>
                      </div>
                      <div class="modal-body">
                        <p>{if $multiLang.text_confirm_delete}{$multiLang.text_confirm_delete}{else}No Translate (Key Lang:text_confirm_delete){/if}?</p>
                      </div>
                      <div class="modal-footer">
                        <a href="{$admin_file}?task=staff_permission&amp;action=delete&amp;id={$staff_permission.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate (Key Lang:button_delete){/if}</i></a>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate (Key Lang:button_close){/if}</i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
          {/foreach}
          {else}
          <tr>
            <td colspan="5"><h4 style="text-align:center">There is no record</h4></td>
          </tr>
          {/if}
        </table>
      </div><!--table-responsive  -->
      <div class="pull-right">{include file="common/paginate.tpl"}</div>
    </div><!--end panel-body-->
</div><!--panel panel-primary-->
{/block}
{block name="javascript"}
<script type="text/javascript">
  $(document).ready(function(){
      $( "#flash" ).fadeIn( 50 ).delay( 3000 ).fadeOut( 500 );
  });
</script>
<script type="text/javascript">
$(function(){
  $("#the_select").change(function(){
    window.location='{$admin_file}?task=staff_permission&kwd=' + this.value
  });
  document.getElementById("search").value = "";
});
</script>
<script>
$('#birth_date').datetimepicker({ locale: 'en', format: 'YYYY-MM-DD' });
 $('[data-toggle="popover"]').popover();
	$(function () {
    $('[data-toggle1="tooltip"]').tooltip()
  });
</script>
{/block}
