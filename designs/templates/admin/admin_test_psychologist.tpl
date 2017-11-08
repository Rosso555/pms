{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_test_psychologist}{$multiLang.text_test_psychologist}{else}No Translate(Key Lang: text_test_psychologist){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_test_psychologist}{$multiLang.text_test_psychologist}{else}No Translate(Key Lang: text_test_psychologist){/if}</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=test_psychologist" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="test_psychologist">
          <div class="form-group" style="margin-bottom:5px;">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_test_psychologist}{$multiLang.button_add_test_psychologist}{else}No Translate(Key Lang: button_add_test_psychologist){/if}
            </button>
          </div>
          &nbsp;&nbsp;&nbsp;
          <div class="form-group select2_search_inline" style="margin-bottom:5px;">
            <select class="form-control select2_search" name="psy_id" style="width:100%;">
              <option value="">---Select Psychologist---</option>
              {foreach from=$psychologist item=v}
              <option value="{$v.id}" {if $smarty.get.psy_id eq $v.id}selected{/if}>{$v.first_name} {$v.last_name}</option>
              {/foreach}
            </select>
          </div>
          <div class="form-group select2_search_inline" style="margin-bottom:5px;">
            <select class="form-control select2_search" name="tid" style="width:100%;">
              <option value="">---Select Test---</option>
              {foreach from=$test item=v}
              <option value="{$v.id}" {if $smarty.get.tid eq $v.id}selected{/if}>{$v.title}</option>
              {/foreach}
            </select>
          </div>
          <div class="form-group select2_search_inline" style="margin-bottom:5px;">
            <select class="form-control select2_search" name="status" style="width:100%;">
              <option value="">--- Select Status ---</option>
              <option value="1" {if $smarty.get.status eq 1}selected{/if}>Pendding...</option>
              <option value="2" {if $smarty.get.status eq 2}selected{/if}>Completed</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:5px;">
            <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getTestPsy.id} in {/if}">
          {if $getTestPsy.id}
          <form action="{$admin_file}?task=test_psychologist&amp;action=edit&amp;id={$getTestPsy.id}" method="post">
          {else}
          <form action="{$admin_file}?task=test_psychologist&amp;action=add" method="post">
          {/if}
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_psychologist}{$multiLang.text_psychologist}{else}No Translate(Key Lang: text_psychologist){/if}:</label>
                  {if $error.psy_id}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_psychologist}{$multiLang.text_psychologist}{else}No Translate(Key Lang: text_psychologist){/if}</span>
                  {/if}
                  <select class="form-control select2" name="psy_id" style="width:100%">
                    <option value="">--- {if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_psychologist}{$multiLang.text_psychologist}{else}No Translate(Key Lang: text_psychologist){/if} ---</option>
                    {foreach from=$psychologist item=data}
                    <option value="{$data.id}" {if $smarty.session.test_psy.psy_id}{if $smarty.session.test_psy.psy_id eq $data.id}selected{/if}{else}{if $getTestPsy.psychologist_id eq $data.id}selected{/if}{/if}>{$data.first_name} {$data.last_name}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</label>
                  {if $error.testid}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</span>
                  {/if}
                  <select class="form-control select2_test_psy" {if $getTestPsy.id} name="test" {else} name="test[]" multiple="multiple" {/if} style="width:100%">
                    {foreach from=$test item=data}
                    <option value="{$data.id}" {if $getTestPsy.test_id}{if $getTestPsy.test_id eq $data.id}selected{/if}{else}{foreach from=$smarty.session.test_psy.test item=v}{if $v eq $data.id}selected{/if}{/foreach}{/if}>{$data.title}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getTestPsy.id}
                    <input type="hidden" name="id" value="{$getTestPsy.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=test_psychologist" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
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
            <th>{if $multiLang.text_psychologist}{$multiLang.text_psychologist}{else}No Translate(Key Lang: text_psychologist){/if}</th>
            <th>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</th>
            <th>{if $multiLang.text_status}{$multiLang.text_status}{else}No Translate(Key Lang: text_status){/if}</th>
            <th width="100">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $testPsychologist|@count gt 0}
        <tbody>
        {foreach from = $testPsychologist item = data key=k}
          <tr>
            <td>{$data.first_name}</td>
            <td>{$data.title}</td>
            <td>
              {if $data.status eq 1}
              <span class="label label-warning"><i class="fa fa-stop-circle-o" aria-hidden="true"></i> Pendding...</span>
              {else}
              <span class="label label-info"><i class="fa fa-check-circle"></i> Completed</span>
              {/if}
            </td>
            <td>
              <!-- Trigger the modal with a button -->
              <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#mySend_{$data.id}" data-toggle1="tooltip" data-placement="top"
              title="Send mail to psychologist!"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
              <!-- Modal -->
              <div class="modal fade" id="mySend_{$data.id}" role="dialog">
                <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="panel panel-primary modal-content">
                    <div class="panel-heading modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="panel-title modal-title">Send Mail</h4>
                    </div>
                    <div class="modal-body">
                      <form action="#">
                        <div class="form-group">
                          <label for="name"><span style="color: red">*</span> Your name:</label>
                          <input type="text" class="form-control" id="name" placeholder="Enter name" name="name">
                        </div>
                        <div class="form-group">
                          <label for="sub"><span style="color: red">*</span> Subject:</label>
                          <input type="text" class="form-control" id="sub" placeholder="Enter subject" name="subject">
                        </div>
                        <div class="form-group">
                          <label for="comment">Message:</label>
                          <textarea class="form-control" rows="5" id="comment"></textarea>
                        </div>
                      </form>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-info btn-md"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send</i></Button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
              <a {if $data.status eq 1}href="{$admin_file}?task=test_psychologist&amp;action=edit&amp;id={$data.id}"{/if} class="btn btn-success btn-xs" {if $data.status eq 2}disabled{/if} data-toggle1="tooltip" data-placement="top"
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
                         <b>({$data.first_name|escape} {$data.last_name|escape} ~ {$data.title|escape})</b>?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=test_psychologist&amp;action=delete&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</i></a>
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
