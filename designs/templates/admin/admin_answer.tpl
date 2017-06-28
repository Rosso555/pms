{extends file="admin/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTQue" href="{$admin_file}?task=test_question&amp;tid={$smarty.get.tid}">{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_answer}{$multiLang.text_answer}{else}No Translate(Key Lang: text_answer){/if}</li>
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
{if $question.type eq 1 OR $question.type eq 2}
<div class="alert alert-danger">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Sorry! you can not insert answer. Because this question with free text.
</div>
{/if}
{if $smarty.cookies.checkAnswer}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot delete "<strong>{$smarty.cookies.checkAnswer}</strong>", because it has been used in survey. You want to permanently delete. <a href="{$admin_file}?task=answer&amp;action=delete_permanently&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;id={$smarty.cookies.answer_id}" class="btn btn-danger btn-xs">Yes, Delete it.</a>
  </div>
{/if}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_answer}{$multiLang.text_answer}{else}No Translate(Key Lang: text_answer){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</b> <span id="test_title">{$test.title}</span>
    </div>
    <div class="box_title">
      <b>{if $multiLang.text_question}{$multiLang.text_question}{else}No Translate(Key Lang: text_question){/if}:</b> {$question.title} (Type:{if $question.type eq 1} Text Input{elseif $question.type eq 2}Text Area{elseif $question.type eq 3}Redio{elseif $question.type eq 4}CheckBox{/if})
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=answer" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="tqid" value="{$smarty.get.tqid}">
          <input type="hidden" name="task" value="answer">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_answer}{$multiLang.button_add_answer}{else}No Translate(Key Lang: button_add_answer){/if}
            </button>
          </div>
          <div class="form-group">
            <button type="button" class="btn btn-warning" onclick="copy_test_answer_answer_topic({$data.id});" {if $listAnswer|@count eq 0}disabled{/if}>
              <i class="fa fa-clone fa-fw"></i> {if $multiLang.button_copy_test_answer_topic}{$multiLang.button_copy_test_answer_topic}{else}No Translate(Key Lang: button_copy_test_answer_topic){/if}
            </button>
          </div>
          <div class="input-group" style="float: right;">
            <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="{if $multiLang.text_search_by}{$multiLang.text_search_by}{else}No Translate(Key Lang: text_search_by){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}...">
            <span class="input-group-btn">
              <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate(Key Lang: button_search){/if}</button>
            </span>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getAnswerByID.id} in {/if}">
          {if $getAnswerByID.id}
          <form action="{$admin_file}?task=answer&amp;action=edit&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;id={$getAnswerByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=answer&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}" method="post">
          {/if}
            <input type="hidden" name="tqid" value="{$smarty.get.tqid}">
            <input type="hidden" name="qid" value="{$smarty.get.qid|escape}">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</label>
                  {if $error.title}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</span>
                  {/if}
                  <input type="text" name="title" class="form-control" placeholder="Title"
                  value="{if $smarty.session.answer.title}{$smarty.session.answer.title}{elseif $getAnswerByID.title}{$getAnswerByID.title}{/if}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <div class="form-group">
                    <label for="title"><span style="color: red">*</span> {if $multiLang.text_view_Order}{$multiLang.text_view_Order}{else}No Translate(Key Lang: text_view_Order){/if} </label>
                    {if $error.view_order}
                      <span style="color: red">{if $multiLang.text_view_Order_empty}{$multiLang.text_view_Order_empty}{else}No Translate(Key Lang: text_view_Order_empty){/if}</span>
                    {/if}
                    <input type="text" name="view_order" class="form-control" placeholder="Example: 123..."
                    value="{if $smarty.session.answer.view_order}{$smarty.session.answer.view_order}{elseif $getAnswerByID.view_order}{$getAnswerByID.view_order}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title" style="margin-bottom: 0px;"> No Calculate:</label>
                  <div class="checkbox box" style="margin-top: 5px;">
                    <label><input type="checkbox" value="1" name="calculate" {if $getAnswerByID.calculate eq 1}checked{/if}>{if $multiLang.text_yes}{$multiLang.text_yes}{else}No Translate(Key Lang: text_yes){/if}</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getAnswerByID.id}
                    <input type="hidden" name="asid" value="{$getAnswerByID.id}" />
                    <button type="submit" class="btn btn-success" {if $question.type eq 1 OR $question.type eq 2}disabled{/if}><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=answer&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_concel){/if}</a>
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
          <th>{if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</th>
          <th>{if $multiLang.text_answer_topic_value}{$multiLang.text_answer_topic_value}{else}No Translate(Key Lang: text_answer_topic_value){/if}</th>
          <th>{if $multiLang.text_no}{$multiLang.text_no}{else}No Translate(Key Lang: text_no){/if} {if $multiLang.text_calculate}{$multiLang.text_calculate}{else}No Translate(Key Lang: text_calculate){/if}</th>
          <th>{if $multiLang.text_view_Order}{$multiLang.text_view_Order}{else}No Translate(Key Lang: text_view_Order){/if}</th>
          <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listAnswer|@count gt 0}
        <tbody>
        {foreach from = $listAnswer item = data key=k}
          <tr>
            <td>{$data.title}</td>
            <td><span class="badge">{$data.count_ans_topic}</span></td>
            <td>{if $data.calculate eq 1}<span class="label label-primary">Yes</span>{else}<span class="label label-warning">No</span>{/if}</td>
            <td>{$data.view_order}</td>
            <td>
              <a href="{$admin_file}?task=answer&amp;action=edit&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
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
                      <p>{if $multiLang.text_delete_answer}{$multiLang.text_delete_answer}{else}No Translate(Key Lang: text_delete_answer){/if} <b>({if $data.title}{$data.title|escape}{else}{$data.default_value|escape}-{$data.assign_value|escape}-{$data.weight_value|escape}{/if})</b>?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=answer&amp;action=delete&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</i></button>
                    </div>
                  </div>
                </div>
              </div>
            <!-- Modal -->
            <a href="{$admin_file}?task=answer_topic&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;ans_id={$data.id}" class="btn btn-info btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_answer_topic_default_value}{$multiLang.button_add_answer_topic_default_value}{else}No Translate(Key Lang: button_add_answer_topic_default_value){/if}"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
            {if $question.type eq 3}
              <a href="{$admin_file}?task=jump_to&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;ans_id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.text_jump_to_question}{$multiLang.text_jump_to_question}{else}No Translate(Key Lang: text_jump_to_question){/if}"><i class="fa fa-plus-square-o"></i></a>
            {/if}
            </td>
          </tr>
        {/foreach}
        </tbody>
        {else}
        <tr>
          <td colspan="5"><h4 style="text-align:center">{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h4></td>
        </tr>
        {/if}
      </table>
      <!-- Modal -->
      <div class="modal fade" id="modal_CopyTest" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="panel panel-primary modal-content">
            <div class="panel-heading modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="panel-title modal-title">{if $multiLang.text_copy_test_answer_topic}{$multiLang.text_copy_test_answer_topic}{else}No Translate(Key Lang:text_copy_test_answer_topic){/if}</h4>
            </div>
            <div class="modal-body">
              <form>
                <small style="color:red;"><b>NOTE 1:</b> This copy function. It will copy "Test, Answer and Answer Topic (Default, Assign, Weight) value" to other question.</small><br>
                <small style="color:red;"><b>NOTE 2:</b> We can't copy question of text free.</small>
                <hr style="margin-top: 0px;">
                <div class="row">
                  <div class="col-md-12">
                    <span style="color: red" id="error_existed"></span>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <span style="color: red">*</span> <b>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang:text_test){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang:text_title){/if}:</b> {$test.title}
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group" id="error_input">
                      <label for="title"><span style="color: red" id="required_que">*</span> {if $multiLang.text_question_title}{$multiLang.text_question_title}{else}No Translate(Key Lang: text_question_title){/if}:</label>

                      <span style="color: red" id="error"></span>
                      <select class="form-control select2" name="question" id="question" style="width:100%">
                        <option value="">--- {if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_question}{$multiLang.text_question}{else}No Translate(Key Lang:text_question){/if} ---</option>
                        {foreach from=$list_question item=data}
                        <option value="{$data.id}">{$data.title} (Type:{if $data.type eq 1} Text Input{elseif $data.type eq 2}Text Area{elseif $data.type eq 3}Redio{elseif $data.type eq 4}CheckBox{/if})</option>
                        {/foreach}
                      </select>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="title"><span style="color: red">*</span> {if $multiLang.text_required}{$multiLang.text_required}{else}No Translate(Key Lang: text_required){/if}:</label>
                      <div class="box">
                        <label class="radio-inline">
                          <input type="radio" class="rdoRequired" name="required" value="1" checked>{if $multiLang.text_yes}{$multiLang.text_yes}{else}No Translate(Key Lang: text_yes){/if}
                        </label>
                        <label class="radio-inline">
                          <input type="radio" class="rdoRequired" name="required" value="0">{if $multiLang.text_no}{$multiLang.text_no}{else}No Translate(Key Lang: text_no){/if}
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <label for="title" style="margin-bottom: 0px;"> {if $multiLang.text_copy_to_all_test_question}{$multiLang.text_copy_to_all_test_question}{else}No Translate(Key Lang:text_copy_to_all_test_question){/if}:</label>
                    <div class="checkbox box" style="margin-top: 5px;">
                      <label><input type="checkbox" value="1" name="copy_all" id="copy_all" onchange="checkCopyAll();">{if $multiLang.text_yes}{$multiLang.text_yes}{else}No Translate(Key Lang: text_yes){/if}</label>
                    </div>
                  </div>
                </div>
              </form>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" id="btn_save" onclick="save_test_answer_answer_topic({$smarty.get.tid}, {$smarty.get.tqid});"><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate(Key Lang: button_save){/if}</i></button>
              <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</i></button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal -->

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
if(url.task === 'test_question') localStorage.setItem('urlTque',urlBack);
//Get session url
var getUrlBack = localStorage.getItem('urlTque');
if(getUrlBack !== null){
  $("#btnBack").attr("href", getUrlBack);
  $("#bCrumbTQue").attr("href", getUrlBack);
}
//End previous url

