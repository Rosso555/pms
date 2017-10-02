{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file_name}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_staff_permision_header}{$multiLang.text_staff_permision_header}{else}No Translate (Key Lang:text_staff_permision_header){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate (Key Lang:text_edit){/if}</li>
  {/if}
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
          <form class="form-inline" role="form" action="{$admin_file}" method="GET">
            <input type="hidden" name="task" value="staff_permission">
            <div class="form-group" style="margin-bottom:5px;">
              <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-plus-circle"></i> {if $multiLang.text_new_staff_permission}{$multiLang.text_new_staff_permission}{else}No Translate (Key Lang:text_new_staff_permission){/if}
              </button>
            </div>
            <div class="form-group" style="margin-bottom:5px;">
              <select class="form-control" id="the_select">
                <option value="">--- All staff role ---</option>
                {foreach from = $search_by_role item = staff_role}
                <option value="{$staff_role.staff_role_id}" {if $smarty.get.srid eq $staff_role.staff_role_id}selected{/if}>{$staff_role.staff_role_name}</option>
                {/foreach}
              </select>
            </div>
            <div class="form-group" style="margin-bottom:5px;">
              <input id="search" type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="">
            </div>
            <div class="form-group" style="margin-bottom:5px;">
              <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
            </div>
          </form>
          <div id="demo" class="collapse {if $error or $edit_staff_permission.id}in{/if}">
            {if $edit_staff_permission.id}
            <form action="{$admin_file}?task=staff_permission&amp;action=edit&amp;id={$edit_staff_permission.id}" method="post">
            {else}
            <form action="{$admin_file}?task=staff_permission&amp;action=add" method="post">
            {/if}
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="staff"><span style="color:red;">*</span> {if $multiLang.text_staff_role}{$multiLang.text_staff_role}{else}No Translate (Key Lang:text_staff_role){/if}:</label>
                    {if $error.staff_role_id eq 1}<span style="color:red;">*&nbsp;{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate (Key Lang:text_please_select){/if} {if $multiLang.text_staff_role}{$multiLang.text_staff_role}{else}No Translate (Key Lang:text_staff_role){/if}</span>{/if}
                    <select class="form-control" id="staff" name="staff_role_id" onchange="getStaffFunctionByStaff(this)">
                      <option value="0">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate (Key Lang:text_select){/if} {if $multiLang.text_staff_role}{$multiLang.text_staff_role}{else}No Translate (Key Lang:text_staff_role){/if} ---</option>
                      {foreach from = $list_staff_role item = staff_role}
                      <option value="{$staff_role.id}" {if $edit_staff_permission.staff_role_id eq $staff_role.id}selected{else}{if $smarty.session.staff_permission.staff_role_id eq $staff_role.id}selected{/if}{/if}>{$staff_role.name}</option>
                      {/foreach}
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group multi_select2">
                    <input type="hidden" id="select2_placeholder" value="{if $multiLang.text_select}{$multiLang.text_select}{else}No Translate (Key Lang:text_select){/if} {if $multiLang.text_staff_function}{$multiLang.text_staff_function}{else}No Translate (Key Lang:text_staff_function){/if}">
                    <label for="staff_function"><span style="color:red;">*</span> {if $multiLang.text_staff_function}{$multiLang.text_staff_function}{else}No Translate (Key Lang:text_staff_function){/if}:</label>
                    {if $error.staff_function_id eq 1}<span style="color:red;">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate (Key Lang:text_please_select){/if} {if $multiLang.text_staff_function}{$multiLang.text_staff_function}{else}No Translate (Key Lang:text_staff_function){/if}</span>{/if}
                    {if $error.existed_per eq 1}<span style="color:red;">{if $multiLang.text_staff_function}{$multiLang.text_staff_function}{else}No Translate (Key Lang:text_staff_function){/if} {if $multiLang.text_is_existed}{$multiLang.text_is_existed}{else}No Translate (Key Lang:text_is_existed){/if}</span>{/if}

                    <select class="form-control select2_mul" id="staff_function" {if $smarty.get.action eq 'edit'}name="staff_function_id"{else}name="staff_function_id[]" multiple="multiple"{/if} style="width:100%;">
                      {foreach from = $list_staff_function item = staff_function}
                      <option value="{$staff_function.id}" {if $edit_staff_permission.staff_function_id eq $staff_function.id}selected{else}{if $smarty.session.staff_permission.staff_function_id}{foreach from=$smarty.session.staff_permission.staff_function_id item=sp}{if $sp eq $staff_function.id}selected{/if}{/foreach}{/if}{/if}>{$staff_function.title}</option>
                      {/foreach}
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    {if $edit_staff_permission.id}
                      <input type="hidden" name="id" value="{$edit_staff_permission.id}" />
                      <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate (Key Lang:button_update){/if}</button>
                      <a href="{$admin_file}?task=staff_permission" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate (Key Lang:button_cancel){/if}</a>
                    {else}
                      <button type="submit" class="btn btn-info"><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate (Key Lang:button_save){/if}</button>
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
          <tbody>
          {foreach from = $list_Staff_Permission item = staff_permission key=k}
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
          {/foreach}
          </tbody>
          {else}
          <tr>
            <td colspan="5"><h4 style="text-align:center">There is no record</h4></td>
          </tr>
          {/if}
        </table>
      </div><!--table-responsive  -->
      {include file="common/paginate.tpl"}
    </div><!--end panel-body-->
</div><!--panel panel-primary-->
{/block}
{block name="javascript"}
<script type="text/javascript">
  $(document).ready(function(){
    $( "#flash" ).fadeIn( 50 ).delay( 3000 ).fadeOut( 500 );

    {if $error}
    $('.select2_mul').on('select2:select', function (event) {
      console.log(event.params.data);
    });
    {/if}

    $("#the_select").change(function(){
      window.location='{$admin_file}?task=staff_permission&kwd={$smarty.get.kwd}&srid=' + this.value
    });
    document.getElementById("search").value = "";

  });

  function getStaffFunctionByStaff(sel)
  {
    $(".loader").show();

    $.ajax({
      type: "GET",
      url: "{$admin_file}?task=staff_permission&action=staff_fun_permistion&srid="+sel.value,
      success: function(data){
        var dataHTML = "";
        if(data.length > 0)
        {
          for (var i = 0; i < data.length; i++) {
            dataHTML += "<option value='"+data[i].id+"'>"+data[i].title+"</option>";
          }
          $("#staff_function").html(dataHTML);
        }else {
          dataHTML += "<option value=''>No Data</option>";
          $("#staff_function").html(dataHTML);
        }
        if(data == false)
        {
          window.location.replace('{$admin_file}?task=perror');
        }
        //hide Loading gif
        $(".loader").hide();
      },
      error: function(){
        //Show error here
        alert("Cannot load data. Please try again later.");
        location.reload();
      }
    });//End Ajax

  }
</script>
{/block}
