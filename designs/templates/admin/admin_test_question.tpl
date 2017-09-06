{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
{if $smarty.cookies.checkTestQues_Qtitle}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot delete "<strong>{$smarty.cookies.checkTestQues_Ttitle},{$smarty.cookies.checkTestQues_Qtitle}</strong>", because it has been used in survey. You want to permanently delete. <a href="{$admin_file}?task=test_question&amp;action=delete_permanently&amp;tid={$smarty.get.tid}&amp;kwd={$smarty.get.kwd}&amp;id={$smarty.cookies.test_que_id}&amp;next={$smarty.get.next}" class="btn btn-danger btn-xs">Yes, Delete it.</a>
  </div>
{/if}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form" role="form" action="{$admin_file}?task=test_question" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="test_question">
          <div class="col-md-3">
            <div class="form-group">
              <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_test_question}{$multiLang.button_add_test_question}{else}No Translate(Key Lang: button_add_test_question){/if}
              </button>
            </div>
          </div>
          <div class="col-md-3">&nbsp;</div>
          <div class="col-md-3">
            <div class="form-group">
              <select class="form-control select2" name="tid" style="width:100%">
                <option value="">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if} ---</option>
                {foreach from=$test item=data}
                <option value="{$data.id}" {if $smarty.get.tid eq $data.id}selected{/if}>{$data.title}</option>
                {/foreach}
              </select>
            </div>
          </div>
          <div class="col-md-3">
          <div class="input-group">
            <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="Question Title">
            <span class="input-group-btn">
              <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate(Key Lang:button_search){/if}</button>
            </span>
          </div>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getTestQByID.id} in {/if}">
          {if $getTestQByID.id}
          <form action="{$admin_file}?task=test_question&amp;action=edit&amp;tid={$smarty.get.tid}&amp;kwd={$smarty.get.kwd}&amp;id={$getTestQByID.id}&amp;next={$smarty.get.next}" method="post">
          {else}
          <form action="{$admin_file}?task=test_question&amp;action=add&amp;tid={$smarty.get.tid}&amp;kwd={$smarty.get.kwd}&amp;next={$smarty.get.next}" method="post">
          {/if}
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang:text_test){/if}:</label>
                  {if $error.test}
                    <span style="color: red">{if $multiLang.text_test_empty}{$multiLang.text_test_empty}{else}No Translate(Key Lang:text_test_empty){/if}.</span>
                  {/if}
                  <br>
                  <select class="form-control select2" name="test" style="width:100%">
                    <option value="">--- {if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang:text_test){/if} ---</option>
                    {foreach from=$test item=data}
                    <option value="{$data.id}" {if $smarty.session.test_question.test}{if $smarty.session.test_question.test eq $data.id}selected{/if}{else}{if $getTestQByID.test_id eq $data.id}selected{/if}{/if}>{$data.title}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_question}{$multiLang.text_question}{else}No Translate(Key Lang:text_question){/if}:</label>
                  {if $error.question}
                    <span style="color: red">{if $multiLang.text_question_empty}{$multiLang.text_question_empty}{else}No Translate(Key Lang:text_question_empty){/if}.</span>
                  {/if}
                  {if $error.is_exist_test_que}
                    <span style="color: red">{if $multiLang.text_question}{$multiLang.text_question}{else}No Translate(Key Lang:text_question){/if} {if $multiLang.text_is_existed}{$multiLang.text_is_existed}{else}No Translate(Key Lang: text_is_existed){/if}</span>
                  {/if}
                  <select class="form-control select2" name="question" style="width:100%" onchange="checkQuestionType(this);">
                    <option value="">--- {if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_question}{$multiLang.text_question}{else}No Translate(Key Lang:text_question){/if} ---</option>
                    {foreach from=$question item=data}
                    <option value="{$data.id}" {if $smarty.session.test_question.question}{if $smarty.session.test_question.question eq $data.id}selected{/if}{else}{if $getTestQByID.question_id eq $data.id}selected{/if}{/if}>{$data.title} (Type:{if $data.type eq 1} Text Input{elseif $data.type eq 2}Text Area{elseif $data.type eq 3}Redio{elseif $data.type eq 4}CheckBox{/if})</option>
                    {/foreach}
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_view_Order}{$multiLang.text_view_Order}{else}No Translate(Key Lang:text_view_Order){/if}:</label>
                  {if $error.view_order}
                    <span style="color: red">{if $multiLang.text_view_Order_empty}{$multiLang.text_view_Order_empty}{else}No Translate(Key Lang:text_view_Order_empty){/if}.</span>
                  {/if}
                  <input type="text" class="form-control" name="view_order" value="{if $smarty.session.test_question.view_order}{$smarty.session.test_question.view_order}{else}{$getTestQByID.view_order}{/if}" placeholder="Example: 123..." onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_required}{$multiLang.text_required}{else}No Translate(Key Lang: text_required){/if}:</label>
                  {if $error.required}
                    <span style="color: red"> {if $multiLang.text_error_check_required}{$multiLang.text_error_check_required}{else}No Translate(Key Lang: text_error_check_required){/if}.</span>
                  {/if}
                  <div class="box">
                    <label class="radio-inline">
                      <input type="radio" name="required" value="1" {if $getTestQByID.is_required}{if $getTestQByID.is_required eq 1}checked{/if}{else}{if $smarty.session.test_question.required}{if $smarty.session.test_question.required eq 1}checked{/if}{else}checked{/if}{/if}>{if $multiLang.text_yes}{$multiLang.text_yes}{else}No Translate(Key Lang: text_yes){/if}
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="required" value="0" {if $getTestQByID.is_required|@count gt 0}{if $getTestQByID.is_required eq 0}checked{/if}{else}{if $smarty.session.test_question.required eq 0 and $smarty.session.test_question.required|@count gt 0}checked{/if}{/if}>{if $multiLang.text_no}{$multiLang.text_no}{else}No Translate(Key Lang: text_no){/if}
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="title"> {if $multiLang.text_select_test_que_copy}{$multiLang.text_select_test_que_copy}{else}No Translate(Key Lang: text_select_test_que_copy){/if} </label>
                  <select class="form-control select2" name="copy_test_que" id="copy_test_que" style="width:100%" onchange="getTestQuestionAnswer(this);">
                    <option value="">--- {if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang:text_test_question){/if} ---</option>
                    {foreach from=$listTestQuestionCopy item=data}
                    <option value="{$data.id}" {if $smarty.session.test_question.copy_test_que eq $data.id}selected{/if}>
                      (Test: {$data.test_title}) (Question: {$data.q_title} "Type:{if $data.type eq 1} Text Input{elseif $data.type eq 2}Text Area{elseif $data.type eq 3}Redio{elseif $data.type eq 4}CheckBox{/if}") (Answer: {$data.count_answer})
                    </option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="title">{if $multiLang.text_answer}{$multiLang.text_answer}{else}No Translate(Key Lang: text_answer){/if}:</label>
                  <div class="box" id="dataAnswer">
                  {if $listAnswer|@count gt 0}
                    {foreach from=$listAnswer item=v}
                      <span class="badge" style="background-color: red;">{$v.view_order}</span> {$v.title},
                    {/foreach}
                  {else}
                    &nbsp;
                  {/if}
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getTestQByID.id}
                    <input type="hidden" name="test_question_id" value="{$getTestQByID.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update} {else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=test_question&amp;tid={$smarty.get.tid}&amp;kwd={$smarty.get.kwd}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel} {else}No Translate(Key Lang: button_cancel){/if}</a>
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
            <th>{if $multiLang.text_test_title}{$multiLang.text_test_title}{else}No Translate(Key Lang: text_test_title){/if}</th>
            <th>{if $multiLang.text_question_title}{$multiLang.text_question_title}{else}No Translate(Key Lang: text_question_title){/if}</th>
            <th>{if $multiLang.text_required}{$multiLang.text_required}{else}No Translate(Key Lang: text_required){/if}</th>
            <th>{if $multiLang.text_answer}{$multiLang.text_answer}{else}No Translate(Key Lang: text_answer){/if}</th>
            <th width="100">{if $multiLang.text_view_Order}{$multiLang.text_view_Order}{else}No Translate(Key Lang: text_view_Order){/if}</th>
            <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listTestQuestion|@count gt 0}
        <tbody>
        {foreach from = $listTestQuestion item = data key=k}
          <tr>
            <td><a href="{$admin_file}?task=test_question&amp;tid={$data.test_id}">{$data.test_title}</a></td>
            <td id="test_que{$data.id}">{$data.q_title} (Type:{if $data.type eq 1} Text Input{elseif $data.type eq 2}Text Area{elseif $data.type eq 3}Redio{elseif $data.type eq 4}CheckBox{/if})</td>
            <td>
            {if $data.is_required eq 0}
              <span class="label label-warning"><i class="fa fa-ban" aria-hidden="true"></i> {if $multiLang.text_no}{$multiLang.text_no}{else}No Translate(Key Lang:text_no){/if}</span>
            {else}
              <span class="label label-primary"><i class="fa fa-check-circle" aria-hidden="true"></i> {if $multiLang.text_yes}{$multiLang.text_yes}{else}No Translate(Key Lang:text_yes){/if}</span>
            {/if}
            </td>
            <td><span class="badge">{$data.count_answer}</span></td>
            <td>{$data.view_order}</td>
            <td>
              <a href="{$admin_file}?task=test_question&amp;action=edit&amp;tid={$smarty.get.tid}&amp;kwd={$smarty.get.kwd}&amp;id={$data.id}&amp;next={$smarty.get.next}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang:button_edit){/if}"><i class="fa fa-edit"></i></a>
              <!-- Trigger the modal with a button -->
              <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$data.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang:button_delete){/if}"><i class="fa fa-trash-o"></i></button>
              <!-- Modal -->
              <div class="modal fade" id="myModal_{$data.id}" role="dialog">
                <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="panel panel-primary modal-content">
                    <div class="panel-heading modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate(Key Lang:text_confirmation){/if}</h4>
                    </div>
                    <div class="modal-body">
                      <p>{if $multiLang.text_delete_test_question_confirmation}{$multiLang.text_delete_test_question_confirmation}{else}No Translate(Key Lang:text_delete_test_question_confirmation){/if}<b>({$data.q_title|escape})</b>?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=test_question&amp;action=delete&amp;tid={$data.test_id}&amp;kwd={$smarty.get.kwd}&amp;id={$data.id}&amp;next={$smarty.get.next}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</i></button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
              <a href="{$admin_file}?task=answer&amp;tid={$data.test_id}&amp;qid={$data.question_id}&amp;tqid={$data.id}&amp;next={$smarty.get.next}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_answer}{$multiLang.button_add_answer}{else}No Translate(Key Lang: button_add_answer){/if}"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <button type="button" class="btn btn-warning btn-xs" onclick="copytest_question({$data.id});" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_copy_test_question}{$multiLang.button_copy_test_question}{else}No Translate(Key Lang:button_copy_test_question){/if}">
                <i class="fa fa-clone fa-fw"></i>
              </button>
            </td>
          </tr>
        {/foreach}
          <!-- Modal -->
          <div class="modal fade" id="myModal_Copy" role="dialog">
            <div class="modal-dialog">
              <!-- Modal content-->
              <div class="panel panel-primary modal-content">
                <div class="panel-heading modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="panel-title modal-title">{if $multiLang.text_copy_test_question}{$multiLang.text_copy_test_question}{else}No Translate(Key Lang:text_copy_test_question){/if}</h4>
                </div>
                <div class="modal-body">
                  <form>
                    <small style="color:red;"><b>NOTE 1:</b> This copy function. It will copy "Test Question, Answer and Answer Topic (Default, Assign, Weight) value", If it has.</small><br>
                    <small style="color:red;"><b>NOTE 2:</b> If you select test has not result, The answer topic is blank, Because the "Answer Topic (Default, Assign, Weight) value" base topic in result. So you need go to "Test" add result first.</small>
                    <hr style="margin-top: 0px;">
                    <div class="row">
                      <div class="col-md-12">
                        <span style="color: red" id="error"></span>
                      </div>
                      <div class="col-md-12">
                        <span style="color: red">*</span><b> {if $multiLang.text_question_title}{$multiLang.text_question_title}{else}No Translate(Key Lang: text_question_title){/if}:</b> <span id="test_que_title"></span>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group" id="error_input">
                          <label for="title"><span style="color: red">*</span> {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang:text_test){/if}:</label>
                          {if $error.test}
                            <span style="color: red">{if $multiLang.text_test_empty}{$multiLang.text_test_empty}{else}No Translate(Key Lang:text_test_empty){/if}.</span>
                          {/if}
                          <br>
                          <select class="form-control select2" name="test" style="width:100%" id="test">
                            <option value="">--- {if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang:text_test){/if} ---</option>
                            {foreach from=$test item=data}
                            <option value="{$data.id}" {if $smarty.session.test_question.test}{if $smarty.session.test_question.test eq $data.id}selected{/if}{else}{if $getTestQByID.test_id eq $data.id}selected{/if}{/if}>{$data.title}</option>
                            {/foreach}
                          </select>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="title"><span style="color: red">*</span> {if $multiLang.text_required}{$multiLang.text_required}{else}No Translate(Key Lang: text_required){/if}:</label>
                          {if $error.required}
                            <span style="color: red"> {if $multiLang.text_error_check_required}{$multiLang.text_error_check_required}{else}No Translate(Key Lang: text_error_check_required){/if}.</span>
                          {/if}
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
                    </div>
                  </form>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" id="btn_save"><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate(Key Lang: button_save){/if}</i></button>
                  <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</i></button>
                </div>
              </div>
            </div>
          </div>
          <!-- Modal -->
        </tbody>
        {else}
        <tr>
          <td colspan="6"><h4 style="text-align:center">{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h4></td>
        </tr>
        {/if}
      </table>
      {if $smarty.get.tid}<a href="{$admin_file}?task=test_question" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>{/if}
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}

