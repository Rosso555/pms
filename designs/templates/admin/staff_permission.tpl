{extends file="admin/layout.tpl"}
{block name="main"}
<div class="row">
  <div class="col-lg-12">
    <ul class="breadcrumb">
      <li><a href="{$admin_file_name}"><i class="fa fa-fw fa-home"></i></a></li>
      <li class="active">Staff Permission</li>
    </ul>
    <div class="panel panel-primary">
        <div class="panel-heading"><h4 class="panel-title">Staff Permission</h4></div>
        <div class="panel-body">
          <div class="panel panel-default">
            <div class="panel-body">
              <form class="form-inline" role="form" action="{$admin_file}?task=staff_permission" method="GET" style="padding: 1px 0px 12px 1px;">
                <input type="hidden" name="task" value="staff_permission">
              <div class="form-group">
                <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                  <i class="fa fa-plus-circle"></i> New Staff Permission
                </button>
              </div>
                <div class="input-group" style="float: right;">
                  <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="">
                  <span class="input-group-btn">
                    <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> Search</button>
                  </span>
                </div>
                <div class="form-group" style="float: right; margin-right: 10px;">
                  <select class="form-control" id="staff" name="sr_id">
                    <option value=""> ---Please Select Staff Role--- </option>
                    {foreach from = $list_staff_role item = staff_role}
                    <option value="{$staff_role.id}" {if $smarty.get.sr_id eq $staff_role.id}selected{/if}>{$staff_role.title}</option>
                    {/foreach}
                  </select>
                  <p class="help-block">{if $error.staff_role_id eq 1}<font style="color:red;">*&nbsp;Please Input staff Role</font>{/if}</p>
                </div>
              </form>
              {if $error or $edit_staff_permission.id}
                <div id="demo" class="collapse in">
              {else}
                <div id="demo" class="collapse">
              {/if}
                  <form  action="{$admin_file}?task=staff_permission" method="post">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="staff_function">Staff Function:</label>
                          <select class="form-control" id="staff_function" name="staff_function_id">
                            <option value=""> ---Please Select Staff Functon--- </option>
                            {foreach from = $list_staff_function item = staff_function}
                            <option value="{$staff_function.id}"{if $edit_staff_permission.staff_function_id eq $staff_function.id}selected{else}{if $smarty.session.staff_permission.staff_function_id eq $staff_function.id}selected{/if}{/if}>{$staff_function.title}</option>
                            {/foreach}
                          </select>
                          <p class="help-block">{if $error.staff_function_id eq 1}<font style="color:red;">*&nbsp;Please Select  Staff funtion</font>{/if}</p>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="staff">Staff Role:</label>
                          <select class="form-control" id="staff" name="staff_role_id">
                            <option value=""> ---Please Select Staff Role--- </option>
                            {foreach from = $list_staff_role item = staff_role}
                            <option value="{$staff_role.id}" {if $edit_staff_permission.staff_role_id eq $staff_role.id}selected{else}{if $smarty.session.staff_permission.staff_role_id eq $staff_role.id}selected{/if}{/if}>{$staff_role.name}</option>
                            {/foreach}
                          </select>
                          <p class="help-block">{if $error.staff_role_id eq 1}<font style="color:red;">*&nbsp;Please Input staff Role</font>{/if}</p>
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
                            <button type="submit" name="submit"class="btn btn-info"><i class="fa fa-floppy-o"></i> Save</button>
                          {/if}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div><!--panel panel-body-->
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr bgcolor="#eeeeee">
                  <th>Function</th>
                  <th>Role</th>
                  <th>Action</th>
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
                      <a href="{$admin_file}?task=staff_permission&amp;action=edit&amp;id={$staff_permission.id|escape}&amp;sr_id={$smarty.get.sr_id|escape}&amp;next={$smarty.get.next|escape}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                      <!-- Trigger the modal with a button -->
                      <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$staff_permission.id}" data-toggle1="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></button>
                      <!-- Modal -->
                      <div class="modal fade" id="myModal_{$staff_permission.id}" role="dialog">
                        <div class="modal-dialog">
                          <!-- Modal content-->
                          <div class="panel panel-primary modal-content">
                            <div class="panel-heading modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                              <h4 class="panel-title modal-title">Confirmation</h4>
                            </div>
                            <div class="modal-body">
                              <p>Are you sure you want to delete this staff permission?</p>
                            </div>
                            <div class="modal-footer">
                              <a href="{$admin_file}?task=staff_permission&amp;action=delete&amp;id={$staff_permission.id}&amp;sr_id={$smarty.get.sr_id|escape}&amp;next={$smarty.get.next|escape}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> Delete</i></a>
                              <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-remove"> Close</i></button>
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
          </div><!--panel panel-default-->
        </div><!--end panel-body-->
      </div><!--panel panel-primary-->
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
