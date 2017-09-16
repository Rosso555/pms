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
<div class="col-md-12">
  <div class="inbox-top">
    {if $getTestById.test_title}<h3 class="text-center">{$getTestById.test_title}</h3>{/if}
    <h4 class="text-center">{$getTestById.title} {if $countTestGroupSession eq $testQueGroup|@count} ({if $multiLang.text_final_step}{$multiLang.text_final_step}{else}No Translate (Key Lang: text_final_step){/if}) {/if}</h4>
    <p class="text-center">{$getTestById.description}</p>
  </div>
  {if $error}<div class="alert alert-warning" role="alert">{if $multiLang.text_error_check_all_required}{$multiLang.text_error_check_all_required}{else}No Translate (Key Lang: text_error_check_all_required){/if}</div>{/if}
  <div class="inbox-test sansserif" style="margin-bottom: 20px;">
    <form class="form" action="{$index_file}?task=test_question&amp;id={$smarty.get.id}" method="post">
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
                      <input style="margin-top: -4px;" type="radio" id="radio{$ans.id}_{$va.id}" name="answer[{$va.id}]" class="check_value{$va.id}" value="{$ans.id|escape}" onclick="removeRequired({$va.id}, {$ans.id} , {$va.type}, {$ans.jump_to}, {$va.view_order}, {if $ans.q_jump_to_view_order}{$ans.q_jump_to_view_order}{else}0{/if})"
                      {if $sessionAnswerIdError|@COUNT gt 0}{foreach from=$sessionAnswerIdError item=ansid}{if $ansid eq $ans.id}checked{/if}{/foreach} {else} {if $sessionAnswerId|@count gt 0}{foreach from=$sessionAnswerId item=d}{if $d eq $ans.id}checked{/if}{/foreach}{else}{if $va.is_required eq 1}required{/if}{/if}{/if}>

                    </label>

                  {elseif $va.type eq 4}
                    <label for="checkbox{$ans.id}_{$va.id}" class="checkbox-inline" style="margin-bottom: 10px;">
                      {if $va.is_required eq 1}<input type="hidden" name="is_required[]" id="is_required{$ans.id}_{$va.id}" value="{$va.is_required|escape}"/>{/if}
                      <input type="hidden" id="flag{$va.id}{$ans.id}" value="0">
                      <!-- hidden test_question_id -->
                      <input type="hidden" id="chk_tqid{$va.id}{$ans.id}" name="tq_id[]" value="{$va.test_question_id|escape}" disabled>

                      <input type="hidden" id="is_email{$va.id}{$ans.id}" name="is_email[]" value="{if $va.is_email eq 1}1{else}0{/if}" disabled>
                      <input type="hidden" id="answer_id{$va.id}{$ans.id}" name="answer_id[]" value="{$ans.id|escape}" disabled>
                      <input type="hidden" id="content{$va.id}{$ans.id}" name="content[]" value="NULL" disabled>

                      <input style="margin-top: -4px;" type="checkbox" id="checkbox{$ans.id}_{$va.id}" name="answer[]" class="check_value{$va.id}" value="{$ans.id|escape}" onclick="removeRequired({$va.id}, {$ans.id} , {$va.type}, {$ans.jump_to}, {$va.view_order}, {if $ans.q_jump_to_view_order}{$ans.q_jump_to_view_order}{else}0{/if})"
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
                    <input style="margin-top: -4px;" type="radio" id="radio{$ans.id}_{$v.id}" name="answer[{$v.id}]" class="check_value{$v.id}" value="{$ans.id|escape}" onclick="removeRequired({$v.id}, {$ans.id}, {$v.type}, {$ans.jump_to}, {$v.view_order}, {if $ans.q_jump_to_view_order}{$ans.q_jump_to_view_order}{else}0{/if})"
                    {if $sessionAnswerIdError|@COUNT gt 0}{foreach from=$sessionAnswerIdError item=ansid}{if $ansid eq $ans.id}checked{/if}{/foreach} {else} {if $sessionAnswerId|@count gt 0}{foreach from=$sessionAnswerId item=d}{if $d eq $ans.id}checked{/if}{/foreach}{else}{if $v.is_required eq 1}required{/if}{/if} {/if}>

                    <span style="line-height: 1.2;">{$ans.title}</span>
                  </label>
                </div>
                {elseif $v.type eq 4}
                <div class="checkbox">
                  <label for="checkbox{$ans.id}_{$v.id}" class="checkbox-inline" style="margin-bottom: 10px;">
                    {if $v.is_required eq 1}<input type="hidden" name="is_required[]" id="is_required{$ans.id}_{$v.id}" value="{$v.is_required|escape}"/>{/if}
                    <input type="hidden" id="flag{$v.id}{$ans.id}" value="0">
                    <!-- hidden test_question_id -->
                    <input type="hidden" id="chk_tqid{$v.id}{$ans.id}" name="tq_id[]" value="{$v.test_question_id|escape}" disabled>

                    <input type="hidden" id="is_email{$v.id}{$ans.id}" name="is_email[]" value="{if $v.is_email eq 1}1{else}0{/if}" disabled>
                    <input type="hidden" id="answer_id{$v.id}{$ans.id}" name="answer_id[]" value="{$ans.id|escape}" disabled>
                    <input type="hidden" id="content{$v.id}{$ans.id}" name="content[]" value="NULL" disabled>

                    <input style="margin-top: -4px;" type="checkbox" id="checkbox{$ans.id}_{$v.id}" name="answer[]" class="check_value{$v.id}" value="{$ans.id|escape}" onclick="removeRequired({$v.id}, {$ans.id}, {$v.type}, {$ans.jump_to}, {$v.view_order}, {if $ans.q_jump_to_view_order}{$ans.q_jump_to_view_order}{else}0{/if})"
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
</div>
{/block}
{block name="javascript"}
<script>
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
  }
}

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