{block name="javascript"}
<script>

 function copytest_question(id){
   var test_que_title = $("#test_que"+id).text();

   $("#test_que_title").text(test_que_title);
   $("#btn_save").attr("onclick", "save_copytest_question("+id+")");
   $('#myModal_Copy').modal('show');
 }

 function save_copytest_question(id){
  var test_id = $("#test").val();
  if($("input[type='radio'].rdoRequired").is(':checked')) {
    var required = $("input[type='radio'].rdoRequired:checked").val();
  }
  $(".loader").show();
  if(test_id != ''){

    $.ajax({
      type: "GET",
      url: "{$admin_file}?task=copy_test_question&tqid="+id+"&test_id="+test_id+"&required="+required,
      success: function(data){
       if(data == false){
         $("#error").text("* Sorry! You can't add test question. Because duplication test question in test.");
         $(".loader").hide();
       }else {
         alert("helllo");
         location.reload();
       }
      },
      error: function(){
       //Show error here
       alert("Cannot show detail. Please try again later.");
       location.reload();
      }
    });//End Ajax
  }else {
    $("#error").text("* Please select test!");
    $("#error_input").attr("class", "form-group has-error");
    $(".loader").hide();
  }


 }

 function getTestQuestionAnswer(sel){
   var tqid = sel.value;
   $(".loader").show();
   $.ajax({
     type: "GET",
     url: "{$admin_file}?task=ajax&action=list_answer&tqid="+tqid,
     success: function(data){

       var dataHTML = "";
       if(data.length > 0)
       {
         for (var i = 0; i < data.length; i++) {
          //  dataHTML += "<label class='radio-inline'><input type='radio' name='required' value='1'>"+data[i].title+"</label>";
           dataHTML += "<span class='badge' style='background-color: red;'>"+data[i].view_order+"</span>="+data[i].title+", ";
         }
         $("#dataAnswer").html(dataHTML);
       }else {
         $("#dataAnswer").html("");
       }
       $(".loader").hide();
     },
     error: function(){
      //Show error here
      alert("Cannot show detail. Please try again later.");
     }
   });//End Ajax

 }

 function checkQuestionType(sel){
   var qid = sel.value;
   $.ajax({
     type: "GET",
     url: "{$admin_file}?task=ajax&action=check_que_type&qid="+qid,
     success: function(data){
       if(data.type == 1 || data.type == 2){
         $("#copy_test_que").attr("disabled", true);
       }else {
         $("#copy_test_que").attr("disabled", false);
       }
     },
     error: function(){
      //Show error here
      alert("Cannot show detail. Please try again later.");
     }
   });//End Ajax

 }
</script>
{/block}
