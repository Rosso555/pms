{extends file="admin/layout.tpl"}
{block name="main"}
<div class="row">
  <div class="col-lg-12">
    <ul class="breadcrumb">
      <li><a href="{$admin_file_name}"><i class="fa fa-fw fa-home"></i></a></li>
      <li class="active">List Staff Role</li>
    </ul>
    {if $error.title}
    <div class="alert alert-danger">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Warning!</strong> Please write staff role.
    </div>
    {/if}
    <div class="panel panel-primary">
      <div class="panel-heading"><h3 class="panel-title">List Staff Role </h3></div>
        <div class="panel-body">
          <div class="panel panel-default">
  					<div class="panel-body">
              <div class="col-md-8">
                <form class="form-inline" role="form" action="{$admin_file_name}?task=staff_role" method="post">
                  <div class="form-group">
                    <label for="">Name:</label>
                    <input type="text" class="form-control" name="name" value="{$edit.name}{if $smarty.session.staff_role.name|escape}{$smarty.session.staff_role.name|escape}{/if}" />
                  </div>
                  <div class="form-group">
                    {if $edit.id}
                    <input type="hidden" name="id" value="{$edit.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-circle-o"></i> Update</button>
                    <a href="{$admin_file_name}?task=staff_role" class="btn btn-danger"><i class="fa fa-close"></i> Cancel</a>
                    {else}
                    <button type="submit" name="submit" class="btn btn-info"><i class="fa fa-check-circle-o"></i> Save</button>
                    {/if}
                  </div>
                </form>
              </div>
              <div class="col-sm-4">
                <form class="form-inline" role="form" action="{$admin_file_name}" method="GET">
                  <div class="form-group">
                    <input type="hidden" name="task" value="staff_role"/>
                    <div class="input-group">
                      <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd}" placeholder="Search staff role">
                      <span class="input-group-btn">
                        <button class="btn btn-info" type="submit">Search</button>
                      </span>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <hr>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr bgcolor="#eeeeee">
                  <!-- <th class="text-center" width="70px">No.</th> -->
                  <th>Name</th>
                  <th class="text-center" width="100px">Action</th>
                </tr>
              </thead>
            <tbody>
              {foreach  from=$list_staff_role item=v}
              <tr>
                <td valign="top">{$v.name}</td>
                <td class="text-center" valign="top">
                  <a href="{$admin_file_name}?task=staff_role&amp;action=edit&amp;id={$v.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                  <button href="#myModal_{$v.id}" class= "btn btn-danger btn-xs" data-toggle1="tooltip" data-placement="top" title="Delete" data-toggle= "modal"><i class="fa fa-trash-o"></i></button>
									<div id="myModal_{$v.id}" class="modal fade" role="dialog">
										<div class="modal-dialog modal-md">
  										<div class="modal-content">
  								    	<div class="modal-header">
  											  <button type="button" class="close" data-dismiss="modal">&times;</button>
  											  <h3>Delete Task Confirmation</h3>
  										  </div>
  									    <div class="modal-body">
  									    	<p>Are you sure want to delete project's named <label class="label label-info">{$v.name} </label> ?</p>
  								      </div>
    								    <div class="modal-footer">
                          <CENTER>
    								    		<button type="button" data-dismiss="modal" class="btn btn-danger pull-left"><i class="fa fa-close"></i> Close</button>
    											  <a href="{$admin_file_name}?task=staff_role&amp;action=delete&amp;id={$v.id}" class="btn btn-primary pull-left"><i class="fa fa-trash-o"></i> Delete</a>
                          </CENTER>
    								    </div>
  								    </div>
								    </div>
								  </div>
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
</div>
{/block}
{block name="javascript"}
<script>
 $('[data-toggle="popover"]').popover();
	$(function () {
    $('[data-toggle1="tooltip"]').tooltip()
  });
</script>
{/block}
