{extends file="admin/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a href="{$admin_file}?task=test">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_test_analysis_topic}{$multiLang.text_test_analysis_topic}{else}No Translate(Key Lang: text_test_analysis_topic){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
{if $smarty.cookies.checkTopicAnalysis}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot delete "<strong>{$smarty.cookies.checkTopicAnalysis}</strong>" because it has been used.
  </div>
{/if}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_test_analysis_topic}{$multiLang.text_test_analysis_topic}{else}No Translate(Key Lang: text_test_analysis_topic){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</b> {$test.title}
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=test_topic_analysis&amp;tid={$smarty.get.tid}" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="test_topic_analysis">
          <input type="hidden" name="tid" value="{$smarty.get.tid}">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_test_analysis_topic}{$multiLang.button_add_test_analysis_topic}{else}No Translate(Key Lang: button_add_test_analysis_topic){/if}
            </button>
          </div>
          <div class="input-group" style="float: right;">
            <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="{if $multiLang.text_search_by}{$multiLang.text_search_by}{else}No Translate(Key Lang: text_search_by){/if} {if $multiLang.text_name}{$multiLang.text_name}{else}No Translate(Key Lang: text_name){/if}...">
            <span class="input-group-btn">
              <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate(Key Lang: button_search){/if}</button>
            </span>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getTestTopicAsisByID.id} in {/if}">
          {if $getTestTopicAsisByID.id}
          <form action="{$admin_file}?task=test_topic_analysis&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$getTestTopicAsisByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=test_topic_analysis&amp;tid={$smarty.get.tid}" method="post">
          {/if}
            <input type="hidden" name="test_id" value="{$smarty.get.tid}">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="topic_id"><span style="color: red">*</span> {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}:</label>
                  {if $error.topic_id}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}</span>
                  {/if}
                  <select class="form-control" name="topic_id" style="width:100%">
                    <option value="">--- Select Topic ---</option>
                    {foreach from=$listTestTopic item=data}
                    <option value="{$data.topic_id}" {if $getTestTopicAsisByID.topic_id}{if $getTestTopicAsisByID.topic_id eq $data.topic_id}selected{/if}{else}{if $smarty.session.test_topic_analysis.topic_id eq $data.topic_id}selected{/if}{/if}>{$data.name}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="ana_topic_id"><span style="color: red">*</span> {if $multiLang.text_analysis_topic}{$multiLang.text_analysis_topic}{else}No Translate(Key Lang: text_analysis_topic){/if}:</label>
                  {if $error.ana_topic_id}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_analysis_topic}{$multiLang.text_analysis_topic}{else}No Translate(Key Lang: text_analysis_topic){/if}</span>
                  {/if}
                  <select class="form-control" name="ana_topic_id" style="width:100%">
                    <option value="">--- Select Analysis Topic ---</option>
                    {foreach from=$listTopicAnalysis item=data}
                    <option value="{$data.id}" {if $getTestTopicAsisByID.topic_analysis_id}{if $getTestTopicAsisByID.topic_analysis_id eq $data.id}selected{/if}{else}{if $smarty.session.test_topic_analysis.ana_topic_id eq $data.id}selected{/if}{/if}>
                      {$data.name}
                    </option>
                    {/foreach}
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="less_than"><span style="color: red">*</span> {if $multiLang.text_less_than_value}{$multiLang.text_less_than_value}{else}No Translate(Key Lang: text_less_than_value){/if}:</label>
                  {if $error.less_than}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_less_than_value}{$multiLang.text_less_than_value}{else}No Translate(Key Lang: text_less_than_value){/if}</span>
                  {/if}
                  <input type="text" id="less_than" class="form-control" name="less_than_value" placeholder="Example: 10.2" value="{if $smarty.session.test_topic_analysis.less_than_value}{$smarty.session.test_topic_analysis.less_than_value}{elseif $getTestTopicAsisByID.less_than}{$getTestTopicAsisByID.less_than}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="bigger_than"><span style="color: red">*</span> {if $multiLang.text_biger_than_value}{$multiLang.text_biger_than_value}{else}No Translate(Key Lang: text_biger_than_value){/if}:</label>
                  {if $error.bigger_than}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_biger_than_value}{$multiLang.text_biger_than_value}{else}No Translate(Key Lang: text_biger_than_value){/if}</span>
                  {/if}
                  <input type="text" id="bigger_than" name="bigger_than_value" class="form-control" placeholder="Example: 10.2"
                  value="{if $smarty.session.test_topic_analysis.bigger_than_value}{$smarty.session.test_topic_analysis.bigger_than_value}{elseif $getTestTopicAsisByID.bigger_than}{$getTestTopicAsisByID.bigger_than}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getTestTopicAsisByID.id}
                    <input type="hidden" name="test_topic_asis_id" value="{$getTestTopicAsisByID.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=test_topic_analysis&amp;tid={$smarty.get.tid}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_concel){/if}</a>
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
          <th>{if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}</th>
          <th>{if $multiLang.text_analysis_topic}{$multiLang.text_analysis_topic}{else}No Translate(Key Lang: text_analysis_topic){/if}</th>
          <th>{if $multiLang.text_less_than_value}{$multiLang.text_less_than_value}{else}No Translate(Key Lang: text_less_than_value){/if}</th>
          <th>{if $multiLang.text_biger_than_value}{$multiLang.text_biger_than_value}{else}No Translate(Key Lang: text_biger_than_value){/if}</th>
          <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listTestTopicAnalysis|@count gt 0}
        <tbody>
        {foreach from = $listTestTopicAnalysis item = data key=k}
          <tr>
            <td>{$data.topic_name}</td>
            <td>{$data.topic_analysis_name}</td>
            <td>{$data.less_than}</td>
            <td>{$data.bigger_than}</td>
            <td>
              <a href="{$admin_file}?task=test_topic_analysis&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
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
                      <p>{if $multiLang.text_confirmation_delete}{$multiLang.text_confirmation_delete}{else}No Translate(Key Lang:text_confirmation_delete){/if}
                        {if $multiLang.text_test_analysis_topic}{$multiLang.text_test_analysis_topic}{else}No Translate(Key Lang: text_test_analysis_topic){/if}
                        <b>({$data.topic_name|escape}/{$data.topic_analysis_name}: {$data.less_than}~{$data.bigger_than})</b> ?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=test_topic_analysis&amp;action=delete&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"></i> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</a>
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
          <td colspan="7"><h4 style="text-align:center">{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h4></td>
        </tr>
        {/if}
      </table>
      <a href="{$admin_file}?task=test" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
