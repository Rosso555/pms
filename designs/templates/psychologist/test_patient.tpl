{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$psychologist_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_test_patient}{$multiLang.text_test_patient}{else}No Translate(Key Lang: text_test_patient){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_test_patient}{$multiLang.text_test_patient}{else}No Translate(Key Lang: text_test_patient){/if}</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$psychologist_file}?task=test_patient" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="test_patient">
          <div class="form-group" style="margin-bottom:5px;">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_test_patient}{$multiLang.button_add_test_patient}{else}No Translate(Key Lang: button_add_test_patient){/if}
            </button>
          </div>
          <div class="form-group" style="margin-bottom:5px;">
            <select class="form-control select2" name="pat_id" style="width:100%;">
              <option value="">---Select {if $multiLang.text_patient}{$multiLang.text_patient}{else}No Translate(Key Lang: text_patient){/if}---</option>
              {foreach from=$patient item=v}
              <option value="{$v.id}" {if $smarty.get.pat_id eq $v.id}selected{/if}>{$v.username}</option>
              {/foreach}
            </select>
          </div>
          <div class="form-group" style="margin-bottom:5px;">
            <select class="form-control select2" name="tid" style="width:100%;">
              <option value="">---Select Test---</option>
              {foreach from=$test item=v}
              <option value="{$v.id}" {if $smarty.get.tid eq $v.id}selected{/if}>{$v.title}</option>
              {/foreach}
            </select>
          </div>
          <div class="form-group" style="margin-bottom:5px;">
            <select class="form-control select2" name="status" style="width:100%;">
              <option value="">--- Select Status ---</option>
              <option value="1" {if $smarty.get.status eq 1}selected{/if}>Pendding...</option>
              <option value="2" {if $smarty.get.status eq 2}selected{/if}>Completed</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:5px;">
            <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getTestPat.id} in {/if}">
          {if $getTestPat.id}
          <form action="{$psychologist_file}?task=test_patient&amp;action=edit&amp;id={$getTestPat.id}" method="post">
          {else}
          <form action="{$psychologist_file}?task=test_patient" method="post">
          {/if}
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</label>
                  {if $error.testid}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</span>
                  {/if}
                  <select class="form-control select2_test_psy" {if $getTestPat.id} name="test" {else} name="test[]" multiple="multiple" {/if} style="width:100%">
                    {foreach from=$test item=data}
                    <option value="{$data.id}" {if $smarty.session.test_patient.test}{if $smarty.session.test_patient.test eq $data.id}selected{/if}{else}{if $getTestPat.test_id eq $data.id}selected{/if}{/if}>{$data.title}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_patient}{$multiLang.text_patient}{else}No Translate(Key Lang: text_patient){/if}:</label>
                  {if $error.psy_id}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_patient}{$multiLang.text_patient}{else}No Translate(Key Lang: text_patient){/if}</span>
                  {/if}
                  <select class="form-control select2" name="pat_id" style="width:100%">
                    <option value="">--- {if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_patient}{$multiLang.text_patient}{else}No Translate(Key Lang: text_patient){/if} ---</option>
                    {foreach from=$patient item=data}
                    <option value="{$data.id}" {if $smarty.session.test_patient.pat_id}{if $smarty.session.test_patient.pat_id eq $data.id}selected{/if}{else}{if $getTestPat.patient_id eq $data.id}selected{/if}{/if}>{$data.username}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getTestPat.id}
                    <input type="hidden" name="id" value="{$getTestPat.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$psychologist_file}?task=test_patient" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
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
            <th>{if $multiLang.text_patient}{$multiLang.text_patient}{else}No Translate(Key Lang: text_patient){/if}</th>
            <th>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</th>
            <th>{if $multiLang.text_status}{$multiLang.text_status}{else}No Translate(Key Lang: text_status){/if}</th>
            <th width="100">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $testPatient|@count gt 0}
        <tbody>
        {foreach from = $testPatient item = data key=k}
          <tr>
            <td>{$data.username}</td>
            <td>{$data.title}</td>
            <td>
              {if $data.status eq 1}
              <button type="button" class="btn btn-danger btn-xs">
                <i class="fa fa-stop-circle-o" aria-hidden="true"></i> Pendding...
              </button>
              {else}
              <button type="button" class="btn btn-success btn-xs">
                <i class="fa fa-check-circle"></i> Completed
              </button>
              {/if}
            </td>
            <td>
              <a {if $data.status eq 1}href="{$psychologist_file}?task=test_patient&amp;action=edit&amp;id={$data.id}"{/if} class="btn btn-success btn-xs" {if $data.status eq 2}disabled{/if} data-toggle1="tooltip" data-placement="top"
              title="{if $data.status eq 1}{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}{else}{if $multiLang.button_can_not_edit}{$multiLang.button_can_not_edit}{else}No Translate(Key Lang: button_can_not_edit){/if}{/if}"><i class="fa fa-edit"></i></a>
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
                      <a href="{$psychologist_file}?task=test_patient&amp;action=delete&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</i></a>
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
