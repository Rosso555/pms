{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTestGroup" href="{$admin_file}?task=test_group">{if $multiLang.text_test_group}{$multiLang.text_test_group}{else}No Translate(Key Lang: text_test_group){/if}</a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_test_group_question}{$multiLang.text_test_group_question}{else}No Translate(Key Lang: text_test_group_question){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_test_group_question}{$multiLang.text_test_group_question}{else}No Translate(Key Lang: text_test_group_question){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</b> {$test.title} - {$test_group.title}
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=test_group_question" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="test">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_test_group_question}{$multiLang.button_add_test_group_question}{else}No Translate(Key Lang: button_add_test_group_question){/if}
            </button>
          </div>
        </form>

        <div id="demo" class="collapse {if $error or $getTestGQByID.id} in {/if}">
          {if $getTestGQByID.id}
          <form action="{$admin_file}?task=test_group_question&amp;action=edit&amp;{$smarty.get.tid}&amp;tgid={$smarty.get.tgid}&amp;id={$getTestGQByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=test_group_question&amp;tid={$smarty.get.tid}&amp;tgid={$smarty.get.tgid}" method="post">
          {/if}
            <div class="row">
              <div class="col-md-12">
                <div class="form-group multi_select2">
                  <input type="hidden" id="select2_placeholder" value="{if $multiLang.text_select}{$multiLang.text_select}{else}No Translate (Key Lang:text_select){/if} {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}:</label>
                  {if $error.test_question}
                    <span style="color: red">{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</span>
                  {/if}
                  {if $error.is_exist_test_group_que}
                    <span style="color: red">{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if} {if $multiLang.text_is_existed}{$multiLang.text_is_existed}{else}No Translate(Key Lang: text_is_existed){/if}</span>
                  {/if}
                  <select class="form-control select2_mul" multiple="multiple" name="test_question[]" style="width:100%">
                    <!-- <option value="{$data.tqid}">- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} -</option> -->
                    {foreach from=$listTestQueGroupAnswer item=data}
                    <option value="{$data.tqid}" {if $data.test_ques_group}disabled{/if}>
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
              <!-- <div class="col-md-6">
                &nbsp;
              </div> -->
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getTestGQByID.id}
                    <input type="hidden" name="id" value="{$getTestGQByID.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=test_group_question&amp;tid={$smarty.get.tid}&amp;tgid={$smarty.get.tgid}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
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
            <th>{if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</th>
            <th width="80">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listTestGroupQuestion|@count gt 0}
        <tbody>
        {foreach from = $listTestGroupQuestion item = data key=k}
          <tr>
            <td>
              {if $data.g_answer_title}
                (Group Question): {$data.g_answer_title}
              {else}
                (Question): {$data.que_title}, (Description): {$data.description}
              {/if}
            </td>
            <td>
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
                      <p>
                        {if $multiLang.text_delete_test_group_question}{$multiLang.text_delete_test_group_question}{else}No Translate(Key Lang: text_delete_test_group_question){/if}
                         <b>"{if $data.g_answer_title}
                             (Group Question): {$data.g_answer_title}
                           {else}
                             (Question): {$data.que_title}, (Description): {$data.description}
                           {/if}"
                         </b>?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=test_group_question&amp;action=delete&amp;tid={$smarty.get.tid}&amp;tgid={$smarty.get.tgid}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</i></a>
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
      <a id="btnBack" href="{$admin_file}?task=test_group" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
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
  if(url.task === 'test_group') localStorage.setItem('urlTestGroup',urlBack);
  //Get session url
  var getUrlBack = localStorage.getItem('urlTestGroup');
  if(getUrlBack !== null){
    $("#btnBack").attr("href", getUrlBack);
    $("#bCrumbTestGroup").attr("href", getUrlBack);
  }
  //End previous url
</script>
{/block}
