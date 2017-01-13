{extends file="admin/layout.tpl"}
{block name="main"}
<div class="row">
  <div class="col-lg-12">
    <ul class="breadcrumb">
      <li><a href="{$admin_file_name}"><i class="fa fa-fw fa-home"></i></a></li>
      <li class="active">{if $multiLang.text_staff_role_header}{$multiLang.text_staff_role_header}{else}No Translate (Key Lang:text_staff_role_header){/if}</li>
    </ul>
    {if $error.title}
    <div class="alert alert-danger">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Warning!</strong> Please write staff role.
    </div>
    {/if}
    {if $error.exist_delete eq 1}
      <div class="alert alert-danger alert-dismissible"  id="{if $error.exist_delete eq 1}flash{/if}">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
        <strong>warning!</strong> sorry you can not delete this record!
      </div>
    {/if}
    <div class="panel panel-primary">
      <div class="panel-heading"><h3 class="panel-title">{if $multiLang.text_staff_role_header}{$multiLang.text_staff_role_header}{else}No Translate (Key Lang:text_staff_role_header){/if}</h3></div>
        <div class="panel-body">
          <div class="panel panel-default">
  					<div class="panel-body">
              <div class="col-md-8">
                <form class="form-inline" role="form" action="{$admin_file_name}?task=staff_role" method="post">
                  <div class="form-group">
                    <label for="">{if $multiLang.text_role_name}{$multiLang.text_role_name}{else}No Translate (Key Lang:text_role_name){/if}:</label>
                    <input type="text" class="form-control" name="name" value="{$edit.name}{if $smarty.session.staff_role.name|escape}{$smarty.session.staff_role.name|escape}{/if}" />
                  </div>
                  <div class="form-group">
                    {if $edit.id}
                    <input type="hidden" name="id" value="{$edit.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-circle-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate (Key Lang:button_update){/if}</button>
                    <a href="{$admin_file_name}?task=staff_role" class="btn btn-danger"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate (Key Lang:button_cancel){/if}</a>
                    {else}
                    <button type="submit" name="submit" class="btn btn-info"><i class="fa fa-check-circle-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate (Key Lang:button_save){/if}</button>
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
                        <button class="btn btn-info" type="submit">{if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
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
                  <th>{if $multiLang.text_role_name}{$multiLang.text_role_name}{else}No Translate (Key Lang:text_role_name){/if}</th>
                  <th class="text-center" width="100px">{if $multiLang.text_title_action}{$multiLang.text_title_action}{else}No Translate (Key Lang:text_title_action){/if}</th>
                </tr>
              </thead>
            <tbody>
              {foreach  from=$list_staff_role item=v}
              <tr>
                <td valign="top">{$v.name}</td>
                <td class="text-center" valign="top">
                  <a href="{$admin_file_name}?task=staff_role&amp;action=edit&amp;id={$v.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate (Key Lang:button_edit){/if}"><i class="fa fa-edit"></i></a>
                  <button href="#myModal_{$v.id}" class= "btn btn-danger btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate (Key Lang:button_delete){/if}" data-toggle= "modal"><i class="fa fa-trash-o"></i></button>
									<div id="myModal_{$v.id}" class="modal fade" role="dialog">
										<div class="modal-dialog modal-md">
  										<div class="modal-content">
  								    	<div class="modal-header">
  											  <button type="button" class="close" data-dismiss="modal">&times;</button>
  											  <h3>{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate (Key Lang:text_confirmation){/if}</h3>
  										  </div>
  									    <div class="modal-body">
  									    	<p>Are you sure want to delete project's named <label class="label label-info">{$v.name} </label> ?</p>
  								      </div>
    								    <div class="modal-footer">
                          <CENTER>
                            <a href="{$admin_file_name}?task=staff_role&amp;action=delete&amp;id={$v.id}" class="btn btn-primary pull-left"><i class="fa fa-trash-o"></i> {if $multiLang.button_yes}{$multiLang.button_yes}{else}No Translate (Key Lang:button_yes){/if}</a>
    								    		<button type="button" data-dismiss="modal" class="btn btn-danger pull-left"><i class="fa fa-close"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate (Key Lang:button_close){/if}</button>
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
<script type="text/javascript">
  $(document).ready(function(){
      $( "#flash" ).fadeIn( 50 ).delay( 3000 ).fadeOut( 500 );
  });
</script>
<script>
 $('[data-toggle="popover"]').popover();
	$(function () {
    $('[data-toggle1="tooltip"]').tooltip()
  });
</script>
{/block}
