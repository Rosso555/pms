{extends file="admin/layout.tpl"}
{block name="main"}
<div class="row">
  <div class="col-lg-12">
    <ul class="breadcrumb">
      <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
      <li class="active">Staff Function</li>
    </ul>
    <div class="panel panel-primary">
      <div class="panel-heading"><h3 class="panel-title">Staff Function</h3></div>
        <div class="panel-body">
          <div class="panel panel-default">
  					<div class="panel-body">
              <form class="form-inline" role="form" action="{$admin_file}" method="GET">
              <div class="form-group">
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                  <i class="fa fa-plus-circle"></i> News Function
                </button>
              </div>
              <div class="form-group" float="right">
                <input type="hidden" name="task" value="staff_function"/>
                <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd}" />
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
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
                  <label>Title:</label><span class="text-danger"> *{if $error.title eq 1}Please write title.{/if}</span>
                  <input type="text" class="form-control" name="title" value="{if $edit.title}{$edit.title}{else}{if $smarty.session.staff_function.title|escape}{$smarty.session.staff_function.title|escape}{/if}{/if}" placeholder="example:ABC.."/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>task Name:</label><span class="text-danger"> *{if $error.task eq 1}Please write task.{/if}</span>
                  <input type="text" class="form-control" name="task" value="{if $edit.task_name}{$edit.task_name}{else}{if $smarty.session.staff_function.task|escape}{$smarty.session.staff_function.task|escape}{/if}{/if}" placeholder="task staff,.." />
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Action Name:</label>
                  <input type="text" class="form-control" name="action" value="{if $edit.action_name}{$edit.action_name}{else}{if $smarty.session.staff_function.action|escape}{$smarty.session.staff_function.action|escape}{/if}{/if}" placeholder="action save.." />
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $edit.id}
                    <input type="hidden" name="id" value="{$edit.id}">
                    <button type="submit" name="submit" class="btn btn-success"><i class="fa fa-check-circle-o"></i> Update</button>
                    <a href="{$admin_file}?task=staff_function" class="btn btn-danger"><i class="fa fa-close"></i> Cancel</a>
                  {else}
                    <button type="submit" class="btn btn-info"><i class="fa fa-check-circle-o"></i> Save</button>
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
                  <th>Title</th>
                  <th>Task</th>
                  <th>Action</th>
                  <th class="text-center" width="100px">Action</th>
                </tr>
              </thead>
            <tbody>
              {foreach from=$list_function item= v }
              <tr>
                <td valign="top">{$v.title}</td>
                <td valign="top">{$v.task_name}</td>
                <td valign="top">{$v.action_name}</td>
                <td class="text-center" valign="top">
                  <a href="{$admin_file}?task=staff_function&amp;action=edit&amp;id={$v.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                  <button href="#myModal_{$v.id}" class= "btn btn-danger btn-xs" data-toggle1="tooltip" data-placement="top" title="Remove" data-toggle= "modal"><i class="fa fa-trash" aria-hidden="true"></i></button>
                  <!-- Trigger the modal with a button -->
                  <div class="modal fade" id="myModal_{$v.id}" role="dialog">
                    <div class="modal-dialog">
                      <div class="panel panel-primary modal-content">
                        <div class="panel-heading modal-header">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="panel-title modal-title">Confirmation</h4>
                        </div>
                        <div class="modal-body">
                          <p>Are you sure you want to Delete {$v.title}?</p>
                        </div>
                        <div class="modal-footer">
                          <a href="{$admin_file}?task=staff_function&amp;action=delete&amp;id={$v.id|escape}" class="btn btn-danger btn-md"><i class="fa fa-check-circle-o"></i> Yes</a>
                          <button type="button" class="btn btn-primary collapsed" data-dismiss="modal"><i class="fa fa-remove"></i> Close</button>
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
<script>
$('#birth_date').datetimepicker({ locale: 'en', format: 'YYYY-MM-DD' });
 $('[data-toggle="popover"]').popover();
	$(function () {
    $('[data-toggle1="tooltip"]').tooltip()
  });
</script>
{/block}
