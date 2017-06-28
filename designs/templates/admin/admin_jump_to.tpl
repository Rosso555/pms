{extends file="admin/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTQue" href="{$admin_file}?task=test_question&amp;tid={$smarty.get.tid}">{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</a></li>
  <li><a id="bCrumbAns" href="{$admin_file}?task=answer&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}">{if $multiLang.text_answer}{$multiLang.text_answer}{else}No Translate(Key Lang: text_answer){/if}</a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_jumping_to}{$multiLang.text_jumping_to}{else}No Translate(Key Lang: text_jumping_to){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title"> {if $multiLang.text_jumping_to}{$multiLang.text_jumping_to}{else}No Translate(Key Lang: text_jumping_to){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_question}{$multiLang.text_question}{else}No Translate(Key Lang: text_question){/if}:</b> {$question.title} (Type:{if $question.type eq 1} Text Input{elseif $question.type eq 2}Text Area{elseif $question.type eq 3}Redio{elseif $question.type eq 4}CheckBox{/if})
    </div>
    <div class="box_title">
      <b>{if $multiLang.text_answer}{$multiLang.text_answer}{else}No Translate(Key Lang: text_answer){/if}:</b> {$answer.title}
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
          <form action="{$admin_file}?task=jump_to&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;ans_id={$smarty.get.ans_id}" method="post">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_jump_to_question}{$multiLang.text_jump_to_question}{else}No Translate(Key Lang: text_jump_to_question){/if}:</label>
                  {if $error.question}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if}{if $multiLang.text_jump_to_question}{$multiLang.text_jump_to_question}{else}No Translate(Key Lang: text_jump_to_question){/if}.</span>
                  {/if}
                  <br>
                  <select class="form-control select2" name="question" style="width:100%">
                    <option value="">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} {if $multiLang.text_jump_to_question}{$multiLang.text_jump_to_question}{else}No Translate(Key Lang: text_jump_to_question){/if} ---</option>
                    {foreach from=$listTestQuestion item=data}
                    <option value="{$data.id}" {if $jump_toByID.jump_to eq $data.id}selected{/if}{if $getCheckViewOrder.jump_to_view_order}{if $data.view_order lte $getCheckViewOrder.jump_to_view_order}disabled{/if}{else}{if $data.view_order lte $test_question.view_order}disabled{/if}{/if}>
                      {$data.q_title} (Type:{if $data.type eq 1} Text Input{elseif $data.type eq 2}Text Area{elseif $data.type eq 3}Redio{elseif $data.type eq 4}CheckBox{/if})
                    </option>
                    {/foreach}
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $jump_toByID.jump_to}
                    <button type="submit" name="butsubmit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                  {else}
                    <button type="submit" name="butsubmit" class="btn btn-info" {if $answer.jump_to}disabled{/if}><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate(Key Lang: button_save){/if}</button>
                  {/if}
                  <a id="btnBack" href="javascript:history.back()" class="btn btn-warning"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>

                  <!-- <a class="btn btn-warning" href="{$admin_file}?task=answer&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}"><i class="fa fa-step-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a> -->
                </div>
              </div>
            </div>
          </form>
        <!-- </div> -->
      </div>
    </div>
    <!--panel panel-body-->
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
            <th>{if $multiLang.text_question}{$multiLang.text_question}{else}No Translate(Key Lang: text_question){/if}</th>
            <th width="100">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        <tbody>
          {if $question_jump_to.title_que}
          <tr>
            <td>{$question_jump_to.title_que}</td>
            <td>
              <a href="{$admin_file}?task=jump_to&amp;action=edit&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;ans_id={$smarty.get.ans_id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
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
                      <p>{if $multiLang.text_delete_question}{$multiLang.text_delete_question}{else}No Translate(Key Lang: text_delete_question){/if} <b>({$question_jump_to.title_que})</b>?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=jump_to&amp;action=delete&amp;tid={$smarty.get.tid}&amp;qid={$smarty.get.qid}&amp;tqid={$smarty.get.tqid}&amp;ans_id={$smarty.get.ans_id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</i></button>
                    </div>
                  </div>
                </div>
              </div>
            <!-- Modal -->
            </td>
          </tr>
          {else}
          <tr>
            <td colspan="7"><h4 style="text-align:center">{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h4></td>
          </tr>
          {/if}
        </tbody>
      </table>
    </div>
    <!--table-responsive  -->
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
