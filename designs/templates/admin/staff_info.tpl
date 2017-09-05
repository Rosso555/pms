{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.staff_infomation_header}{$multiLang.staff_infomation_header}{else}No Translate (Key Lang:staff_infomation_header){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate (Key Lang:text_edit){/if}</li>
  {/if}
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h3 class="panel-title">{if $multiLang.staff_infomation_header}{$multiLang.staff_infomation_header}{else}No Translate (Key Lang:staff_infomation_header){/if}</h3></div>
  <div class="panel-body">
    <div class="panel panel-default">
			<div class="panel-body">
        <div class="row">
          <div class="col-md-12">
            <form class="form-inline" role="form" action="{$admin_file}" method="GET">
              <div class="form-group">
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                  <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_profile}{$multiLang.button_add_profile}{else}No Translate (Key Lang:button_add_profile){/if}
                </button>
              </div>
              <div class="form-group" float="right">
                <input type="hidden" name="task" value="staff_info"/>
                <input type="text" class="form-control" name="kwd" value="{if $smarty.get.kwd eq 1 || $smarty.get.kwd eq 2}{else}{$smarty.get.kwd}{/if}" />
              </div>
              <div class="form-group">
                <select class="form-control " name="status" id="the_select">
                  <option value="">Show active or stoped</option>
                  <option value="">Show all</option>
                  <option value="1" {if $smarty.get.status eq 1}selected{/if}>Active</option>
                  <option value="2" {if $smarty.get.status eq 2}selected{/if}>stoped</option>
                </select>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
              </div>
            </form>
          </div>
        </div>
        <div class="collapse {if $error Or $edit.id}in{/if}" id="collapseExample" style="margin-top: 10px;">
          <form class="form" role="form" action="{$admin_file}?task=staff_info" method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label>{if $multiLang.text_staff_full_name}{$multiLang.text_staff_full_name}{else}No Translate (Key Lang:text_staff_full_name){/if}:</label><span class="text-danger"> *{if $error.name eq 1}{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate (Key Lang:text_please_input){/if} {if $multiLang.text_staff_full_name}{$multiLang.text_staff_full_name}{else}No Translate (Key Lang:text_staff_full_name){/if}.{/if}</span>
              <input type="text" class="form-control" name="name" value="{if $edit.name}{$edit.name}{else}{if $smarty.session.staff_info.name|escape}{$smarty.session.staff_info.name|escape}{/if}{/if}" />
            </div>
            <div class="form-group">
              <label>{if $multiLang.text_password}{$multiLang.text_password}{else}No Translate (Key Lang:text_password){/if}: <span class="text-danger">*</span></label>
              <span class="text-danger">
                {if $error.pass eq 1}{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate (Key Lang:text_please_input){/if} {if $multiLang.text_password}{$multiLang.text_password}{else}No Translate (Key Lang:text_password){/if}.{/if}
                {if $error.password eq 2}{if $multiLang.text_invalid_pass}{$multiLang.text_invalid_pass}{else}No Translate (Key Lang:text_invalid_pass){/if}.{/if}
              </span>
              <input type="password" id='password' class="form-control" name="password" value="{if $edit.password}{$edit.password}{else}{if $smarty.session.staff_info.password|escape}{$smarty.session.staff_info.password|escape}{/if}{/if}" />
              <input type="checkbox" onchange="document.getElementById('password').type = this.checked ? 'text' : 'password'"> {if $multiLang.text_show_password}{$multiLang.text_show_password}{else}No Translate (Key Lang:text_show_password){/if}
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
            <label>Gender</label><span class="text-danger"> *{if $error.gender}Please choice gender.{/if}</span>
            <div class="radio">
              <label class="radio-inline">
                <input type="radio" name="gender" id="inlineRadio1" value="1"{if $edit.gender eq 1}checked{/if}{if $smarty.session.staff_info.gender eq 1}checked{/if} checked> {if $multiLang.text_gernder_male}{$multiLang.text_gernder_male}{else}No Translate (Key Lang:text_gernder_male){/if}
              </label>
              <label class="radio-inline">
                <input type="radio" name="gender" id="inlineRadio2" value="2"{if $edit.gender eq 2}checked{/if}{if $smarty.session.staff_info.gender eq 2}checked{/if}>{if $multiLang.text_female}{$multiLang.text_female}{else}No Translate (Key Lang:text_female){/if}
              </label>
            </div>
            <div class="form-group">
              <label>{if $multiLang.text_phone}{$multiLang.text_phone}{else}No Translate (Key Lang:text_phone){/if}:</label><span class="text-danger"> *{if $error.phone eq 1}{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate (Key Lang:text_please_input){/if} {if $multiLang.text_phone}{$multiLang.text_phone}{else}No Translate (Key Lang:text_phone){/if}.{/if}</span>
              <input type="text" class="form-control" name="phone" value="{if $edit.phone}{$edit.phone}{else}{if $smarty.session.staff_info.phone|escape}{$smarty.session.staff_info.phone|escape}{/if}{/if}" placeholder="+855 123 456" onkeyup="NumAndTwoDecimals(event , this);" />
            </div>
            <div class="form-group">
              <label>{if $multiLang.text_staff_role}{$multiLang.text_staff_role}{else}No Translate (Key Lang:text_staff_role){/if}</label><span class="text-danger"> *{if $error.staff_role eq 1}{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate (Key Lang:text_please_input){/if} {if $multiLang.text_staff_role}{$multiLang.text_staff_role}{else}No Translate (Key Lang:text_staff_role){/if}.{/if}</span>
              <select class="form-control select2" name="staff_role" style="width:100%">
                <option value="">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate (Key Lang:text_please_select){/if} {if $multiLang.text_staff_role}{$multiLang.text_staff_role}{else}No Translate (Key Lang:text_staff_role){/if}</option>
                {foreach item=v from=$staff_role}
                <option value="{$v.id}" {if $edit.staff_role_id eq $v.id} selected{/if}{if $smarty.session.staff_info.staff_role eq $v.id}selected{/if}>{$v.name}</option>
                {/foreach}
              </select>
            </div>
            <div class="row">
              <div class="col-md-2">
                <div class="form-group">
                  <label>{if $multiLang.text_upload_photos}{$multiLang.text_upload_photos}{else}No Translate (Key Lang:text_upload_photos){/if} :</label></br>
                  <span class="text-danger">{if $error.no_image}{if $multiLang.text_browse}{$multiLang.text_browse}{else}No Translate (Key Lang:text_browse){/if} {if $multiLang.text_upload_photos}{$multiLang.text_upload_photos}{else}No Translate (Key Lang:text_upload_photos){/if}.{/if}</span>
                  {if $error.type eq 1}<div class="text-danger">{if $multiLang.text_error_type}{$multiLang.text_error_type}{else}No Translate (Key Lang:text_error_type){/if}</div>{/if}
                  {if $error.size eq 1}<div class="text-danger">{if $multiLang.text_error_file}{$multiLang.text_error_file}{else}No Translate (Key Lang:text_error_file){/if}</div>{/if}
                  <input type="file" name="image" id="file" onchange="readURL(this);" />
                  <input type="hidden" name="old_file" value="{$edit.photo}">
                </div>
              </div>
              <div class="col-md-9">
              <img class="img-thumbnail img-middle" id="blah" src="{if $edit.photo}/images/staff/thumbnail__{$edit.photo} {else} /images/user-default.png {/if}" width="100" style="height:103px">
              </br>
              <div class="text-danger">Max file size:8MB</div>
            </div>
            </div>
            <div class="form-group">
              {if $edit.id}
                <input type="hidden" name="id" value="{$edit.id}">
                <button type="submit" name="submit" class="btn btn-success"><i class="fa fa-check-circle-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate (Key Lang:button_update){/if}</button>
                <a href="{$admin_file}?task=staff_info" class="btn btn-danger"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate (Key Lang:button_cancel){/if}</a>
              {else}
                <button type="submit" class="btn btn-info"><i class="fa fa-check-circle-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate (Key Lang:button_save){/if}</button>
              {/if}
            </div>
          </form>
        </div>
      </div><!--panel panel-body-->
    </div><!--panel panel-default-->
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
            <th>{if $multiLang.text_title_name}{$multiLang.text_title_name}{else}No Translate (Key Lang:text_title_name){/if}</th>
            <th>{if $multiLang.text_title_gender}{$multiLang.text_title_gender}{else}No Translate (Key Lang:text_title_gender){/if}</th>
            <th>{if $multiLang.text_title_role}{$multiLang.text_title_role}{else}No Translate (Key Lang:text_title_role){/if}</th>
            <th>{if $multiLang.text_title_phone}{$multiLang.text_title_phone}{else}No Translate (Key Lang:text_title_phone){/if}</th>
            <th>{if $multiLang.text_title_status}{$multiLang.text_title_status}{else}No Translate (Key Lang:text_title_status){/if}</th>
            <th class="text-center" width="100px">{if $multiLang.text_title_action}{$multiLang.text_title_action}{else}No Translate (Key Lang:text_title_action){/if}</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$list_staff item= v }
          <tr>
            <td valign="top">{$v.name}</td>
            <td valign="top">{if $v.gender eq 1}Male{else}Female{/if}</td>
            <td valign="top">{$v.role_name}</td>
            <td valign="top">{$v.phone}</td>
            <td>{if $v.status eq 1}
            <button type="button" class="btn btn-success btn-xs" {if $v.staff_role_id|@count eq 1 and $v.staff_role_id eq 1}disabled{/if} data-toggle="modal" data-target="#myModal_{$v.id}" data-toggle1="tooltip" data-placement="top" title="{if $v.staff_role_id|@count eq 1 and $v.staff_role_id eq 1}Can not change status, Because this Super Admin.{else}{if $multiLang.text_click_change_status}{$multiLang.text_click_change_status}{else}No Translate (Key Lang:text_click_change_status){/if}{/if}">
              <i class="fa fa-circle" aria-hidden="true"></i>{if $multiLang.text_show_Active}{$multiLang.text_show_Active}{else}No Translate (Key Lang:text_show_Active){/if}
            </button>
            {elseif $v.status eq 2}
            <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$v.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.text_click_change_status}{$multiLang.text_click_change_status}{else}No Translate (Key Lang:text_click_change_status){/if}">
              <i class="fa fa-square" aria-hidden="true"></i> {if $multiLang.text_show_stope}{$multiLang.text_show_stope}{else}No Translate (Key Lang:text_show_stope){/if}
            </button>
            {/if}
            <!-- Trigger the modal with a button -->
            <div class="modal fade" id="myModal_{$v.id}" role="dialog">
              <div class="modal-dialog">
                <div class="panel panel-primary modal-content">
                  <div class="panel-heading modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate (Key Lang:text_confirmation){/if}</h4>
                  </div>
                  <div class="modal-body">
                    <p>{if $v.status eq 1}{if $multiLang.text_change_status}{$multiLang.text_change_status}{else}No Translate (Key Lang:text_change_status){/if} {if $multiLang.text_show_Active}{$multiLang.text_show_Active}?{else}No Translate (Key Lang:text_show_Active){/if}
                      {elseif $v.status eq 2}{if $multiLang.text_change_status}{$multiLang.text_change_status}{else}No Translate (Key Lang:text_change_status){/if} {if $multiLang.text_show_stope}{$multiLang.text_show_stope}?{else}No Translate (Key Lang:text_show_stope){/if}{/if}</p>
                  </div>
                  <div class="modal-footer">
                    <a href="{$admin_file}?task=staff_info&amp;action=change_status&amp;id={$v.id|escape}&amp;status={$v.status|escape}" class="btn btn-danger btn-md"><i class="fa fa-check-circle-o"></i> {if $multiLang.button_yes}{$multiLang.button_yes}{else}No Translate (Key Lang:button_yes){/if}</a>
                    <button type="button" class="btn btn-primary collapsed" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate (Key Lang:button_cancel){/if}</button>
                  </div>
                </div>
              </div>
            </div>
          <!-- Modal -->
          </td>
            <td class="text-center" valign="top">
              <button href="#View_Profile_{$v.id}" class= "btn btn-info btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_view_pro}{$multiLang.button_view_pro}{else}No Translate (Key Lang:button_view_pro){/if}" data-toggle= "modal"><i class="fa fa-eye"></i></button>
              <!-- Modal -->
              <div class="modal fade" id="View_Profile_{$v.id}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel">{if $multiLang.text_pro_info}{$multiLang.text_pro_info}{else}No Translate (Key Lang:text_pro_info){/if}</h4>
                    </div>
                    <div class="modal-body">
                    <img class="img-circle" src="{if $v.photo}/images/staff/thumbnail__{$v.photo} {else} /images/user-default.png {/if}" width="20%">
                     <dl class="dl-horizontal">
                        <dt>{if $multiLang.text_title_name}{$multiLang.text_title_name}{else}No Translate (Key Lang:text_title_name){/if}:</dt>
                        <dd class="text-left">{$v.name}</dd>

                        <dt>{if $multiLang.text_password}{$multiLang.text_password}{else}No Translate (Key Lang:text_password){/if}:</dt>
                        <dd class="text-left">{$v.password}</dd>

                        <dt>{if $multiLang.text_title_phone}{$multiLang.text_title_phone}{else}No Translate (Key Lang:text_title_phone){/if}:<dt>
                        <dd class="text-left">{$v.phone}</dd>

                        <dt>{if $multiLang.text_title_gender}{$multiLang.text_title_gender}{else}No Translate (Key Lang:text_title_gender){/if}:</dt>
                        <dd class="text-left">{if $v.gender eq 1}Male{else}Female{/if}</dd>

                        <dt>{if $multiLang.text_title_role}{$multiLang.text_title_role}{else}No Translate (Key Lang:text_title_role){/if}:</dt>
                        <dd class="text-left">{$v.role_name}</dd>
                      </dl>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=staff_info&amp;action=edit&amp;id={$v.id}" class="btn btn-success"><i class="fa fa-edit"></i> {if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate (Key Lang:button_edit){/if}</a>
                      <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle" aria-hidden="true"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate (Key Lang:button_close){/if}</button>
                    </div>
                  </div>
                </div>
              </div>
              <a href="{$admin_file}?task=staff_info&amp;action=edit&amp;id={$v.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate (Key Lang:button_edit){/if}"><i class="fa fa-edit"></i></a>
            </td>
          </tr>
          {/foreach}
        </tbody>
      </table>
    </div>
    {include file = "common/paginate.tpl"}
  </div>
</div>
{/block}
{block name="javascript"}
<script type="text/javascript">
$(function(){
  $("#the_select").change(function(){
    window.location='{$admin_file}?task=staff_info&kwd={$smarty.get.kwd}&status=' + this.value
  });
});
</script>
<script>
// $('#birth_date').datetimepicker({ locale: 'en', format: 'YYYY-MM-DD' });
 $('[data-toggle="popover"]').popover();
	$(function () {
    $('[data-toggle1="tooltip"]').tooltip()
  });
</script>
{/block}
