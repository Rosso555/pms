{extends file="psychologist/layout.tpl"}
{block name="main"}
<div class="panel panel-primary">
  <div class="panel-heading"><h3 class="panel-title">Welcome!</h3></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-plus-circle"></i> Add Patient
              </button>
            </div>
          </div>
        </div>

        <div id="demo" class="collapse {if $error}in{/if}">
          <form>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email">Email:</label>
                  <input type="email" class="form-control" id="email" placeholder="Enter email">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="pwd">Password:</label>
                  <input type="password" class="form-control" id="pwd" placeholder="Enter password">
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
          </form>

        </div>

      </div><!--End panel-heading-->
    </div><!--End panel panel-primary-->

  </div><!--End panel-heading-->
</div><!--End panel panel-primary-->

{/block}
