{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTest" href="{$admin_file}?task=test">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_test_question_view_order}{$multiLang.text_test_question_view_order}{else}No Translate(Key Lang: text_test_question_view_order){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>

<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_test_question_view_order}{$multiLang.text_test_question_view_order}{else}No Translate(Key Lang: text_test_question_view_order){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</b> {$test.title}
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=test_question_view_order" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="test_question_view_order">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_test_question_view_order}{$multiLang.button_add_test_question_view_order}{else}No Translate(Key Lang: button_add_test_question_view_order){/if}
            </button>
          </div>
        </form>
        <div id="demo" class="collapse {if $error OR $getViewOrderByID.id} in {/if}">
          {if $getViewOrderByID.id}
          <form action="{$admin_file}?task=test_question_view_order&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$getViewOrderByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=test_question_view_order&amp;tid={$smarty.get.tid}" method="post">
          {/if}
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_view_Order}{$multiLang.text_view_Order}{else}No Translate(Key Lang: text_view_Order){/if}:</label>
                  {if $error.view_order}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_view_Order}{$multiLang.text_view_Order}{else}No Translate(Key Lang: text_view_Order){/if}</span>
                  {/if}
                  <input type="text" name="view_order" class="form-control" placeholder="{if $multiLang.text_example}{$multiLang.text_example}{else}No Translate(Key Lang: text_example){/if}: 123..."
                  value="{if $getViewOrderByID.view_order}{$getViewOrderByID.view_order}{else}{if $smarty.session.view_order.view_order}{$smarty.session.view_order.view_order}{/if}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}:</label>
                  {if $error.is_view_order_exist}
                    <span style="color: red">{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang:text_test_question){/if} {if $multiLang.text_is_existed}{$multiLang.text_is_existed}{else}No Translate(Key Lang: text_is_existed){/if}</span>
                  {/if}
                  {if $error.test_question}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</span>
                  {/if}
                  <select class="form-control select2" name="test_question" style="width:100%">
                    <option value="{$data.tqid}">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} ---</option>
                    {foreach from=$listTestQueGroupAnswer item=data}
                    <option value="{$data.tqid}" {if $getViewOrderByID.id}{if $getViewOrderByID.test_question_id neq $data.tqid}{if $data.tq_view_order}disabled{/if}{/if}{else}{if $data.tq_view_order}disabled{/if}{/if} {if $getViewOrderByID.test_question_id}{if $getViewOrderByID.test_question_id eq $data.tqid}selected{/if}{else}{if $smarty.session.view_order.test_question eq $data.tqid}selected{/if}{/if}>
                    {if $data.g_answer_title}
                      (Group Question): {$data.g_answer_title}
                    {else}
                      (Question): {$data.que_title}, (Description): {$data.description}
                    {/if}
                    </option>

                    {/foreach}
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getViewOrderByID.id}
                    <input type="hidden" name="id" value="{$getViewOrderByID.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=test_question_view_order&amp;tid={$smarty.get.tid}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
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
            <th>{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</th>
            <th>{if $multiLang.text_description}{$multiLang.text_description}{else}No Translate(Key Lang: text_description){/if}</th>
            <th>{if $multiLang.text_view_Order}{$multiLang.text_view_Order}{else}No Translate(Key Lang: text_view_Order){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</th>
            <th width="100">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listTestQestionViewOrder|@count gt 0}
        <tbody>
        {foreach from = $listTestQestionViewOrder item = data key=k}
          <tr>
            <td>
              {if $data.g_answer_title}
                (Group Question): {$data.g_answer_title}
              {else}
                (Question): {$data.que_title}
              {/if}
            </td>
            <td>{$data.description}</td>
            <td>{$data.view_order}</td>
            <td>
              <a href="{$admin_file}?task=test_question_view_order&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
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
                      <a href="{$admin_file}?task=test_question_view_order&amp;action=delete&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</i></a>
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
