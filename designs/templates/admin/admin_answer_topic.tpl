{extends file="admin/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTQue" href="{$admin_file}?task=test_question&amp;tid={$smarty.get.tid}">{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</a></li>
  <li><a id="bCrumbAns" href="{$admin_file}?task=answer&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}">{if $multiLang.text_answer}{$multiLang.text_answer}{else}No Translate(Key Lang: text_answer){/if}</a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_answer_topic_value}{$multiLang.text_answer_topic_value}{else}No Translate(Key Lang: text_answer_topic_value){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
{if $error.checkAnswer}
<div class="alert alert-danger">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Sorry! you can not insert answer than one.
</div>
{/if}
{if $smarty.cookies.checkAnswer}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot delete "<strong>{$smarty.cookies.topicTitle}</strong>", because it has been used in survey. You want to permanently delete. <a href="{$admin_file}?task=answer_topic&amp;action=delete_permanently&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;ans_id={$smarty.get.ans_id}&amp;id={$smarty.cookies.ans_topic_id}" class="btn btn-danger btn-xs">Yes, Delete it.</a>
  </div>
{/if}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_answer_topic_value}{$multiLang.text_answer_topic_value}{else}No Translate(Key Lang: text_answer_topic_value){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</b> {$test.title}
    </div>
    <div class="box_title">
      <b>{if $multiLang.text_question}{$multiLang.text_question}{else}No Translate(Key Lang: text_question){/if}:</b> {$question.title} (Type:{if $question.type eq 1} Text Input{elseif $question.type eq 2}Text Area{elseif $question.type eq 3}Redio{elseif $question.type eq 4}CheckBox{/if})
    </div>
    <div class="box_title">
      <b>{if $multiLang.text_answer}{$multiLang.text_answer}{else}No Translate(Key Lang: text_answer){/if}:</b> {$answer.title}
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=answer" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="tqid" value="{$smarty.get.tqid}">
          <input type="hidden" name="task" value="answer">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_answer_topic_value}{$multiLang.button_add_answer_topic_value}{else}No Translate(Key Lang: button_add_answer_topic_value){/if}
            </button>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getAnswerTopicByID.id} in {/if}">
          {if $getAnswerTopicByID.id}
          <form action="{$admin_file}?task=answer_topic&amp;action=edit&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;ans_id={$smarty.get.ans_id}&amp;id={$getAnswerTopicByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=answer_topic&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;ans_id={$smarty.get.ans_id}" method="post">
          {/if}
            <input type="hidden" name="ans_id" value="{$smarty.get.ans_id}">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</label>
                  {if $error.topic_id}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</span>
                  {/if}
                  {if $error.is_exist_topic}
                    <span style="color: red">{if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} {if $multiLang.text_is_existed}{$multiLang.text_is_existed}{else}No Translate(Key Lang: text_is_existed){/if} </span>
                  {/if}
                  <select class="form-control select2" name="topic" style="width:100%">
                    <option value="">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if} ---</option>
                    {foreach from=$topicResult item=data}
                    <option value="{$data.topic_id}" {if $getAnswerTopicByID.topic_id}{if $getAnswerTopicByID.topic_id eq $data.topic_id}selected{/if}{else}{if $smarty.session.answer_topic.topic eq $data.topic_id}selected{/if}{/if}>{$data.name}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_default_value}{$multiLang.text_default_value}{else}No Translate(Key Lang: text_default_value){/if}</label>
                  {if $error.default_value}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_default_value}{$multiLang.text_default_value}{else}No Translate(Key Lang: text_default_value){/if}</span>
                  {/if}
                  <input type="text" class="form-control" name="default_value" placeholder="Example: 10.2" value="{if $smarty.session.answer_topic.default_value}{$smarty.session.answer_topic.default_value}{elseif $getAnswerTopicByID.default_value}{$getAnswerTopicByID.default_value}{else}{$answer.view_order}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <div class="form-group">
                    <label for="title"> {if $multiLang.text_assign_value}{$multiLang.text_assign_value}{else}No Translate(Key Lang: text_assign_value){/if}</label>
                    <input type="text" name="assign_value" class="form-control" placeholder="Example: 10.2"
                    value="{if $smarty.session.answer_topic.assign_value}{$smarty.session.answer_topic.assign_value}{elseif $getAnswerTopicByID.assign_value}{$getAnswerTopicByID.assign_value}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"> {if $multiLang.text_weight_value}{$multiLang.text_weight_value}{else}No Translate(Key Lang: text_weight_value){/if}</label>
                  <input type="text" class="form-control" name="weight_value" placeholder="Example: 10.2" value="{if $smarty.session.answer_topic.weight_value}{$smarty.session.answer_topic.weight_value}{elseif $getAnswerTopicByID.weight_value}{$getAnswerTopicByID.weight_value}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getAnswerTopicByID.id}
                    <input type="hidden" name="id" value="{$getAnswerTopicByID.id}" />
                    <button type="submit" class="btn btn-success" {if $question.type eq 1 OR $question.type eq 2}disabled{/if}><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=answer_topic&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;ans_id={$smarty.get.ans_id}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_concel){/if}</a>
                  {else}
                    <button type="submit" name="butsubmit" class="btn btn-info" {if $question.type eq 1 OR $question.type eq 2}disabled{/if}><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate(Key Lang: button_save){/if}</button>
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
          <th>{if $multiLang.text_default_value}{$multiLang.text_default_value}{else}No Translate(Key Lang: text_default_value){/if}</th>
          <th>{if $multiLang.text_assign_value}{$multiLang.text_assign_value}{else}No Translate(Key Lang: text_assign_value){/if}</th>
          <th>{if $multiLang.text_weight_value}{$multiLang.text_weight_value}{else}No Translate(Key Lang: text_weight_value){/if}</th>
          <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listAnswerTopic|@count gt 0}
        <tbody>
        {foreach from = $listAnswerTopic item = data key=k}
          <tr>
            <td>{$data.name}</td>
            <td>{$data.default_value}</td>
            <td>{$data.assign_value}</td>
            <td>{$data.weight_value}</td>
            <td>
              <a href="{$admin_file}?task=answer_topic&amp;action=edit&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;ans_id={$smarty.get.ans_id}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
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
                      <p>{if $multiLang.text_delete_answer_topic}{$multiLang.text_delete_answer_topic}{else}No Translate(Key Lang: text_delete_answer_topic){/if} <b>({$data.name|escape}-{$data.default_value|escape}-{$data.assign_value|escape}-{$data.weight_value|escape})</b>?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=answer_topic&amp;action=delete&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;ans_id={$smarty.get.ans_id}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</i></button>
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

      <!-- <a href="{$admin_file}?task=answer&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a> -->
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
  if(url.task === 'answer') localStorage.setItem('urlAns',urlBack);
  //Get session url
  var getUrlBack = localStorage.getItem('urlAns');
  var getUrlBackTque = localStorage.getItem('urlTque');
  if(getUrlBack !== null) {
    $("#btnBack").attr("href", getUrlBack);
    $("#bCrumbAns").attr("href", getUrlBack);
  }
  if(getUrlBack !== '') $("#bCrumbTQue").attr("href", getUrlBackTque);
  //End previous url

 </script>
{/block}
