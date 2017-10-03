{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTest" href="{$admin_file}?task=test">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</a></li>
  <li><a id="bCrumbNewResult" href="{$admin_file}?task=test">{if $multiLang.text_new_result}{$multiLang.text_new_result}{else}No Translate(Key Lang: text_new_result){/if}</a></li>
  <li {if $smarty.get.action neq edit}class="active"{/if}>{if $multiLang.text_result}{$multiLang.text_result}{else}No Translate(Key Lang: text_result){/if}</li>
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
  <div class="panel-heading"><h4 class="panel-title"> {if $multiLang.text_result}{$multiLang.text_result}{else}No Translate(Key Lang: text_result){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}:</b> {$test.title}
    </div>
    <div class="box_title">
      <b>{if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}:</b> {$topic.name},
      <b>{if $multiLang.text_message}{$multiLang.text_message}{else}No Translate(Key Lang: text_message){/if}:</b> {$new_result.message}
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=result" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="tid" value="{$smarty.get.tid}">
          <input type="hidden" name="task" value="result">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_test_question_condition}{$multiLang.button_add_test_question_condition}{else}No Translate(Key Lang: button_add_test_question_condition){/if}
            </button>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getTestQuesConByID.id} in {/if}">
          {if $getTestQuesConByID.id}
          <form action="{$admin_file}?task=test_question_condition&amp;action=edit&amp;tid={$smarty.get.tid}&amp;tpid={$smarty.get.tpid}&amp;nrid={$smarty.get.nrid}&amp;id={$getTestQuesConByID.id}&amp;next={$smarty.get.next}" method="post">
          {else}
          <form action="{$admin_file}?task=test_question_condition&amp;action=add&amp;tid={$smarty.get.tid}&amp;tpid={$smarty.get.tpid}&amp;nrid={$smarty.get.nrid}&amp;next={$smarty.get.next}" method="post">
          {/if}
            <input type="hidden" name="nrid" value="{$smarty.get.nrid}">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if} :</label>
                  {if $error.topic}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</span>
                  {/if}
                  <select class="form-control select2" name="tqid" style="width:100%">
                    <option value="">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if} ---</option>
                    {foreach from=$listTestQuestion item=data}
                    <option value="{$data.id}" {if $smarty.session.test_que_con.tqid}{if $smarty.session.test_que_con.tqid eq $data.id}selected{/if}{else}{if $getTestQuesConByID.test_question_id eq $data.id}selected{/if}{/if}>
                      (View Order: {$data.view_order}) {$data.q_title} (Type:{if $data.type eq 1} Text Input{elseif $data.type eq 2}Text Area{elseif $data.type eq 3}Redio{elseif $data.type eq 4}CheckBox{/if})
                    </option>
                    {/foreach}
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_condition}{$multiLang.text_condition}{else}No Translate(Key Lang: text_condition){/if}:</label>
                  {if $error.conditional}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_less_than_value}{$multiLang.text_less_than_value}{else}No Translate(Key Lang: text_less_than_value){/if}.</span>
                  {/if}
                  <select class="form-control select2" name="conditional" style="width:100%">
                    <option value="">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} {if $multiLang.text_condition}{$multiLang.text_condition}{else}No Translate(Key Lang: text_condition){/if} ---</option>
                    <option value="1" {if $getTestQuesConByID.conditional eq 1}selected{elseif $smarty.session.test_que_con.conditional eq 1}selected{/if}> > </option>
                    <option value="2" {if $getTestQuesConByID.conditional eq 2}selected{elseif $smarty.session.test_que_con.conditional eq 2}selected{/if}> < </option>
                    <option value="3" {if $getTestQuesConByID.conditional eq 3}selected{elseif $smarty.session.test_que_con.conditional eq 3}selected{/if}> = </option>
                    <option value="4" {if $getTestQuesConByID.conditional eq 4}selected{elseif $smarty.session.test_que_con.conditional eq 4}selected{/if}> >= </option>
                    <option value="5" {if $getTestQuesConByID.conditional eq 5}selected{elseif $smarty.session.test_que_con.conditional eq 5}selected{/if}> <= </option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_value}{$multiLang.text_value}{else}No Translate(Key Lang: text_value){/if}:</label>
                  {if $error.value}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_value}{$multiLang.text_value}{else}No Translate(Key Lang: text_value){/if}.</span>
                  {/if}
                  <input type="text" class="form-control" name="value" placeholder="Example: 123..." value="{if !$smarty.session.test_que_con.value AND !$getTestQuesConByID.value_condition}0{/if}{if $smarty.session.test_que_con.value}{$smarty.session.test_que_con.value}{elseif $getTestQuesConByID.value_condition}{$getTestQuesConByID.value_condition}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_operator}{$multiLang.text_operator}{else}No Translate(Key Lang: text_operator){/if}:</label>
                  {if $error.operater}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_operator}{$multiLang.text_operator}{else}No Translate(Key Lang: text_operator){/if}.</span>
                  {/if}
                  <select class="form-control select2" name="operator" style="width:100%">
                    <option value="">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} {if $multiLang.text_operator}{$multiLang.text_operator}{else}No Translate(Key Lang: text_operator){/if} ---</option>
                    <option value="1" {if $getTestQuesConByID.operator eq 1}selected{elseif $smarty.session.test_que_con.operator eq 1}selected{/if}> AND </option>
                    <option value="2" {if $getTestQuesConByID.operator eq 2}selected{elseif $smarty.session.test_que_con.operator eq 2}selected{/if}> OR </option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getTestQuesConByID.id}
                    <input type="hidden" name="tqcid" value="{$getTestQuesConByID.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=test_question_condition&amp;tid={$smarty.get.tid}&amp;tpid={$smarty.get.tpid}&amp;nrid={$smarty.get.nrid}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
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
    <div class="box_title">
      <b>Result Condition:</b> {$getViewConditonTestQue}
    </div>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
          <th>{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</th>
          <th>{if $multiLang.text_condition}{$multiLang.text_condition}{else}No Translate(Key Lang: text_condition){/if}</th>
          <th>{if $multiLang.text_value}{$multiLang.text_value}{else}No Translate(Key Lang: text_value){/if}</th>
          <th>{if $multiLang.text_operator}{$multiLang.text_operator}{else}No Translate(Key Lang: text_operator){/if}</th>
          <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listTestQueCondition|@count gt 0}
        <tbody>
        {foreach from = $listTestQueCondition item = data key=k}
          <tr>
            <td>{$data.q_title} (Type:{if $data.type eq 1} Text Input{elseif $data.type eq 2}Text Area{elseif $data.type eq 3}Redio{elseif $data.type eq 4}CheckBox{/if})</td>
            <td>
            {if $data.conditional eq 1}
              >
            {elseif $data.conditional eq 2}
              <
            {elseif $data.conditional eq 3}
              =
            {elseif $data.conditional eq 4}
              >=
            {else}
              <=
            {/if}
            </td>
            <td>{$data.value_condition}</td>
            <td>
            {if $data.operator eq 1}
              AND
            {else}
              OR
            {/if}
            </td>
            <td>
              <a href="{$admin_file}?task=test_question_condition&amp;action=edit&amp;tid={$smarty.get.tid}&amp;tpid={$smarty.get.tpid}&amp;nrid={$smarty.get.nrid}&amp;id={$data.id}&amp;next={$smarty.get.next}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
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
                      <p>{if $multiLang.text_delete_test_que_condition}{$multiLang.text_delete_test_que_condition}{else}No Translate(Key Lang: text_delete_test_que_condition){/if}
                         <b>"{$data.q_title} (Type:{if $data.type eq 1} Text Input{elseif $data.type eq 2}Text Area{elseif $data.type eq 3}Redio{elseif $data.type eq 4}CheckBox{/if})"</b> ?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=test_question_condition&amp;action=delete&amp;tid={$smarty.get.tid}&amp;tpid={$smarty.get.tpid}&amp;nrid={$smarty.get.nrid}&amp;id={$data.id}&amp;next={$smarty.get.next}" class="btn btn-danger btn-md" style="color: white;">
                        <i class="fa fa-trash-o"></i> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}
                      </a>
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

      <a href="{$index_file}?task=new_result&amp;tid={$smarty.get.tid}" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
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
  if(url.task === 'new_result') localStorage.setItem('urlNewResult',urlBack);
  //Get session url
  var getUrlBack = localStorage.getItem('urlNewResult');
  var getUrlBackTest = localStorage.getItem('urlTest');
  if(getUrlBack !== null){
    $("#btnBack").attr("href", getUrlBack);
    $("#bCrumbNewResult").attr("href", getUrlBack);
  }
  if(getUrlBackTest !== null) $("#bCrumbTest").attr("href", getUrlBackTest);
  //End previous url
</script>
{/block}