//Remove Requeired and check jumping to
function removeRequired(id, ansid, type, jump_to, view_order, q_jumpto_view_order)
{

  var flag = $("#flag"+id+ansid).val();

  if(jump_to != 0){
    //Set session
    sessionStorage.setItem('jump_to'+id, jump_to);
    sessionStorage.setItem('view_order'+id, view_order);
    sessionStorage.setItem('q_jumpto_view_order'+id, q_jumpto_view_order);
  }
  {if $test_group_id}
    //Set session
    sessionStorage.setItem('jump_to_group{$test_group_id}', jump_to);
    sessionStorage.setItem('view_order_group{$test_group_id}', view_order);
    sessionStorage.setItem('q_jumpto_view_order_group{$test_group_id}', q_jumpto_view_order);
  {/if}

  var check_value = document.getElementsByClassName('check_value'+id);
  {foreach item = v from= $result key=id}
    {if $v.flag eq 1}

      {foreach item=g_v from= $v.group_answer key=id}
        {if $g_v.id neq ''}
          var check_value{$g_v.id} = [];
          for (var i=0; i<check_value.length; i++) {
          //Get checked box
            if (check_value[i].checked) {
              if(id == {$g_v.id}){
                check_value{$g_v.id}.push({ answer_id: check_value[i].value });
              }
            }
          }
        {/if}
      {/foreach}

    {else}

      var check_value{$v.id} = [];
      for (var i=0; i<check_value.length; i++) {
      //Get checked box
        if (check_value[i].checked) {
          if(id == {$v.id}){
            check_value{$v.id}.push({ answer_id: check_value[i].value });
          }
        }
      }

    {/if}

  {/foreach}
  //Get session jumping to
  var session_jump_to = sessionStorage.getItem('jump_to'+id);
  var session_view_order = sessionStorage.getItem('view_order'+id);
  var session_q_jumpto_view_order = sessionStorage.getItem('q_jumpto_view_order'+id);

  //fetch question
  {foreach item = v from= $result key=id}
    {if $v.flag eq 1}

      {if $v.group_answer|COUNT gt 0}
        {foreach from=$v.group_answer item=g_v}
            // fetch answer
            {foreach item=g_value from=$g_v.answer}
              //Check Add disabled jumping to
              if({$g_v.view_order} > view_order && {$g_v.view_order} <= q_jumpto_view_order && q_jumpto_view_order != 0){
                if(jump_to != {$g_v.id} && jump_to != 0){
                  $("#required_{$g_v.id}").hide();

                  {if $g_v.type == 1 || $g_v.type == 2}
                    $('#is_required{$g_value.id}_{$g_v.id}').val(0);
                    $('#tq_id{$g_v.id}').attr("disabled", "true");
                    $("#test{$g_v.id}").attr("disabled", "true");
                  {/if}

                  {if $g_v.type == 3}
                    $('#is_required{$g_v.id}').val(0);
                    $('#tq_id{$g_v.id}').attr("disabled", "true");
                    $("#radio{$g_value.id}_{$g_v.id}").attr("disabled", "true");
                  {/if}

                  {if $g_v.type == 4}
                    $('#is_required{$g_value.id}_{$g_v.id}').val(0);
                    $('#chk_tqid{$g_v.id}{$g_value.id}').attr("disabled", "true");
                    $("#checkbox{$g_value.id}_{$g_v.id}").attr("disabled", "true");
                  {/if}

                }else {
                  $("#required_{$g_v.id}").show();

                  {if $g_v.type == 1 || $g_v.type == 2}
                    $('#is_required{$g_value.id}_{$g_v.id}').val(1);
                    $('#tq_id{$g_v.id}').removeAttr("disabled");
                    $("#test{$g_v.id}").removeAttr("disabled");
                  {/if}

                  {if $g_v.type == 3}
                    $('#is_required{$g_v.id}').val(1);
                    $('#tq_id{$g_v.id}').removeAttr("disabled");
                    $("#radio{$g_value.id}_{$g_v.id}").removeAttr("disabled");
                  {/if}

                  {if $g_v.type == 4}
                    $('#is_required{$g_value.id}_{$g_v.id}').val(1);
                    $('#chk_tqid{$g_v.id}{$g_value.id}').removeAttr("disabled");
                    $("#checkbox{$g_value.id}_{$g_v.id}").removeAttr("disabled");
                  {/if}
                }
              }
              //Clear all input disabled
              if({$g_v.view_order} > view_order && {$g_v.view_order} <= session_q_jumpto_view_order && q_jumpto_view_order == 0 && jump_to == 0){
                //Get session jumping to
                var session_jump_to1 = sessionStorage.getItem('jump_to{$g_v.id}');
                var session_view_order1 = sessionStorage.getItem('view_order{$g_v.id}');
                var session_q_jumpto_view_order1 = sessionStorage.getItem('q_jumpto_view_order{$g_v.id}');

                if(session_jump_to1 != {$g_v.id} && session_jump_to1 != null){
                  //Set session
                  sessionStorage.setItem('sess_jump_to_view_order', session_q_jumpto_view_order1);
                  sessionStorage.setItem('sess_view_order', session_view_order1);
                  $("#required_{$g_v.id}").show();

                  {if $g_v.type == 1 || $g_v.type == 2}
                    $('#is_required{$g_value.id}_{$g_v.id}').val(1);
                    $('#tq_id{$g_v.id}').removeAttr("disabled");
                    $("#test{$g_v.id}").removeAttr("disabled");
                  {/if}

                  {if $g_v.type == 3}
                    $('#is_required{$g_v.id}').val(1);
                    $('#tq_id{$g_v.id}').removeAttr("disabled");
                    $("#radio{$g_value.id}_{$g_v.id}").removeAttr("disabled");
                  {/if}

                  {if $g_v.type == 4}
                    $('#is_required{$g_value.id}_{$g_v.id}').val(1);
                    $('#chk_tqid{$g_v.id}{$g_value.id}').removeAttr("disabled");
                    $("#checkbox{$g_value.id}_{$g_v.id}").removeAttr("disabled");
                  {/if}

                }else {
                  // Get session jumping to
                  var sess_jump_to_view_order = sessionStorage.getItem('sess_jump_to_view_order');
                  var sess_view_order = sessionStorage.getItem('sess_view_order');

                  if({$g_v.view_order} > sess_view_order && {$g_v.view_order} <= sess_jump_to_view_order && sess_jump_to_view_order != null){
                    $("#required_{$g_v.id}").hide();

                    {if $g_v.type == 1 || $g_v.type == 2}
                      $('#is_required{$g_value.id}_{$g_v.id}').val(0);
                      $('#tq_id{$g_v.id}').attr("disabled", "true");
                      $("#test{$g_v.id}").attr("disabled", "true");
                    {/if}

                    {if $g_v.type == 3}
                      $('#is_required{$g_v.id}').val(0);
                      $('#tq_id{$g_v.id}').attr("disabled", "true");
                      $("#radio{$g_value.id}_{$g_v.id}").attr("disabled", "true");
                    {/if}

                    {if $g_v.type == 4}
                      $('#is_required{$g_value.id}_{$g_v.id}').val(0);
                      $('#chk_tqid{$g_v.id}{$g_value.id}').attr("disabled", "true");
                      $("#checkbox{$g_value.id}_{$g_v.id}").attr("disabled", "true");
                    {/if}

                  }else {
                    $("#required_{$g_v.id}").show();

                    {if $g_v.type == 1 || $g_v.type == 2}
                      $('#is_required{$g_value.id}_{$g_v.id}').val(1);
                      $('#tq_id{$g_v.id}').removeAttr("disabled");
                      $("#test{$g_v.id}").removeAttr("disabled");
                    {/if}

                    {if $g_v.type == 3}
                      $('#is_required{$g_v.id}').val(1);
                      $('#tq_id{$g_v.id}').removeAttr("disabled");
                      $("#radio{$g_value.id}_{$g_v.id}").removeAttr("disabled");
                    {/if}

                    {if $g_v.type == 4}
                      $("#flag"+id+ansid).val(0);
                      $('#is_required{$g_value.id}_{$g_v.id}').val(1);
                      $('#chk_tqid{$g_v.id}{$g_value.id}').removeAttr("disabled");
                      $("#checkbox{$g_value.id}_{$g_v.id}").removeAttr("disabled");
                    {/if}
                  }
                }
              }
              //Remove Required
              {if $g_v.type == 4}
                if(check_value{$g_v.id}.length == 0){
                  $("#flag"+id+"{$g_value.id}").val(0);
                  $('#is_required{$g_value.id}_'+id).val(1);
                  $("#checkbox{$g_value.id}_"+id).attr("required", "true");

                  //Action button check box
                  $('#answer_id'+id+'{$g_value.id}').attr('disabled', 'true');
                  $('#content'+id+'{$g_value.id}').attr('disabled', 'true');
                  $('#is_email'+id+'{$g_value.id}').attr('disabled', 'true');
                  $('#chk_tqid'+id+'{$g_value.id}').attr('disabled', 'true');
                }else {
                  if(flag == 0){
                    $("#flag"+id+ansid).val(1);
                    //Action button check box
                    $('#answer_id'+id+ansid).removeAttr('disabled');
                    $('#content'+id+ansid).removeAttr('disabled');
                    $('#is_email'+id+ansid).removeAttr('disabled');
                    $('#chk_tqid'+id+ansid).removeAttr('disabled');
                  }else {
                    $("#flag"+id+ansid).val(0);
                    //Action button check box
                    $('#answer_id'+id+ansid).attr('disabled', 'true');
                    $('#content'+id+ansid).attr('disabled', 'true');
                    $('#is_email'+id+ansid).attr('disabled', 'true');
                    $('#chk_tqid'+id+ansid).attr('disabled', 'true');
                  }
                  $("#is_required{$g_value.id}_"+id).val(0);
                  $("#checkbox{$g_value.id}_"+id).removeAttr("required");
                }
              {/if}

              {if $g_v.type == 3}
                $('#is_required'+id).val(0);
                // $('#tq_id{$v.id}').removeAttr("disabled");
                $("#radio{$g_value.id}_"+id).removeAttr("required");
              {/if}

            {/foreach} //end fetch answer
        {/foreach}
      {/if}

    {else}
      //fetch answer
      {foreach item=value from=$v.answer}
        //Check Add disabled jumping to
        if({$v.view_order} > view_order && {$v.view_order} <= q_jumpto_view_order && q_jumpto_view_order != 0){
          if(jump_to != {$v.id} && jump_to != 0){
            $("#required_{$v.id}").hide();

            {if $v.type == 1 || $v.type == 2}
              $('#is_required{$value.id}_{$v.id}').val(0);
              $('#tq_id{$v.id}').attr("disabled", "true");
              $("#test{$v.id}").attr("disabled", "true");
            {/if}

            {if $v.type == 3}
              $('#is_required{$v.id}').val(0);
              $('#tq_id{$v.id}').attr("disabled", "true");
              $("#radio{$value.id}_{$v.id}").attr("disabled", "true");
            {/if}

            {if $v.type == 4}
              $('#is_required{$value.id}_{$v.id}').val(0);
              $('#chk_tqid{$v.id}{$value.id}').attr("disabled", "true");
              $("#checkbox{$value.id}_{$v.id}").attr("disabled", "true");
            {/if}

          }else {
            $("#required_{$v.id}").show();

            {if $v.type == 1 || $v.type == 2}
              $('#is_required{$value.id}_{$v.id}').val(1);
              $('#tq_id{$v.id}').removeAttr("disabled");
              $("#test{$v.id}").removeAttr("disabled");
            {/if}

            {if $v.type == 3}
              $('#is_required{$v.id}').val(1);
              $('#tq_id{$v.id}').removeAttr("disabled");
              $("#radio{$value.id}_{$v.id}").removeAttr("disabled");
            {/if}

            {if $v.type == 4}
              $('#is_required{$value.id}_{$v.id}').val(1);
              $('#chk_tqid{$v.id}{$value.id}').removeAttr("disabled");
              $("#checkbox{$value.id}_{$v.id}").removeAttr("disabled");
            {/if}
          }
        }
        //Clear all input disabled
        if({$v.view_order} > view_order && {$v.view_order} <= session_q_jumpto_view_order && q_jumpto_view_order == 0 && jump_to == 0){
          //Get session jumping to
          var session_jump_to1 = sessionStorage.getItem('jump_to{$v.id}');
          var session_view_order1 = sessionStorage.getItem('view_order{$v.id}');
          var session_q_jumpto_view_order1 = sessionStorage.getItem('q_jumpto_view_order{$v.id}');

          if(session_jump_to1 != {$v.id} && session_jump_to1 != null){
            //Set session
            sessionStorage.setItem('sess_jump_to_view_order', session_q_jumpto_view_order1);
            sessionStorage.setItem('sess_view_order', session_view_order1);
            $("#required_{$v.id}").show();

            {if $v.type == 1 || $v.type == 2}
              $('#is_required{$value.id}_{$v.id}').val(1);
              $('#tq_id{$v.id}').removeAttr("disabled");
              $("#test{$v.id}").removeAttr("disabled");
            {/if}

            {if $v.type == 3}
              $('#is_required{$v.id}').val(1);
              $('#tq_id{$v.id}').removeAttr("disabled");
              $("#radio{$value.id}_{$v.id}").removeAttr("disabled");
            {/if}

            {if $v.type == 4}
              $('#is_required{$value.id}_{$v.id}').val(1);
              $('#chk_tqid{$v.id}{$value.id}').removeAttr("disabled");
              $("#checkbox{$value.id}_{$v.id}").removeAttr("disabled");
            {/if}

          }else {
            // Get session jumping to
            var sess_jump_to_view_order = sessionStorage.getItem('sess_jump_to_view_order');
            var sess_view_order = sessionStorage.getItem('sess_view_order');

            if({$v.view_order} > sess_view_order && {$v.view_order} <= sess_jump_to_view_order && sess_jump_to_view_order != null){
              $("#required_{$v.id}").hide();

              {if $v.type == 1 || $v.type == 2}
                $('#is_required{$value.id}_{$v.id}').val(0);
                $('#tq_id{$v.id}').attr("disabled", "true");
                $("#test{$v.id}").attr("disabled", "true");
              {/if}

              {if $v.type == 3}
                $('#is_required{$v.id}').val(0);
                $('#tq_id{$v.id}').attr("disabled", "true");
                $("#radio{$value.id}_{$v.id}").attr("disabled", "true");
              {/if}

              {if $v.type == 4}
                $('#is_required{$value.id}_{$v.id}').val(0);
                $('#chk_tqid{$v.id}{$value.id}').attr("disabled", "true");
                $("#checkbox{$value.id}_{$v.id}").attr("disabled", "true");
              {/if}

            }else {
              $("#required_{$v.id}").show();

              {if $v.type == 1 || $v.type == 2}
                $('#is_required{$value.id}_{$v.id}').val(1);
                $('#tq_id{$v.id}').removeAttr("disabled");
                $("#test{$v.id}").removeAttr("disabled");
              {/if}

              {if $v.type == 3}
                $('#is_required{$v.id}').val(1);
                $('#tq_id{$v.id}').removeAttr("disabled");
                $("#radio{$value.id}_{$v.id}").removeAttr("disabled");
              {/if}

              {if $v.type == 4}
                $("#flag"+id+ansid).val(0);
                $('#is_required{$value.id}_{$v.id}').val(1);
                $('#chk_tqid{$v.id}{$value.id}').removeAttr("disabled");
                $("#checkbox{$value.id}_{$v.id}").removeAttr("disabled");
              {/if}
            }
          }
        }
        //Remove Required
        {if $v.type == 4}
          if(check_value{$v.id}.length == 0){
            $("#flag"+id+"{$value.id}").val(0);
            $('#is_required{$value.id}_'+id).val(1);
            $("#checkbox{$value.id}_"+id).attr("required", "true");

            //Action button check box
            $('#answer_id'+id+'{$value.id}').attr('disabled', 'true');
            $('#content'+id+'{$value.id}').attr('disabled', 'true');
            $('#is_email'+id+'{$value.id}').attr('disabled', 'true');
            $('#chk_tqid'+id+'{$value.id}').attr('disabled', 'true');
          }else {
            if(flag == 0){
              $("#flag"+id+ansid).val(1);
              //Action button check box
              $('#answer_id'+id+ansid).removeAttr('disabled');
              $('#content'+id+ansid).removeAttr('disabled');
              $('#is_email'+id+ansid).removeAttr('disabled');
              $('#chk_tqid'+id+ansid).removeAttr('disabled');
            }else {
              $("#flag"+id+ansid).val(0);
              //Action button check box
              $('#answer_id'+id+ansid).attr('disabled', 'true');
              $('#content'+id+ansid).attr('disabled', 'true');
              $('#is_email'+id+ansid).attr('disabled', 'true');
              $('#chk_tqid'+id+ansid).attr('disabled', 'true');
            }
            $("#is_required{$value.id}_"+id).val(0);
            $("#checkbox{$value.id}_"+id).removeAttr("required");
          }
        {/if}

        {if $v.type == 3}
          $('#is_required'+id).val(0);
          // $('#tq_id{$v.id}').removeAttr("disabled");
          $("#radio{$value.id}_"+id).removeAttr("required");
        {/if}

      {/foreach} //end fetch answer
    {/if}

  {/foreach} //fetch question

  //Action button radio
  $('#raanswer_id'+id).removeAttr('disabled');
  $('#racontent'+id).removeAttr('disabled');
  $('#rais_email'+id).removeAttr('disabled');
  $('#raanswer_id'+id).val(ansid);

  $('#tq_id'+id).removeAttr('disabled');

}
//if error submit
function sessionAnswerId(){

  {if $sessionAnswerIdError|@count gt 0}
    //fetch question
    {foreach item=v from=$result key=id}
      {if $v.flag eq 1}

        {foreach item=g_v from= $v.group_answer key=id}
          //fetch answer
          {foreach item=g_value from=$g_v.answer}
            //fetch sessionAnswerIdError
            {foreach from=$sessionAnswerIdError item=ansid}
              {if $g_value.id eq $ansid}
                {if $g_v.type == 3}
                  $('#is_required{$g_v.id}').val(0);
                  $('#tq_id{$g_v.id}').removeAttr("disabled");
                  $("#radio{$ansid}_{$g_v.id}").removeAttr("required");
                {/if}

                {if $g_v.type == 4}
                  {foreach item=g_value from=$g_v.answer}
                    $('#is_required{$g_value.id}_{$g_v.id}').val(0);
                  {/foreach}

                  $('#chk_tqid{$g_v.id}{$ansid}').removeAttr("disabled");
                  $("#checkbox{$ansid}_{$g_v.id}").removeAttr("required");

                  $("#flag{$g_v.id}{$ansid}").val(1);
                  //Action button check box
                  $('#answer_id{$g_v.id}{$ansid}').removeAttr('disabled');
                  $('#content{$g_v.id}{$ansid}').removeAttr('disabled');
                  $('#is_email{$g_v.id}{$ansid}').removeAttr('disabled');
                  $('#chk_tqid{$g_v.id}{$ansid}').removeAttr('disabled');
                {/if}

                //Action button radio
                $('#raanswer_id{$g_v.id}').removeAttr('disabled');
                $('#racontent{$g_v.id}').removeAttr('disabled');
                $('#rais_email{$g_v.id}').removeAttr('disabled');
                $('#raanswer_id{$g_v.id}').val({$ansid});
              {/if}
            {/foreach}//endfetch sessionAnswerIdError
          {/foreach}//end fetch answer
        {/foreach}
      {else}
        //fetch answer
        {foreach item=value from=$v.answer}

          {foreach from=$sessionAnswerIdError item=ansid}
            {if $value.id eq $ansid}

              {if $v.type == 3}
                $('#is_required{$v.id}').val(0);
                $('#tq_id{$v.id}').removeAttr("disabled");
                $("#radio{$ansid}_{$v.id}").removeAttr("required");
              {/if}

              {if $v.type == 4}
                {foreach item=value from=$v.answer}
                  $('#is_required{$value.id}_{$v.id}').val(0);
                {/foreach}

                $('#chk_tqid{$v.id}{$ansid}').removeAttr("disabled");
                $("#checkbox{$ansid}_{$v.id}").removeAttr("required");

                $("#flag{$v.id}{$ansid}").val(1);
                //Action button check box
                $('#answer_id{$v.id}{$ansid}').removeAttr('disabled');
                $('#content{$v.id}{$ansid}').removeAttr('disabled');
                $('#is_email{$v.id}{$ansid}').removeAttr('disabled');
                $('#chk_tqid{$v.id}{$ansid}').removeAttr('disabled');

              {/if}

              //Action button radio
              $('#raanswer_id{$v.id}').removeAttr('disabled');
              $('#racontent{$v.id}').removeAttr('disabled');
              $('#rais_email{$v.id}').removeAttr('disabled');
              $('#raanswer_id{$v.id}').val({$ansid});
            {/if}
          {/foreach}//end fetch sessionAnswerIdError

        {/foreach}//end fetch answer

      {/if}

    {/foreach}
  {/if}

  {if $contentError|@count gt 0}
    {foreach from=$contentError item=va}
      {if $va.content neq 'NULL'}
        $('#tq_id{$va.tqid}').removeAttr('disabled');
        $('#answer_id{$va.tqid}').removeAttr('disabled');
        $('#content{$va.tqid}').removeAttr('disabled');
        $('#is_email{$va.tqid}').removeAttr('disabled');
        $('#text_is_required{$va.tqid}').val(0);
        $('#content{$va.tqid}').val("{$va.content}");
        $('#test{$va.tqid}').val("{$va.content}");

      {/if}
    {/foreach}

  {/if}

}

