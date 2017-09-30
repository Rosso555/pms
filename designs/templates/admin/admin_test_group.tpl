{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_test_group}{$multiLang.text_test_group}{else}No Translate(Key Lang: text_test_group){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
{if $smarty.cookies.checkTestGroup}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot delete "<strong>{$smarty.cookies.checkTestGroup}</strong>" because it has been used.
  </div>
{/if}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_test_group}{$multiLang.text_test_group}{else}No Translate(Key Lang: text_test_group){/if}</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=test_group" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="test">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_test_group}{$multiLang.button_add_test_group}{else}No Translate(Key Lang: button_add_test_group){/if}
            </button>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getTestGroupByID.id} in {/if}">
          {if $getTestGroupByID.id}
          <form action="{$admin_file}?task=test_group&amp;action=edit&amp;catid={$smarty.get.catid}&amp;id={$getTestGroupByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=test_group&amp;action=add&amp;catid={$smarty.get.catid}" method="post">
          {/if}
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</label>
                  {if $error.testid}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if}</span>
                  {/if}
                  <select class="form-control select2" name="test" style="width:100%">
                    <option value="">--- {if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if} ---</option>
                    {foreach from=$test item=data}
                    <option value="{$data.id}" {if $smarty.session.test_group.test}{if $smarty.session.test_group.test eq $data.id}selected{/if}{else}{if $getTestGroupByID.test_id eq $data.id}selected{/if}{/if}>{$data.title}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}:</label>
                  {if $error.title}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}.</span>
                  {/if}
                  <input type="text" name="title" class="form-control" placeholder="Title"
                  value="{if $smarty.session.test_group.title}{$smarty.session.test_group.title}{elseif $getTestGroupByID.title}{$getTestGroupByID.title}{/if}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_view_Order}{$multiLang.text_view_Order}{else}No Translate(Key Lang: text_view_Order){/if}:</label>
                  {if $error.view_order}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_view_Order}{$multiLang.text_view_Order}{else}No Translate(Key Lang: text_view_Order){/if}.</span>
                  {/if}
                  <input type="text" name="view_order" class="form-control" placeholder="Example: 123..."
                  value="{if $smarty.session.test_group.view_order}{$smarty.session.test_group.view_order}{elseif $getTestGroupByID.view_order}{$getTestGroupByID.view_order}{/if}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getTestGroupByID.id}
                    <input type="hidden" name="id" value="{$getTestGroupByID.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=test_group" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
                  {else}
                    <button type="submit" name="butsubmit" class="btn btn-info"><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate(Key Lang: button_save){/if}</button>
                  {/if}
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div><!--panel panel-body-->
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
            <th>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</th>
            <th>{if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</th>
            <th>{if $multiLang.text_view_Order}{$multiLang.text_view_Order}{else}No Translate(Key Lang: text_view_Order){/if}</th>
            <th>{if $multiLang.text_group_question}{$multiLang.text_group_question}{else}No Translate(Key Lang: text_group_question){/if}</th>
            <th width="100">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listTestGroup|@count gt 0}
        <tbody>
        {foreach from = $listTestGroup item = data key=k}
          <tr>
            <td><a href="{$admin_file}?task=test_group&amp;tid={$data.test_id}">{$data.title_test}</a></td>
            <td>{$data.title}</td>
            <td>{$data.view_order}</td>
            <td><span class="badge">{$data.total_group_question}</span></td>
            <td>
              <a href="{$admin_file}?task=test_group&amp;action=edit&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
              <!-- Trigger the modal with a button -->
              <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$data.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}"><i class="fa fa-trash-o"></i></button>
              <!-- Modal -->
              <div class="modal fade" id="myModal_{$data.id}" role="dialog">
                <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="panel panel-primary modal-content">
                    <div class="panel-heading modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate(Key Lang: text_confirmation){/if}</h4>
                    </div>
                    <div class="modal-body">
                      <p>
                        {if $multiLang.text_delete_test_group}{$multiLang.text_delete_test_group}{else}No Translate(Key Lang: text_delete_test_group){/if}
                         <b>({$data.title_test|escape} ~ {$data.title|escape})</b>?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=test_group&amp;action=delete&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
              <a href="{$admin_file}?task=test_group_question&amp;tid={$data.test_id}&amp;tgid={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_test_group_question}{$multiLang.button_add_test_group_question}{else}No Translate(Key Lang: button_add_test_group_question){/if}"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
            </td>
          </tr>
        {/foreach}
        </tbody>
        {else}
        <tr>
          <td colspan="7"><h4 style="text-align:center">{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h4></td>
        </tr>
        {/if}
      </table>
      {if $smarty.get.tid}<a href="{$admin_file}?task=test_group" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate (Key Lang: text_back){/if}</a>{/if}
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
