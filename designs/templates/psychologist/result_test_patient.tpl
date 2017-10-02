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
  <h4 class="text-center">{$getTestById.title} {if $resultStep eq 1} ({if $multiLang.text_final_step}{$multiLang.text_final_step}{else}No Translate (Key Lang: text_final_step){/if}) {/if}</h4>
  <p class="text-center">{$getTestById.description}</p>
  <hr>
  <p class="text-center" style="font-size: 25px;">PATIENT: {$patient.username}</p>
</div>

<div class="inbox-test sansserif" style="margin-bottom: 20px;">
  <div class="row">
  {foreach from=$reponseAnswerByTestPat item=v}
    <div class="col-md-12 col-sm-12 col-xs-12">
      <p style="margin-top: 20px; {if $v.hide_title eq 1}margin-bottom: 0px;{/if}">{if $v.is_required eq 1}<span style="color:red;">*</span>{/if}<b> {if $v.hide_title eq 0}{$v.que_title}{else}{$v.description}{/if}</b></p>
      {if $v.hide_title eq 0}<p style="margin-left: 16px;">{$v.description}</p>{/if}
      <!-- text free input -->
      {if $v.type eq 1}
      <p style="margin-left: 16px; margin-top: 15px;">
        <input type="text"class="form-control" value="{$v.content}">
      </p>
      {elseif $v.type eq 2}
      <p style="margin-left: 16px; margin-top: 15px;">
        <textarea class="form-control"rows="3">{$v.content}</textarea>
      </p><!-- end text free input -->
      {elseif $v.type eq 3}
      <div class="radio" >
        <label for="radio" class="radio-inline" style="margin-bottom: 10px;">
          <input style="margin-top: -4px;" type="radio" value="{$v.answer_id}" checked="checked">
          <span style="line-height: 1.2;">{$v.ans_title}</span>
        </label>
      </div>
      {elseif $v.type eq 4}
      {foreach from=$v.result_answer item=ans}
      <label for="checkbox{$ans.id}_{$va.id}" class="checkbox-inline" style="margin-bottom: 10px;">
        <input style="margin-top: -4px;" type="checkbox" name="answer" value="{$v.answer_id}" checked="checked">
        <span style="line-height: 1.2;">{$ans.ans_title}</span>
      </label>
      {/foreach}
      {/if}
    </div>
  {/foreach}
  </div>
  <hr>
  <h3>Result And Graph</h3>
  <br><br><br><br>
  </form>
</div>
{/block}
{block name="javascript"}
<script>

</script>
{/block}
