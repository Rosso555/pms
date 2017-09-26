{extends file="common/layout.tpl"}
{block name="style"}
<style>
  .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th{
    border-top: 0px solid red;
  }
  input[type=radio] {
    width: 16px;
    height: 16px;
  }
  input[type=checkbox] {
    width: 16px;
    height: 16px;
  }
</style>
{/block}
{block name="main"}
<div class="inbox-top">
  {if $getTestById.test_title}<h3 class="text-center">{$getTestById.test_title}</h3>{/if}
  <h4 class="text-center">{$getTestById.title} {if $countTestGroupSession eq $testQueGroup|@count} ({if $multiLang.text_final_step}{$multiLang.text_final_step}{else}No Translate (Key Lang: text_final_step){/if}) {/if}</h4>
  <p class="text-center">{$getTestById.description}</p>
</div>
{if $error}<div class="alert alert-warning" role="alert">{if $multiLang.text_error_check_all_required}{$multiLang.text_error_check_all_required}{else}No Translate (Key Lang: text_error_check_all_required){/if}</div>{/if}
<div class="inbox-test sansserif" style="margin-bottom: 20px;">
  <form class="form" action="{$index_file}?task=test_question&amp;tid={$smarty.get.tid}&amp;id={$smarty.get.id}" method="post">
    <input type="hidden" id="test_group_id" name="test_group_id" value="{$test_group_id|escape}">
  {if $result|@COUNT gt 0}
    {foreach item = v from=$result key=id name=first_foo}
      {if $v.flag eq 1}
      {$totalC = 100 / $v.answer|@count}
      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12"><p style="margin-top: 20px; margin-bottom: -10px;"><b> {$v.g_answer_title}</b></p></div>
      </div>
      <div class="row">
        <div class="col-md-5 col-sm-12 col-xs-12">&nbsp;</div>
        <div class="col-md-7 col-sm-12 col-xs-12">
          {foreach item=value from=$v.answer name=foo}
            <div style="width:{$totalC}%; float: left; padding-left: 7px; margin-top: 10px;"><p>{$value.title}</p></div>
          {/foreach}
        </div>
      </div>
      {if $v.group_answer}
        {foreach item=va from=$v.group_answer name=foo}
        <div class="row">
          <div class="col-md-5 col-sm-12 col-xs-12">
            <p style="margin-left: 10px; margin-top: 5px;" class="answer_responsive">
              {if $va.is_required eq 1}<span style="color:red;" id="required_{$va.id}">*</span>{/if}
              {if $va.hide_title eq 0}{$va.que_title}{else}{$va.description}{/if}
            </p>
          </div>
          <div class="col-md-7 col-sm-12 col-xs-12">
            <table class="table tbl_ans_color" style="border-radius: 5px;">
              <tr>
              <!-- check $va.answer -->
              {if $va.answer}
              {foreach item=ans from=$va.answer name=foo}
                <td>
                {$ans.jump_to}
                {if $va.type eq 3}
                  <label for="radio{$ans.id}_{$va.id}" class="radio-inline" style="margin-bottom: 10px;">
                    {if $smarty.foreach.foo.first}
                    {if $va.is_required eq 1}<input type="hidden" name="is_required[]" id="is_required{$va.id}" value="{$va.is_required|escape}"/>{/if}
                    <!-- hidden test_question_id -->
                    <input type="hidden" id="tq_id{$va.id}" name="tq_id[]" value="{$va.test_question_id|escape}" disabled>
                    <input type="hidden" id="rais_email{$va.id}" name="is_email[]" value="{if $va.is_email eq 1}1{else}0{/if}" disabled>
                    <input type="hidden" id="raanswer_id{$va.id}" name="answer_id[]" value="{$ans.id|escape}" disabled>
                    <input type="hidden" id="racontent{$va.id}" name="content[]" value="NULL" disabled>
                    {/if}
                    <input style="margin-top: -4px;" type="radio" id="radio{$ans.id}_{$va.id}" name="answer[{$va.id}]" class="check_value{$va.id}" value="{$ans.id|escape}" onclick="removeRequired({$va.id}, {$ans.id} , {$va.type}, {if $ans.jump_to}{$ans.jump_to}{else}0{/if}, {$va.view_order}, {if $ans.q_jump_to_view_order}{$ans.q_jump_to_view_order}{else}0{/if})"
                    {if $sessionAnswerIdError|@COUNT gt 0}{foreach from=$sessionAnswerIdError item=ansid}{if $ansid eq $ans.id}checked{/if}{/foreach} {else} {if $sessionAnswerId|@count gt 0}{foreach from=$sessionAnswerId item=d}{if $d eq $ans.id}checked{/if}{/foreach}{else}{if $va.is_required eq 1}required{/if}{/if}{/if}>
                  </label>

                {elseif $va.type eq 4}
                  <label for="checkbox{$ans.id}_{$va.id}" class="checkbox-inline" style="margin-bottom: 10px;">
                    {if $va.is_required eq 1}<input type="hidden" name="is_required[]" class="chk_box_is_required{$v.id}" id="is_required{$va.id}" value="{$va.is_required|escape}"/>{/if}
                    <!-- hidden test_question_id -->
                    <input type="hidden" id="chk_tqid{$va.id}{$ans.id}" name="tq_id[]" value="{$va.test_question_id|escape}" disabled>
                    <input type="hidden" id="is_email{$va.id}{$ans.id}" name="is_email[]" value="{if $va.is_email eq 1}1{else}0{/if}" disabled>
                    <input type="hidden" id="answer_id{$va.id}{$ans.id}" name="answer_id[]" value="{$ans.id|escape}" disabled>
                    <input type="hidden" id="content{$va.id}{$ans.id}" name="content[]" value="NULL" disabled>

                    <input style="margin-top: -4px;" type="checkbox" id="checkbox{$ans.id}_{$va.id}" name="answer[]" class="check_box_value{$va.id}" value="{$ans.id|escape}" onclick="removeRequired({$va.id}, {$ans.id} , {$va.type}, {if $ans.jump_to}{$ans.jump_to}{else}0{/if}, {$va.view_order}, {if $ans.q_jump_to_view_order}{$ans.q_jump_to_view_order}{else}0{/if})"
                    {if $sessionAnswerIdError|@COUNT gt 0}{foreach from=$sessionAnswerIdError item=ansid}{if $ansid eq $ans.id}checked{/if}{/foreach}{else} {if $sessionAnswerId|@count gt 0}{foreach from=$sessionAnswerId item=d}{if $d eq $ans.id}checked{/if}{/foreach}{else}{if $va.is_required eq 1}required{/if}{/if} {/if}>

                  </label>
                {/if}
                </td>
              {/foreach}
              {/if}
              <!--end check $va.answer -->
              </tr>
            </table>
          </div>
        </div>
        {/foreach}
      {/if}

      {else}
      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <p style="margin-top: 20px; {if $v.hide_title eq 1}margin-bottom: 0px;{/if}">{if $v.is_required eq 1}<span style="color:red;" id="required_{$v.id}">*</span>{/if}<b> {if $v.hide_title eq 0}{$v.que_title}{else}{$v.description}{/if}</b></p>
          {if $v.hide_title eq 0}<p style="margin-left: 16px;">{$v.description}</p>{/if}
          <!-- check answer -->
          {if $v.answer}
          <div style="margin-left: 5px;">
            <!-- fetch answer -->
            {foreach item=ans from=$v.answer name=foo}
            {$ans.jump_to}
              {if $v.type eq 3}
              <div class="radio" >
                <label for="radio{$ans.id}_{$v.id}" class="radio-inline" style="margin-bottom: 10px;">
                  {if $smarty.foreach.foo.first}
                  {if $v.is_required eq 1}<input type="hidden" name="is_required[]" id="is_required{$v.id}" value="{$v.is_required|escape}"/>{/if}
                  <!-- hidden test_question_id -->
                  <input type="hidden" id="tq_id{$v.id}" name="tq_id[]" value="{$v.test_question_id|escape}" disabled>
                  <input type="hidden" id="rais_email{$v.id}" name="is_email[]" value="{if $v.is_email eq 1}1{else}0{/if}" disabled>
                  <input type="hidden" id="raanswer_id{$v.id}" name="answer_id[]" value="{$ans.id|escape}" disabled>
                  <input type="hidden" id="racontent{$v.id}" name="content[]" value="NULL" disabled>
                  {/if}
                  <input style="margin-top: -4px;" type="radio" id="radio{$ans.id}_{$v.id}" name="answer[{$v.id}]" class="check_value{$v.id}" value="{$ans.id|escape}" onclick="removeRequired({$v.id}, {$ans.id}, {$v.type}, {if $ans.jump_to}{$ans.jump_to}{else}0{/if}, {$v.view_order}, {if $ans.q_jump_to_view_order}{$ans.q_jump_to_view_order}{else}0{/if})"
                  {if $sessionAnswerIdError|@COUNT gt 0}{foreach from=$sessionAnswerIdError item=ansid}{if $ansid eq $ans.id}checked{/if}{/foreach} {else} {if $sessionAnswerId|@count gt 0}{foreach from=$sessionAnswerId item=d}{if $d eq $ans.id}checked{/if}{/foreach}{else}{if $v.is_required eq 1}required{/if}{/if} {/if}>

                  <span style="line-height: 1.2;">{$ans.title}</span>
                </label>
              </div>
              {elseif $v.type eq 4}
              <div class="checkbox">
                <label for="checkbox{$ans.id}_{$v.id}" class="checkbox-inline" style="margin-bottom: 10px;">
                  {if $v.is_required eq 1}<input type="hidden" name="is_required[]" class="chk_box_is_required{$v.id}" id="is_required{$v.id}" value="{$v.is_required|escape}"/>{/if}
                  <!-- hidden test_question_id -->
                  <input type="hidden" id="chk_tqid{$v.id}{$ans.id}" name="tq_id[]" value="{$v.test_question_id|escape}" disabled>
                  <input type="hidden" id="is_email{$v.id}{$ans.id}" name="is_email[]" value="{if $v.is_email eq 1}1{else}0{/if}" disabled>
                  <input type="hidden" id="answer_id{$v.id}{$ans.id}" name="answer_id[]" value="{$ans.id|escape}" disabled>
                  <input type="hidden" id="content{$v.id}{$ans.id}" name="content[]" value="NULL" disabled>

                  <input style="margin-top: -4px;" type="checkbox" id="checkbox{$ans.id}_{$v.id}" name="answer[]" class="check_box_value{$v.id}" value="{$ans.id|escape}" onclick="removeRequired({$v.id}, {$ans.id}, {$v.type}, {if $ans.jump_to}{$ans.jump_to}{else}0{/if}, {$v.view_order}, {if $ans.q_jump_to_view_order}{$ans.q_jump_to_view_order}{else}0{/if})"
                  {if $sessionAnswerIdError|@COUNT gt 0}{foreach from=$sessionAnswerIdError item=ansid}{if $ansid eq $ans.id}checked{/if}{/foreach}{else} {if $sessionAnswerId|@count gt 0}{foreach from=$sessionAnswerId item=d}{if $d eq $ans.id}checked{/if}{/foreach}{else}{if $v.is_required eq 1}required{/if}{/if} {/if}>

                  <span style="line-height: 1.2;">{$ans.title}</span>
                </label>
              </div>
              {/if}

            {/foreach}
          </div>
          {/if}
          <!-- end check answer -->
          <!-- text free input -->
          {if $v.type eq 1}
          <p style="margin-left: 16px; margin-top: 15px;">
            {if $v.is_required eq 1}<input type="hidden" name="is_required[]" id="text_is_required_{$v.id}" value="{$v.is_required|escape}"/>{/if}
            <!-- hidden test_question_id -->
            <input type="hidden" id="tq_id_{$v.id}" name="tq_id[]" value="{$v.test_question_id|escape}" disabled>
            <input type="hidden" id="is_email_{$v.id}" name="is_email[]" value="{if $v.is_email eq 1}1{else}0{/if}" disabled>
            <input type="hidden" id="answer_id_{$v.id}" name="answer_id[]" value="NULL" disabled>
            <input type="hidden" id="content_{$v.id}" name="content[]" value="{foreach from=$sessionContent item=va}{if $va.id eq $ans.id}{$va.content}{/if}{/foreach}" disabled>

            <!-- <input type="hidden" id="answer{$v.id}" name="answer[]" {if $v.is_required eq 1}required{/if} value="NULL" disabled> -->
            <input {if $v.is_email eq 1}type="email"{else}type="text"{/if} class="form-control check_value{$v.id}" onchange="checkvalue(event, this, {$v.id});" id="test{$v.id}" {if $v.is_required eq 1}required{/if} value="{if $contentError}{foreach from=$contentError item=vc}{if $vc.tqid eq $v.id}{$vc.content}{/if}{/foreach}{else}{foreach from=$sessionContent item=va}{if $va.id eq $ans.id}{$va.content}{/if}{/foreach}{/if}">
          </p>
          {elseif $v.type eq 2}
          <p style="margin-left: 16px; margin-top: 15px;">
            {if $v.is_required eq 1}<input type="hidden" name="is_required[]" id="text_is_required_{$v.id}" value="{$v.is_required|escape}"/>{/if}
            <!-- hidden test_question_id -->
            <input type="hidden" id="tq_id_{$v.id}" name="tq_id[]" value="{$v.test_question_id|escape}" disabled>
            <input type="hidden" id="is_email_{$v.id}" name="is_email[]" value="{if $v.is_email eq 1}1{else}0{/if}" disabled>
            <input type="hidden" id="answer_id_{$v.id}" name="answer_id[]" value="NULL" disabled>
            <input type="hidden" id="content_{$v.id}" name="content[]" value="{foreach from=$sessionContent item=va}{if $va.id eq $ans.id}{$va.content}{/if}{/foreach}" disabled>

            <!-- <input type="hidden" id="answer{$v.id}" name="answer[]" {if $v.is_required eq 1}required{/if} value="NULL" disabled> -->
            <textarea class="form-control check_value{$v.id}" onkeyup="checkvalue(event, this, {$v.id});" id="test{$v.id}" {if $v.is_required eq 1}required{/if} rows="3">{if $contentError}{foreach from=$contentError item=vc}{if $vc.tqid eq $v.id}{$vc.content}{/if}{/foreach}{else}{foreach from=$sessionContent item=va}{if $va.id eq $ans.id}{$va.content}{/if}{/foreach}{/if}</textarea>
          </p><!-- end text free input -->
          {/if}
        </div>

      </div>
      {/if}
    {/foreach}
  {else}
    <h3 class="text-center">{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h3>
  {/if}
      <hr>
      <div class="form-group text-center">
        {if $testQueGroup|@count gt 1}
          {if $countTestGroupSession neq 1}
          <a href="{$index_file}?task=back_step&amp;id={$smarty.get.id}" class="btn btn-warning"> <i class="fa fa-step-backward" aria-hidden="true"></i> {if $multiLang.button_back_step}{$multiLang.button_back_step}{else}No Translate (Key Lang: button_back_step){/if}</a>
          {/if}
          {if $countTestGroupSession eq $testQueGroup|@count}
          <button type="submit" class="btn btn-success"> {if $multiLang.button_show_my_score}{$multiLang.button_show_my_score}{else}No Translate (Key Lang: button_show_my_score){/if} </button>
          {else}
          <button type="submit" class="btn btn-success"> {if $multiLang.button_next_step}{$multiLang.button_next_step}{else}No Translate (Key Lang: button_next_step){/if} <i class="fa fa-step-forward" aria-hidden="true"></i></button>
          {/if}
        {else}
        <button type="submit" class="btn btn-success"> {if $multiLang.button_show_my_score}{$multiLang.button_show_my_score}{else}No Translate (Key Lang: button_show_my_score){/if}</button>
        {/if}
        <button type="button" class="btn btn-warning" onclick="save_draft();"> {if $multiLang.button_save_draft}{$multiLang.button_save_draft}{else}No Translate (Key Lang: button_save_draft){/if}</button>
      </div>
  </form>
</div>

<!-- Modal -->
<div class="modal fade" id="model_leave" role="dialog">
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

{/block}
{block name="javascript"}
<script>
var jump_to_value = {$ResultJumpTo};

// {foreach from=$ResultJumpTo.12 item=v}
//   jump_to_value.push({ chk_answer: check_bok_value[i].value });
// {/foreach}

$(document).ready( function()
{
  //Run function when browser resizes
  $(window).resize( resizeBrowser );

  function resizeBrowser(){
    var width = window.innerWidth;
    var height = window.innerHeight;

    if(width < 992){
      $('.tbl_ans_color').attr('style', 'margin-bottom: 20px; background-color: #eee; border-radius: 5px;');
      $('.answer_responsive').attr('style', 'margin-left: 10px; margin-top: 5px; margin-bottom: 4px;');
    }else {
      $('.tbl_ans_color').attr('style', 'margin-bottom: 5px; border-radius: 5px;');
      $('.answer_responsive').attr('style', 'margin-left: 10px; margin-top: 5px;');

    }
  }
  //Run function after finished loading
  resizeBrowser();

});

function save_draft()
{
  $(".loader").show();
  var tque_id = document.getElementsByName('tq_id[]');
  var answer  = document.getElementsByName('answer_id[]');
  var content = document.getElementsByName('content[]');
  var test_id = {$smarty.get.tid};
  var test_pat_id = {$smarty.get.id};
  var test_que_data = [];

  for (var i=0; i<tque_id.length; i++) {
    if(tque_id[i].disabled == false || answer[i].disabled == false || content[i].disabled == false) {
      test_que_data.push({ test_que_id: tque_id[i].value, answer_id: answer[i].value, content: content[i].value });
    }
  }

  var paramdata = { test_id : test_id,
                    test_pat_id : test_pat_id,
                    test_que_data: JSON.stringify(test_que_data) };

  if(test_que_data.length > 0)
  {
    $.ajax({
      type: "POST",
      url: "{$patient_file}?task=test_save_draft",
      dataType:'json',
      data: paramdata,
      success: function(data){
        var dataHTML = "";
        if(data == true)
        {
          //hide Loading gif
          $(".loader").hide();
        }
      },
      error: function(){
        //Show error here
        alert("Cannot save data. Please try again later.");
        // location.reload();
      }
    });//End Ajax
  } else {
    alert("You don't check answer.");
    $(".loader").hide();
  }

}
//Event type on text input & textarea
function checkvalue(e, field, id)
{
    var text = $('#test'+id).val();

    if(text !== '')
    {
      $('#tq_id_'+id).removeAttr('disabled');
      $('#text_is_required_'+id).val(0);
      $('#answer_id_'+id).removeAttr('disabled');
      $('#content_'+id).removeAttr('disabled');
      $('#is_email_'+id).removeAttr('disabled');
      $('#test'+id).removeAttr('required');
    }

    if(text == '')
    {
      $('#tq_id_'+id).attr('disabled','disabled');
      $('#text_is_required_'+id).val(1);
      $('#answer_id_'+id).attr('disabled', 'disabled');
      $('#content_'+id).attr('disabled', 'disabled');
      $('#is_email_'+id).attr('disabled', 'disabled');
      $('#test'+id).attr('required', true);
    }
    $('#content_'+id).val(text);
}

//Remove Requeired and check box, radio & jumping to
function removeRequired(id, ansid, type, jump_to, view_order, q_jumpto_view_order)
{
  // for check or uncheck
  var chk_box = document.getElementById('checkbox'+ansid+'_'+id);

  if(type == 4)
  {
    if(chk_box.checked == true)
    {
      //Action button check box
      $('.chk_box_is_required'+id).val(0);
      $('#chk_tqid'+id+ansid).removeAttr('disabled');
      $('#is_email'+id+ansid).removeAttr('disabled');
      $('#answer_id'+id+ansid).removeAttr('disabled');
      $('#content'+id+ansid).removeAttr('disabled');

      $('#checkbox'+ansid+'_'+id).removeAttr('required');
      $('.check_box_value'+id).removeAttr('required');
    } else {
      //Action button uncheck
      $('#chk_tqid'+id+ansid).attr('disabled', true);
      $('#is_email'+id+ansid).attr('disabled', true);
      $('#answer_id'+id+ansid).attr('disabled', true);
      $('#content'+id+ansid).attr('disabled', true);
    }
  }

  if(type == 3)
  {
    $('#is_required'+id).val(0);
    $('#tq_id'+id).removeAttr("disabled");
    $('#rais_email'+id).removeAttr('disabled');
    $('#raanswer_id'+id).removeAttr('disabled');
    $('#racontent'+id).removeAttr('disabled');
    $('#radio'+ansid+'_'+id).removeAttr("required");
    $('#raanswer_id'+id).val(ansid);
  }

  var check_bok_value = document.getElementsByClassName('check_box_value'+id);
  var chk_value = [];

  for (var i=0; i<check_bok_value.length; i++) {
    //Get checked value
    if (check_bok_value[i].checked == true) {
      chk_value.push({ chk_answer: check_bok_value[i].value });
    }
  }
  //Add Requeired = 1 & required is true, if user uncheck
  if(chk_value.length == 0)
  {
    $('.chk_box_is_required'+id).val(1);
    $('.check_box_value'+id).attr('required', true);
  }

  if(jump_to > 0)
  {
    console.log(jump_to_value[jump_to].length);

    for (var i = 0; i < jump_to_value[jump_to].length; i++) {
      alert(jump_to_value[jump_to][i]);
    }
  }

}

window.onload = function(e)
{
  //Save draft: if has do it
  {if $testTmpQuestion|@COUNT gt 0}
    {foreach from=$testTmpQuestion item=v}

      {if $v.type == 1 || $v.type == 2}
        $('#text_is_required_{$v.tqid}').val(0);
        $('#tq_id_{$v.tqid}').removeAttr('disabled');
        $('#is_email_{$v.tqid}').removeAttr('disabled');
        $('#answer_id_{$v.tqid}').removeAttr('disabled');
        $('#content_{$v.tqid}').removeAttr('disabled');
        $('#content_{$v.tqid}').val('{$v.content}');
        $('#test{$v.tqid}').removeAttr('required');
        $('#test{$v.tqid}').val('{$v.content}');
      {/if}

      {if $v.type == 3}
        $('#is_required{$v.tqid}').val(0);
        $('#tq_id{$v.tqid}').removeAttr("disabled");
        $('#rais_email{$v.tqid}').removeAttr('disabled');
        $('#raanswer_id{$v.tqid}').removeAttr('disabled');
        $('#racontent{$v.tqid}').removeAttr('disabled');
        $('#radio{$v.answer_id}_{$v.tqid}').removeAttr("required");
        $('#radio{$v.answer_id}_{$v.tqid}').attr('checked', true);
      {/if}

      {if $v.type == 4}
        //Action button check box
        $('.chk_box_is_required{$v.tqid}').val(0);
        $('#chk_tqid{$v.tqid}{$v.answer_id}').removeAttr("disabled");
        $('#is_email{$v.tqid}{$v.answer_id}').removeAttr('disabled');
        $('#answer_id{$v.tqid}{$v.answer_id}').removeAttr('disabled');
        $('#content{$v.tqid}{$v.answer_id}').removeAttr('disabled');

        $("#checkbox{$v.answer_id}_{$v.tqid}").removeAttr("required");
        $("#checkbox{$v.answer_id}_{$v.tqid}").attr('checked', true);
        $('.check_box_value{$v.tqid}').removeAttr('required');
      {/if}

    {/foreach}
  {/if}

}

//Remove Requeired: When check box has one or two check
function check_value()
{

}


</script>
{/block}