// Remove saved data from sessionStorage
function clear_session_jump_to()
{
  {foreach item = v from= $result key=id}
    {if $test_group_id eq '' OR $error}
      sessionStorage.removeItem('jump_to{$v.id}');
      sessionStorage.removeItem('view_order{$v.id}');
      sessionStorage.removeItem('q_jumpto_view_order{$v.id}');
    {/if}
  {/foreach}
  sessionStorage.removeItem('sess_jump_to_view_order');
  sessionStorage.removeItem('sess_view_order');

  {if $error}
  sessionStorage.removeItem('jump_to_group{$test_group_id|escape}');
  sessionStorage.removeItem('view_order_group{$test_group_id|escape}');
  {/if}
}

window.onload = function(e)
{
  clear_session_jump_to();
  session_jumping_to();
  sessionAnswerId();
  // check_value();

  {foreach item = v from= $result key=id}
    {if $error}
      // Remove saved data from sessionStorage
      sessionStorage.removeItem('session_jump_to{$v.id}');
    {/if}
  {/foreach}

  {if $error}
    {foreach from=$testQueGroup item=va}
      // Remove saved data from sessionStorage
      sessionStorage.removeItem('jump_to{$va.id}');
      sessionStorage.removeItem('view_order{$va.id}');
    {/foreach}
  {/if}

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
        $('#tq_id{$v.id}').removeAttr("disabled");
        $('#rais_email{$v.tqid}').removeAttr('disabled');
        $('#raanswer_id{$v.tqid}').removeAttr('disabled');
        $('#racontent{$v.tqid}').removeAttr('disabled');
        $('#radio{$v.answer_id}_{$v.tqid}').removeAttr("required");
        $('#radio{$v.answer_id}_{$v.tqid}').attr('checked', true);
      {/if}

      {if $v.type == 4}
        //Action button check box
        $('#is_required{$v.answer_id}_{$v.tqid}').val(0);
        $("#flag{$v.tqid}{$v.answer_id}").val(1);

        $('#chk_tqid{$v.tqid}{$v.answer_id}').removeAttr("disabled");
        $('#is_email{$v.tqid}{$v.answer_id}').removeAttr('disabled');
        $('#answer_id{$v.tqid}{$v.answer_id}').removeAttr('disabled');
        $('#content{$v.tqid}{$v.answer_id}').removeAttr('disabled');

        $("#checkbox{$v.answer_id}_{$v.tqid}").removeAttr("required");
        $("#checkbox{$v.answer_id}_{$v.tqid}").attr('checked', true);

      {/if}

    {/foreach}
  {/if}

}