function checkCopyAll(){
  var copy_all = document.getElementById("copy_all").checked;
  if (copy_all) {
    $("#required_que").text("");
  }else {
    $("#required_que").text("*");
  }
}
function copy_test_answer_answer_topic(){
  $("#test_title").text();
  $("#modal_CopyTest").modal("show");
}
function save_test_answer_answer_topic(tid, tqid){

  var copy_all = document.getElementById("copy_all").checked;
  var question_id = $("#question").val();
  var copy_all_value = '';

  if (copy_all) {
    $("#required_que").text("");
    copy_all_value = $("#copy_all").val();
  }

  if($("input[type='radio'].rdoRequired").is(':checked')) {
    var required = $("input[type='radio'].rdoRequired:checked").val();
  }

  $(".loader").show();
  if(question_id != '' || copy_all_value != ''){
    $("#error").text("");

    var paramdata = { tid: tid, tqid: tqid, question_id: question_id, required: required, copy_all_value: copy_all_value };

    $.ajax({
      type: "POST",
      url:"{$admin_file}?task=copy_test_answer",
      dataType:"json",
      data: paramdata,
      success: function(data){
       if(data == false){
         $("#error_existed").text("* Sorry! You can't add test question. Because duplication test question in test.");
         $(".loader").hide();
       }else {
         location.reload();
       }
      },
      error: function(){
       //Show error here
       alert("Cannot save data. Please try again later.");
       location.reload();
      }
    });//End Ajax
  }else {
    $("#error").text("Please select question!");
    $(".loader").hide();
  }

}

</script>
{/block}
