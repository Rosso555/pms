{extends file="admin/layout.tpl"}
{block name="main"}
{if $smarty.cookies.checkCategory}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot delete "<strong>{$smarty.cookies.checkCategory}</strong>" because it has been used.
  </div>
{/if}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">Category</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <form class="form-inline" action="{$admin_file}?task=category" method="post">
              <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" class="form-control" value="{$getCategoryByID.name}" id="name" placeholder="Enter Category" required>
              </div>
              {if $getCategoryByID.id}
              <input type="hidden" name="id" value="{$getCategoryByID.id}" />
              <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> Update</button>
              <a href="{$admin_file}?task=category" class="btn btn-danger"><i class="fa fa-close"></i> Cancel</a>
              {else}
              <button type="submit" class="btn btn-info"><i class="fa fa-floppy-o"></i> Save</button>
              {/if}
            </form>
          </div>
          <div class="col-md-6">
            <form class="form-inline" action="{$admin_file}?task=category" method="get">
              <input type="hidden" name="task" value="category">
              <div class="input-group" style="float: right;">
                <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="">
                <span class="input-group-btn">
                  <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> Search</button>
                </span>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div><!--panel panel-body-->
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
          <th>Name</th>
          <th width="130">Action</th>
          </tr>
        </thead>
        {if $listCategory|@count gt 0}
        <tbody>
        {foreach from = $listCategory item = data key=k}
          <tr>
            <td>{$data.name}</td>
            <td>
              <a href="{$admin_file}?task=category&amp;action=edit&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
              <!-- Trigger the modal with a button -->
              <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$data.id}" data-toggle1="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></button>
              <!-- Modal -->
              <div class="modal fade" id="myModal_{$data.id}" role="dialog">
                <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="panel panel-primary modal-content">
                    <div class="panel-heading modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="panel-title modal-title">Confirmation</h4>
                    </div>
                    <div class="modal-body">
                      <p>Are you sure you want to delete this category <b>({$data.name|escape})</b> ?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=category&amp;action=delete&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> Delete</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> Close</i></button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
            </td>
          </tr>
        {/foreach}
        </tbody>
        {else}
        <tr>
          <td colspan="7"><h4 style="text-align:center">There is no record</h4></td>
        </tr>
        {/if}
      </table>
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