function check_value(){

  {foreach item = v from= $result key=id}
    {if $v.is_required eq 1}
      var check_value = document.getElementsByClassName('check_value{$v.id}');

      var check_value{$v.id} = [];

      for (var i=0; i<check_value.length; i++) {
        //Get checked value
        if (check_value[i].checked && check_value[i].value > 0) {
          check_value{$v.id}.push({ answer: check_value[i].value });
        }
        if({$v.type} == 1 || {$v.type} == 2){
          if(check_value[i].value != ''){
            check_value{$v.id}.push({ answer: check_value[i].value });
          }
        }
      }

      //fetch answer
      {foreach item=value from=$v.answer key=k}
        //Remove Required
        if(check_value{$v.id}.length == 0){
          $('#is_required{$value.id}_{$v.id}').val(1);
          $("#checkbox{$value.id}_{$v.id}").attr("required", "true");
          $("#radio{$value.id}_{$v.id}").attr("required", "true");
          $('#answer{$v.id}').attr('disabled','disabled');
          $('#answer_id{$v.id}').attr('disabled','disabled');
          $('#content{$v.id}').attr('disabled','disabled');
          $('#is_email{$v.id}').attr('disabled','disabled');
        }else {

          $('#is_required{$value.id}_{$v.id}').val(0);
          $("#checkbox{$value.id}_{$v.id}").removeAttr("required");
          $("#radio{$value.id}_{$v.id}").removeAttr("required");
          $('#answer{$v.id}').removeAttr('disabled');
          $('#answer_id{$v.id}').removeAttr('disabled');
          $('#content{$v.id}').removeAttr('disabled');
          $('#is_email{$v.id}').removeAttr('disabled');
          //Action button radio
          $('#raanswer_id{$v.id}').removeAttr('disabled');
          $('#racontent{$v.id}').removeAttr('disabled');
          $('#rais_email{$v.id}').removeAttr('disabled');
          //Action button check box
          $('#answer_id{$v.id}{$value.id}').removeAttr('disabled');
          $('#content{$v.id}{$value.id}').removeAttr('disabled');
          $('#is_email{$v.id}{$value.id}').removeAttr('disabled');
        }

      {/foreach}

    {/if}
  {/foreach}

}

