{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTest" href="{$admin_file}?task=test">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</a></li>
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
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=result" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="tid" value="{$smarty.get.tid}">
          <input type="hidden" name="task" value="result">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_result}{$multiLang.button_add_result}{else}No Translate(Key Lang: button_add_result){/if}
            </button>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getResultByID.id} in {/if}">
          {if $getResultByID.id}
          <form action="{$admin_file}?task=result&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$getResultByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=result&amp;action=add&amp;tid={$smarty.get.tid}" method="post">
          {/if}

            <input type="hidden" name="tid" value="{$smarty.get.tid}">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_score_from}{$multiLang.text_score_from}{else}No Translate(Key Lang: text_score_from){/if}:</label>
                  {if $error.score_from}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_score_from}{$multiLang.text_score_from}{else}No Translate(Key Lang: text_score_from){/if}.</span>
                  {/if}
                  <input type="text" name="score_from" class="form-control" placeholder="Example: 123..."
                  value="{if $smarty.session.result.score_from}{$smarty.session.result.score_from}{elseif $getResultByID.score_from}{$getResultByID.score_from}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_score_to}{$multiLang.text_score_to}{else}No Translate(Key Lang: text_score_to){/if}:</label>
                  {if $error.score_to}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_score_to}{$multiLang.text_score_to}{else}No Translate(Key Lang: text_score_to){/if}.</span>
                  {/if}
                  <input type="text" class="form-control" name="score_to" placeholder="Example: 123..." value="{if $smarty.session.result.score_to}{$smarty.session.result.score_to}{elseif $getResultByID.score_to}{$getResultByID.score_to}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</label>
                  {if $error.topic}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</span>
                  {/if}
                  <select class="form-control select2" name="topic" style="width:100%">
                    <option value="">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if} ---</option>
                    {foreach from=$topic item=data}
                    <option value="{$data.id}" {if $smarty.session.result.topic}{if $smarty.session.result.topic eq $data.id}selected{/if}{else}{if $getResultByID.topic_id eq $data.id}selected{/if}{/if}>{$data.name}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="comment"><span style="color: red">*</span> {if $multiLang.text_message}{$multiLang.text_message}{else}No Translate(Key Lang: text_message){/if}:</label>
                  {if $error.message}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_message}{$multiLang.text_message}{else}No Translate(Key Lang: text_message){/if}.</span>
                  {/if}
                  <textarea class="form-control" name="message" rows="5" id="comment">{if $smarty.session.result.message}{$smarty.session.result.message}{elseif $getResultByID.message}{$getResultByID.message}{/if}</textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getResultByID.id}
                    <input type="hidden" name="rid" value="{$getResultByID.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=result&amp;tid={$smarty.get.tid}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
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
          <th>{if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</th>
          <th>{if $multiLang.text_score_from}{$multiLang.text_score_from}{else}No Translate(Key Lang: text_score_from){/if}</th>
          <th>{if $multiLang.text_score_to}{$multiLang.text_score_to}{else}No Translate(Key Lang: text_score_to){/if}</th>
          <th>{if $multiLang.text_message}{$multiLang.text_message}{else}No Translate(Key Lang: text_message){/if}</th>
          <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listResult|@count gt 0}
        <tbody>
        {foreach from = $listResult item = data key=k}
          <tr>
            <td>{$data.name}</td>
            <td>{$data.score_from}</td>
            <td>{$data.score_to}</td>
            <td>{$data.message}</td>
            <td>
              <a href="{$admin_file}?task=result&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
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
                      <p>{if $multiLang.text_delete_result}{$multiLang.text_delete_result}{else}No Translate(Key Lang: text_delete_result){/if}
                         <b>({$data.score_from|escape} & {$data.score_to|escape})</b> ?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=result&amp;action=delete&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;">
                        <i class="fa fa-trash-o"></i> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}
                      </a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
              <a href="{$admin_file}?task=result_condition&amp;tid={$smarty.get.tid}&amp;tpid={$data.topic_id}&amp;rid={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_result_condition}{$multiLang.button_add_result_condition}{else}No Translate(Key Lang: button_add_result_condition){/if}"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
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
      <a id="btnBack" href="{$admin_file}?task=test" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
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
