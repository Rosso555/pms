{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$psychologist_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_home}{$multiLang.text_home}{else}No Translate(Key Lang: text_home){/if}</li>
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if} {if $multiLang.text_list}{$multiLang.text_list}{else}No Translate(Key Lang: text_list){/if}</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <form class="form-inline" role="form" action="{$psychologist_file}" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="test_psychologist">
          <div class="col-md-3 form-group" style="margin-bottom:5px;">
            <select class="form-control select2" name="cid" style="width:100%;">
              <option value="">---Select Category---</option>
              {foreach from=$category item=v}
              <option value="{$v.id}" {if $smarty.get.cid eq $v.id}selected{/if}>{$v.name}</option>
              {/foreach}
            </select>
          </div>
          <div class="col-md-3 form-group" style="margin-bottom:5px;">
            <select class="form-control select2" name="tid" style="width:100%;">
              <option value="">---Select Test---</option>
              {foreach from=$test item=v}
              <option value="{$v.id}" {if $smarty.get.tid eq $v.id}selected{/if}>{$v.title}</option>
              {/foreach}
            </select>
          </div>
          <div class="col-md-3 form-group" style="margin-bottom:5px;">
            <select class="form-control select2" name="status" style="width:100%;">
              <option value="">--- Select Status ---</option>
              <option value="1" {if $smarty.get.status eq 1}selected{/if}>Pendding...</option>
              <option value="2" {if $smarty.get.status eq 2}selected{/if}>Completed</option>
            </select>
          </div>
          <div class="col-md-3 form-group" style="margin-bottom:5px;">
            <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
          </div>
        </form>
        </div>
      </div>
    </div><!--panel panel-body-->

    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
            <th>{if $multiLang.text_category}{$multiLang.text_category}{else}No Translate(Key Lang: text_category){/if}</th>
            <th>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</th>
            <th>{if $multiLang.text_status}{$multiLang.text_status}{else}No Translate(Key Lang: text_status){/if}</th>
            <th width="100">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listTestPsychologist|@count gt 0}
        <tbody>
        {foreach from = $listTestPsychologist item = data key=k}
          <tr>
            <td>{$data.catName}</td>
            <td>{$data.title}</td>
            <td>
              {if $data.status eq 1}
              <span class="label label-warning">
                <i class="fa fa-stop-circle-o" aria-hidden="true"></i> Pendding...
              </span>
              {else}
              <span class="label label-info">
                <i class="fa fa-check-circle"></i> Completed
              </span>
              {/if}
            </td>
            <td>
              <a href="{$psychologist_file}?task=test_question&amp;tid={$data.test_id}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top"
              title="{if $multiLang.button_view}{$multiLang.button_view}{else}No Translate(Key Lang: button_view){/if}"><i class="fa fa-eye"></i></a>
              <!-- Trigger the modal with a button -->
              <button type="button" class="btn btn-danger btn-xs" {if $data.status eq 2}disabled{/if} data-toggle="modal" data-target="#myModal_{$data.id}" data-toggle1="tooltip" data-placement="top"
              title="{if $data.status eq 1}{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}{else}{if $multiLang.button_can_not_delete}{$multiLang.button_can_not_delete}{else}No Translate(Key Lang: button_can_not_delete){/if}{/if}"><i class="fa fa-trash-o"></i></button>
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
                        {if $multiLang.text_confirm_delete}{$multiLang.text_confirm_delete}{else}No Translate(Key Lang: text_confirm_delete){/if}
                         <b>({$data.username|escape} ~ {$data.title|escape})</b>?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$psychologist_file}?task=test_psychologist&amp;action=delete&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
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
          <td colspan="4"><h4 style="text-align:center">{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h4></td>
        </tr>
        {/if}
      </table>
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->

{/block}
