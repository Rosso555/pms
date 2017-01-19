{extends file="admin/layout.tpl"}
{block name="main"}
<div class="row">
  <div class="col-lg-12">
    <ul class="breadcrumb">
      <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
      <li class="active">{if $multiLang.text_staff_function}{$multiLang.text_staff_function}{else}No Translate (Key Lang:text_staff_function){/if}</li>
    </ul>
    {if $error.exist_delete eq 1}
      <div class="alert alert-danger alert-dismissible"  id="{if $error.exist_delete eq 1}flash{/if}">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
        <strong>warning!</strong> sorry you can not delete this record!
      </div>
    {/if}
    <div class="panel panel-primary">
      <div class="panel-heading"><h3 class="panel-title">{if $multiLang.text_staff_function}{$multiLang.text_staff_function}{else}No Translate (Key Lang:text_staff_function){/if}</h3></div>
        <div class="panel-body">
          <div class="panel panel-default">
  					<div class="panel-body">
              <form class="form-inline" role="form" action="{$admin_file}" method="GET">
              <div class="form-group">
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                  <i class="fa fa-plus-circle"></i> {if $multiLang.button_new_function}{$multiLang.button_new_function}{else}No Translate (Key Lang:button_new_function){/if}
                </button>
              </div>
              <div class="form-group" float="right">
                <input type="hidden" name="task" value="staff_function"/>
                <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd}" />
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
              </div>
            </form>
            </div>
          </div>
          <form class="form" role="form" action="{$admin_file}?task=staff_function" method="post" enctype="multipart/form-data">
            {if $error Or $edit.id}
            <div class="collapse in" id="collapseExample">
            {else}
            <div class="collapse" id="collapseExample">
            {/if}
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>{if $multiLang.text_staff_function_title}{$multiLang.text_staff_function_title}{else}No Translate (Key Lang:text_staff_function_title){/if}:</label><span class="text-danger"> *{if $error.title eq 1}{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate (Key Lang:text_please_input){/if} {if $multiLang.text_staff_function_title}{$multiLang.text_staff_function_title}{else}No Translate (Key Lang:text_staff_function_title){/if}.{/if}</span>
                  <input type="text" class="form-control" name="title" value="{if $edit.title}{$edit.title}{else}{if $smarty.session.staff_function.title|escape}{$smarty.session.staff_function.title|escape}{/if}{/if}" placeholder="example:ABC.."/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>{if $multiLang.text_task_title}{$multiLang.text_task_title}{else}No Translate (Key Lang:text_task_title){/if}:</label><span class="text-danger"> *{if $error.task eq 1}{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate (Key Lang:text_please_input){/if} {if $multiLang.text_task_title}{$multiLang.text_task_title}{else}No Translate (Key Lang:text_task_title){/if}.{/if}</span>
                  <input type="text" class="form-control" name="task" value="{if $edit.task_name}{$edit.task_name}{else}{if $smarty.session.staff_function.task|escape}{$smarty.session.staff_function.task|escape}{/if}{/if}" placeholder="task staff,.." />
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate (Key Lang:text_action){/if}:</label>
                  <input type="text" class="form-control" name="action" value="{if $edit.action_name}{$edit.action_name}{else}{if $smarty.session.staff_function.action|escape}{$smarty.session.staff_function.action|escape}{/if}{/if}" placeholder="action save.." />
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $edit.id}
                    <input type="hidden" name="id" value="{$edit.id}">
                    <button type="submit" name="submit" class="btn btn-success"><i class="fa fa-check-circle-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate (Key Lang:button_update){/if}</button>
                    <a href="{$admin_file}?task=staff_function" class="btn btn-danger"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate (Key Lang:button_cancel){/if}</a>
                  {else}
                    <button type="submit" class="btn btn-info"><i class="fa fa-check-circle-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate (Key Lang:button_save){/if}</button>
                  {/if}
                </div>
              </div>
            </div>
            </div>
          </form>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr bgcolor="#eeeeee">
                  <th>{if $multiLang.text_staff_function_title}{$multiLang.text_staff_function_title}{else}No Translate (Key Lang:text_staff_function_title){/if}</th>
                  <th>{if $multiLang.text_task_title}{$multiLang.text_task_title}{else}No Translate (Key Lang:text_task_title){/if}</th>
                  <th>{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate (Key Lang:text_action){/if}</th>
                  <th class="text-center" width="100px">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate (Key Lang:text_action){/if}</th>
                </tr>
              </thead>
            <tbody>
              {foreach from=$list_function item= v }
              <tr>
                <td valign="top">{$v.title}</td>
                <td valign="top">{$v.task_name}</td>
                <td valign="top">{$v.action_name}</td>
                <td class="text-center" valign="top">
                  <a href="{$admin_file}?task=staff_function&amp;action=edit&amp;id={$v.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate (Key Lang:button_edit){/if}"><i class="fa fa-edit"></i></a>
                  <button href="#myModal_{$v.id}" class= "btn btn-danger btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate (Key Lang:button_delete){/if}" data-toggle= "modal"><i class="fa fa-trash" aria-hidden="true"></i></button>
                  <!-- Trigger the modal with a button -->
                  <div class="modal fade" id="myModal_{$v.id}" role="dialog">
                    <div class="modal-dialog">
                      <div class="panel panel-primary modal-content">
                        <div class="panel-heading modal-header">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate (Key Lang:text_confirmation){/if}</h4>
                        </div>
                        <div class="modal-body">
                          <p>Are you sure you want to Delete {$v.title}?</p>
                        </div>
                        <div class="modal-footer">
                          <a href="{$admin_file}?task=staff_function&amp;action=delete&amp;id={$v.id|escape}" class="btn btn-danger btn-md"><i class="fa fa-check-circle-o"></i> {if $multiLang.button_yes}{$multiLang.button_yes}{else}No Translate (Key Lang:button_yes){/if}</a>
                          <button type="button" class="btn btn-primary collapsed" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate (Key Lang:button_close){/if}</button>
                        </div>
                      </div>
                    </div>
                  </div>
                <!-- Modal -->
                </td>
              </tr>
              {/foreach}
            </tbody>
          </table>
        </div>
        <div class="pull-right">{include file = "common/paginate.tpl"}</div>
      </div>
    </div>
  </div>
{/block}
{block name="javascript"}
<script type="text/javascript">
  $(document).ready(function(){
      $( "#flash" ).fadeIn( 50 ).delay( 3000 ).fadeOut( 500 );
  });
</script>
{/block}