function session_jumping_to()
{
  var jump_to = sessionStorage.getItem('jump_to_group{$test_group_id}');
  var view_order = sessionStorage.getItem('view_order_group{$test_group_id}');

  {if $sessionAnswerId|@count gt 0}
    //fetch question
    {foreach item = v from= $result key=id}
      {if $v.flag eq 1}

        {foreach item=g_v from= $v.group_answer key=id}
          //fetch answer
          {foreach item=g_value from=$g_v.answer}
            //fetch sessionAnswerIdError
            {foreach from=$sessionAnswerId item=ansid}
              {if $g_value.id eq $ansid}
                {if $g_v.type == 3}
                  $('#is_required{$g_v.id}').val(0);
                  $('#tq_id{$g_v.id}').removeAttr("disabled");
                  $("#radio{$ansid}_{$g_v.id}").removeAttr("required");
                {/if}

                {if $g_v.type == 4}
                  $('#is_required{$ansid}_{$g_v.id}').val(0);
                  $('#chk_tqid{$g_v.id}{$ansid}').removeAttr("disabled");
                  $("#checkbox{$ansid}_{$g_v.id}").removeAttr("required");

                  $("#flag{$g_v.id}{$ansid}").val(1);
                  //Action button check box
                  $('#answer_id{$g_v.id}{$ansid}').removeAttr('disabled');
                  $('#content{$g_v.id}{$ansid}').removeAttr('disabled');
                  $('#is_email{$g_v.id}{$ansid}').removeAttr('disabled');
                  $('#chk_tqid{$g_v.id}{$ansid}').removeAttr('disabled');
                {/if}

                //Action button radio
                $('#raanswer_id{$g_v.id}').removeAttr('disabled');
                $('#racontent{$g_v.id}').removeAttr('disabled');
                $('#rais_email{$g_v.id}').removeAttr('disabled');
                $('#raanswer_id{$g_v.id}').val({$ansid});
              {/if}
            {/foreach}//endfetch sessionAnswerIdError
          {/foreach}//end fetch answer
        {/foreach}

      {else}
        //fetch answer
        {foreach item=value from=$v.answer}

          {foreach from=$sessionAnswerIdError item=ansid}
            {if $value.id eq $ansid}

              {if $v.type == 3}
                $('#is_required{$v.id}').val(0);
                $('#tq_id{$v.id}').removeAttr("disabled");
                $("#radio{$ansid}_{$v.id}").removeAttr("required");
              {/if}

              {if $v.type == 4}
                $('#is_required{$ansid}_{$v.id}').val(0);
                $('#chk_tqid{$v.id}{$ansid}').removeAttr("disabled");
                $("#checkbox{$ansid}_{$v.id}").removeAttr("required");
              {/if}

              //Action button radio
              $('#raanswer_id{$v.id}').removeAttr('disabled');
              $('#racontent{$v.id}').removeAttr('disabled');
              $('#rais_email{$v.id}').removeAttr('disabled');
              $('#raanswer_id{$v.id}').val({$ansid});
            {/if}
          {/foreach}//end fetch sessionAnswerIdError

        {/foreach}//end fetch answer

      {/if}

    {/foreach}

    {if $sessionContentBack|@count gt 0}
      {foreach from=$sessionContentBack item=va}
        {if $va.content neq 'NULL'}
          $('#tq_id{$va.tqid}').removeAttr('disabled');
          $('#answer_id{$va.tqid}').removeAttr('disabled');
          $('#content{$va.tqid}').removeAttr('disabled');
          $('#is_email{$va.tqid}').removeAttr('disabled');
          $('#text_is_required{$va.tqid}').val(0);
          $('#content{$va.tqid}').val("{$va.content}");
          $('#test{$va.tqid}').val("{$va.content}");

        {/if}
      {/foreach}

    {/if}

  {else}

    {foreach from=$testQueGroup item=va}
    // sessionStorage.setItem('q_jumpto_view_order_group{$test_group_id}', q_jumpto_view_order);
      var jump_to = sessionStorage.getItem('jump_to_group{$va.id|escape}');
      var view_order = sessionStorage.getItem('view_order_group{$va.id|escape}');
      var q_jumpto_view_order = sessionStorage.getItem('q_jumpto_view_order_group{$va.id|escape}');
      //fetch question check get view_order
      {foreach item = v from= $result key=id}
        {if $v.id neq ''}
          if(jump_to == {$v.id}) view_order = {$v.view_order};
        {/if}
      {/foreach}

      if(q_jumpto_view_order != null){
        //fetch question
        {foreach item = v from= $result key=id}
          //fetch answer
          {foreach item=value from=$v.answer}
            if({$v.view_order} < q_jumpto_view_order){
              $("#required_{$v.id}").hide();

              {if $v.type == 1 || $v.type == 2}
                $('#is_required{$value.id}_{$v.id}').val(0);
                $("#test{$v.id}").attr("disabled", "true");
              {/if}

              {if $v.type == 3}
                $('#is_required{$value.id}_{$v.id}').val(0);
                $("#radio{$value.id}_{$v.id}").attr("disabled", "true");
              {/if}

              {if $v.type == 4}
                $('#is_required{$value.id}_{$v.id}').val(0);
                $("#checkbox{$value.id}_{$v.id}").attr("disabled", "true");
              {/if}
            }
          {/foreach}
        {/foreach}
      }
    {/foreach}
  {/if}



}


</script>
{/block}
