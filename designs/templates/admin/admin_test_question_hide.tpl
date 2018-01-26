{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTest" href="{$admin_file}?task=test">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</a></li>
  <li {if $smarty.get.action neq edit}class="active"{/if}>{if $multiLang.text_test_question_hide}{$multiLang.text_test_question_hide}{else}No Translate(Key Lang: text_test_question_hide){/if}</li>
  {if $smarty.get.action eq edit}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
{if $smarty.cookies.checkResultFrom}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot delete "<strong>{$smarty.cookies.checkResultTopic},{$smarty.cookies.checkResultFrom} &amp; {$smarty.cookies.checkResultTo}</strong>" because it has been used.
  </div>
{/if}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title"> {if $multiLang.text_test_question_hide}{$multiLang.text_test_question_hide}{else}No Translate(Key Lang: text_test_question_hide){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}:</b> {$test.title}
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=result" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="tid" value="{$smarty.get.tid}">
          <input type="hidden" name="task" value="result">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_test_question_hide}{$multiLang.button_add_test_question_hide}{else}No Translate(Key Lang: button_add_test_question_hide){/if}
            </button>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getTestQueHideByID.id} in {/if}">
          {if $getTestQueHideByID.id}
          <form action="{$admin_file}?task=test_question_hide&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$getTestQueHideByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=test_question_hide&amp;action=add&amp;tid={$smarty.get.tid}" method="post">
          {/if}
            <input type="hidden" name="tid" value="{$smarty.get.tid}">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}:</label>
                  {if $error.test_que_exist}
                    <span style="color: red">{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang:text_test_question){/if} {if $multiLang.text_is_existed}{$multiLang.text_is_existed}{else}No Translate(Key Lang: text_is_existed){/if}</span>
                  {/if}
                  {if $error.test_question}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</span>
                  {/if}
                  <select class="form-control select2" name="test_question" style="width:100%">
                    <option value="{$data.tqid}">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} ---</option>
                    {foreach from=$listTestQuestion item=data}
                    <option value="{$data.id}" {if $getTestQueHideByID.test_question_id}{if $getTestQueHideByID.test_question_id eq $data.id}selected{/if}{else}{if $smarty.session.test_que_hide.test_question eq $data.id}selected{/if}{/if}>
                      (Question): {$data.q_title} / (Description): {$data.description} / ({if $data.type eq 1}Text Input{elseif $data.type eq 2}Text Area{elseif $data.type eq 3}Radio{elseif $data.type eq 4}Check Box{/if})
                    </option>
                    {/foreach}
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getTestQueHideByID.id}
                    <input type="hidden" name="id" value="{$getTestQueHideByID.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=test_question_hide&amp;tid={$smarty.get.tid}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
                  {else}
                    <button type="submit" name="butsubmit" class="btn btn-danger"><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate(Key Lang: button_save){/if}</button>
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
            <th>{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</th>
            <th>{if $multiLang.text_description}{$multiLang.text_description}{else}No Translate(Key Lang: text_description){/if}</th>
            <th>{if $multiLang.text_condition}{$multiLang.text_condition}{else}No Translate(Key Lang: text_condition){/if}</th>
            <th width="100">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listTestQueHide|@count gt 0}
        <tbody>
        {foreach from = $listTestQueHide item = data key=k}
          <tr>
            <td>{$data.que_title}</td>
            <td>{$data.description}</td>
            <td><span class="badge">{$data.total_tqh}</span></td>
            <td>
              <a href="{$admin_file}?task=test_question_hide&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
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
                      <p>{if $multiLang.text_delete_test_question_confirmation}{$multiLang.text_delete_test_question_confirmation}{else}No Translate(Key Lang: text_delete_test_question_confirmation){/if}
                        <b>({if $data.g_answer_title}
                          (Group Question): {$data.g_answer_title}
                        {else}
                          (Question): {$data.que_title}
                        {/if})</b>?
                      </p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=test_question_hide&amp;action=delete&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</i></button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
              <a href="{$admin_file}?task=test_question_hide_condition&amp;tid={$data.test_id}&amp;tqid={$data.test_question_id}&amp;tqh_id={$data.id}" class="btn btn-warning btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_test_que_hide_condition}{$multiLang.button_add_test_que_hide_condition}{else}No Translate(Key Lang: button_add_test_que_hide_condition){/if}"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
            </td>

          </tr>
        {/foreach}
        </tbody>
        {else}
        <tr>
          <td colspan="3"><h4 style="text-align:center">{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h4></td>
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
