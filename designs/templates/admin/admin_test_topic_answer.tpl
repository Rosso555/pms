{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTest" href="{$admin_file}?task=test">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_test_topic_answer}{$multiLang.text_test_topic_answer}{else}No Translate(Key Lang: text_test_topic_answer){/if}</li>
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
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_test_topic_answer}{$multiLang.text_test_topic_answer}{else}No Translate(Key Lang: text_test_topic_answer){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</b> {$test.title}
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=test_topic_answer&amp;tid={$smarty.get.tid}" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="test_topic_answer">
          <input type="hidden" name="tid" value="{$smarty.get.tid}">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_test_topic_answer}{$multiLang.button_add_test_topic_answer}{else}No Translate(Key Lang: button_add_test_topic_answer){/if}
            </button>
          </div>
          <div class="input-group" style="float: right;">
            <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="{if $multiLang.text_search_by}{$multiLang.text_search_by}{else}No Translate(Key Lang: text_search_by){/if} {if $multiLang.text_name}{$multiLang.text_name}{else}No Translate(Key Lang: text_name){/if}...">
            <span class="input-group-btn">
              <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate(Key Lang: button_search){/if}</button>
            </span>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getTestTopicAnsByID.id} in {/if}">
          {if $getTestTopicAnsByID.id}
          <form action="{$admin_file}?task=test_topic_answer&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$getTestTopicAnsByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=test_topic_answer&amp;action=add&amp;tid={$smarty.get.tid}" method="post">
          {/if}
            <input type="hidden" name="test_id" value="{$smarty.get.tid}">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="topic_id"><span style="color: red">*</span> {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}:</label>
                  {if $error.topic_id}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}</span>
                  {/if}
                  {if $error.is_key_test_topic_ans_exist}
                    <span style="color: red"> {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} {if $multiLang.text_is_existed}{$multiLang.text_is_existed}{else}No Translate(Key Lang: text_is_existed){/if}</span>
                  {/if}
                  <select class="form-control" name="topic_id" style="width:100%">
                    <option value="">--- Select Topic ---</option>
                    {foreach from=$listTestTopic item=data}
                    <option value="{$data.topic_id}" {if $getTestTopicAnsByID.topic_id}{if $getTestTopicAnsByID.topic_id eq $data.topic_id}selected{/if}{else}{if $smarty.session.test_topic_answer.topic_id eq $data.topic_id}selected{/if}{/if}>{$data.name}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_average}{$multiLang.text_average}{else}No Translate(Key Lang: text_average){/if}:</label>
                  {if $error.average}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_average}{$multiLang.text_average}{else}No Translate(Key Lang: text_average){/if}</span>
                  {/if}
                  <input type="text" name="average" class="form-control" placeholder="Example: 123..."
                  value="{if $getTestTopicAnsByID.average}{$getTestTopicAnsByID.average}{else}{$smarty.session.test_topic_answer.average}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_standard}{$multiLang.text_standard}{else}No Translate(Key Lang: text_standard){/if}:</label>
                  {if $error.stdd}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_standard}{$multiLang.text_standard}{else}No Translate(Key Lang: text_standard){/if}</span>
                  {/if}
                  <input type="text" name="stdd" class="form-control" placeholder="Example: 123.."
                  value="{if $getTestTopicAnsByID.stdd}{$getTestTopicAnsByID.stdd}{else}{$smarty.session.test_topic_answer.stdd}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_multiplier}{$multiLang.text_multiplier}{else}No Translate(Key Lang: text_multiplier){/if}:</label>
                  {if $error.multiplier}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_multiplier}{$multiLang.text_multiplier}{else}No Translate(Key Lang: text_multiplier){/if}</span>
                  {/if}
                  <input type="text" name="multiplier" class="form-control" placeholder="Example: 123..."
                  value="{if $getTestTopicAnsByID.multiplier}{$getTestTopicAnsByID.multiplier}{else}{$smarty.session.test_topic_answer.multiplier}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_constant}{$multiLang.text_constant}{else}No Translate(Key Lang: text_constant){/if}:</label>
                  {if $error.constant}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_constant}{$multiLang.text_constant}{else}No Translate(Key Lang: text_constant){/if}</span>
                  {/if}
                  <input type="text" name="constant" class="form-control" placeholder="Example: 123.."
                  value="{if $getTestTopicAnsByID.constant}{$getTestTopicAnsByID.constant}{else}{$smarty.session.test_topic_answer.constant}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getTestTopicAnsByID.id}
                    <input type="hidden" name="id" value="{$getTestTopicAnsByID.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=test_topic_answer&amp;tid={$smarty.get.tid}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_concel){/if}</a>
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
          <th>{if $multiLang.text_average}{$multiLang.text_average}{else}No Translate(Key Lang: text_average){/if}</th>
          <th>{if $multiLang.text_standard}{$multiLang.text_standard}{else}No Translate(Key Lang: text_standard){/if}</th>
          <th>{if $multiLang.text_multiplier}{$multiLang.text_multiplier}{else}No Translate(Key Lang: text_multiplier){/if}</th>
          <th>{if $multiLang.text_constant}{$multiLang.text_constant}{else}No Translate(Key Lang: text_constant){/if}</th>
          <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listTestTopicAnswer|@count gt 0}
        <tbody>
        {foreach from = $listTestTopicAnswer item = data key=k}
          <tr>
            <td>{$data.name}</td>
            <td>{$data.average}</td>
            <td>{$data.stdd}</td>
            <td>{$data.multiplier}</td>
            <td>{$data.constant}</td>
            <td>
              <a href="{$admin_file}?task=test_topic_answer&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
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
                        {if $multiLang.text_test_topic_answer}{$multiLang.text_test_topic_answer}{else}No Translate(Key Lang: text_test_topic_answer){/if}
                        <b>({$data.name|escape})</b> ?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=test_topic_answer&amp;action=delete&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"></i> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</a>
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

      <a id="btnBack" href="javascript:history.back()" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
{block name="javascript"}
<script>
  //Get previous url
  var urlBack =  document.referrer;
  var url = '';
  if(urlBack !== '') url = getUrlPrevious(urlBack);
  if(url.task === 'test') localStorage.setItem('urlTest',urlBack);
  //Get session url
  var getUrlBack = localStorage.getItem('urlTest');
  if(getUrlBack !== null){
    $("#btnBack").attr("href", getUrlBack);
    $("#bCrumbTest").attr("href", getUrlBack);
  }
  //End previous url
</script>
{/block}
