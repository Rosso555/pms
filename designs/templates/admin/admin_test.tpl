{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq edit}class="active"{/if}>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</li>
  {if $smarty.get.action eq edit}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
{if $smarty.cookies.checkTest}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot delete "<strong>{$smarty.cookies.checkTest}</strong>" because it has been used.
  </div>
{/if}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form" role="form" action="{$admin_file}?task=test" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="test">
          <div class="col-md-3">
            <div class="form-group">
              <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_test}{$multiLang.button_add_test}{else}No Translate(Key Lang: button_add_test){/if}
              </button>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <select class="form-control select2" name="catid" style="width:100%">
                <option value="">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} {if $multiLang.text_category}{$multiLang.text_category}{else}No Translate(Key Lang: text_category){/if} ---</option>
                {foreach from=$category item=data}
                <option value="{$data.id}" {if $smarty.get.catid eq $data.id}selected{/if}>{$data.name}</option>
                {/foreach}
              </select>
            </div>
          </div>
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
            <div class="input-group" style="float: right;">
              <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="{if $multiLang.text_search_by}{$multiLang.text_search_by}{else}No Translate(Key Lang: text_search_by){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}...">
              <span class="input-group-btn">
                <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate(Key Lang: button_search){/if}</button>
              </span>
            </div>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getTestByID.id} in {/if}">
          {if $getTestByID.id}
          <form action="{$admin_file}?task=test&amp;action=edit&amp;catid={$smarty.get.catid}&amp;id={$getTestByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=test&amp;catid={$smarty.get.catid}" method="post">
          {/if}
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}:</label>
                  {if $error.title}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</span>
                  {/if}
                  <input type="text" name="title" class="form-control" placeholder="Title"
                  value="{if $smarty.session.test.title}{$smarty.session.test.title}{elseif $getTestByID.title}{$getTestByID.title}{/if}">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_category}{$multiLang.text_category}{else}No Translate(Key Lang: text_category){/if}:</label>
                  {if $error.category}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_category}{$multiLang.text_category}{else}No Translate(Key Lang: text_category){/if}</span>
                  {/if}
                  <select class="form-control select2" name="category" style="width:100%">
                    <option value="">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} {if $multiLang.text_category}{$multiLang.text_category}{else}No Translate(Key Lang: text_category){/if} ---</option>
                    {foreach from=$category item=data}
                    <option value="{$data.id}" {if $smarty.session.test.category}{if $smarty.session.test.category eq $data.id}selected{/if}{else}{if $getTestByID.category_id eq $data.id}selected{/if}{/if}>{$data.name}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="title"> {if $multiLang.text_graphic}{$multiLang.text_graphic}{else}No Translate(Key Lang: text_graphic){/if}:</label>
                  <input type="text" name="graphic_title" class="form-control" placeholder="Graphic Title"
                  value="{if $smarty.session.test.graphic_title}{$smarty.session.test.graphic_title}{elseif $getTestByID.graph_title}{$getTestByID.graph_title}{/if}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label> {if $multiLang.text_description}{$multiLang.text_description}{else}No Translate(Key Lang: text_description){/if}:</label>
                  <textarea class="form-control" name="description" rows="5" placeholder="Write something" style="overflow:auto; resize:none;">{if $smarty.session.test.description}{$smarty.session.test.description}{elseif $getTestByID.description}{$getTestByID.description}{/if}</textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getTestByID.id}
                    <input type="hidden" name="id" value="{$getTestByID.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=test" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
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
            <th width="230">{if $multiLang.text_category}{$multiLang.text_category}{else}No Translate(Key Lang: text_category){/if}</th>
            <th width="230">{if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</th>
            <th width="230">{if $multiLang.text_graphic}{$multiLang.text_graphic}{else}No Translate(Key Lang: text_graphic){/if}</th>
            <th width="550">{if $multiLang.text_description}{$multiLang.text_description}{else}No Translate(Key Lang: text_description){/if}</th>
            <th width="100">{if $multiLang.text_status}{$multiLang.text_status}{else}No Translate(Key Lang: text_status){/if}</th>
            <th width="400">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listTest|@count gt 0}
        <tbody>
        {foreach from = $listTest item = data key=k}
          <tr>
            <td><a href="{$admin_file}?task=test&amp;catid={$data.category_id}">{$data.category_name}</a></td>
            <td>{$data.title}</td>
            <td>{$data.graph_title}</td>
            <td>{$data.description}</td>
            <td>
            {if $data.status eq 1}
            <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#status_{$data.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.text_click_change_status}{$multiLang.text_click_change_status}{else}No Translate(Key Lang: text_click_change_status){/if}">
              <i class="fa fa-ban"></i> {if $multiLang.text_unpublish}{$multiLang.text_unpublish}{else}No Translate(Key Lang: text_unpublish){/if}
            </button>
            {else}
            <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#status_{$data.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.text_click_change_status}{$multiLang.text_click_change_status}{else}No Translate(Key Lang: text_click_change_status){/if}">
              <i class="fa fa-check-circle"></i> {if $multiLang.text_publish}{$multiLang.text_publish}{else}No Translate(Key Lang: text_publish){/if}
            </button>
            {/if}
              <!-- Modal -->
              <div class="modal fade" id="status_{$data.id}" role="dialog">
                <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="panel panel-primary modal-content">
                    <div class="panel-heading modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate(Key Lang: text_confirmation){/if}</h4>
                    </div>
                    <div class="modal-body">
                      <p>
                        {if $multiLang.text_change_status}{$multiLang.text_change_status}{else}No Translate(Key Lang: text_change_status){/if}
                        <b>
                        {if $data.status eq 1}
                          {if $multiLang.text_publish}{$multiLang.text_publish}{else}No Translate(Key Lang: text_publish){/if}
                        {else}
                          {if $multiLang.text_unpublish}{$multiLang.text_unpublish}{else}No Translate(Key Lang: text_unpublish){/if}
                        {/if} </b>?
                      </p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=test&amp;action=change_status&amp;catid={$smarty.get.catid}&amp;id={$data.id|escape}&amp;status={$data.status|escape}" class="btn btn-danger btn-md" style="color: white;">
                        <i class="fa fa-check-circle-o"></i> {if $multiLang.button_yes}{$multiLang.button_yes}{else}No Translate(Key Lang: button_yes){/if}
                      </a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_discard}{$multiLang.button_discard}{else}No Translate(Key Lang: button_discard){/if}</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
            </td>
            <td>
              <a href="{$admin_file}?task=test&amp;action=edit&amp;catid={$smarty.get.catid}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}" style="margin-bottom: 3px;"><i class="fa fa-edit"></i></a>
              <!-- Trigger the modal with a button -->
              <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$data.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}" style="margin-bottom: 3px;"><i class="fa fa-trash-o"></i></button>
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
                        {if $multiLang.text_delete_test}{$multiLang.text_delete_test}{else}No Translate(Key Lang: text_delete_test){/if}
                         <b>({$data.title|escape})</b>.</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=test&amp;action=delete&amp;catid={$smarty.get.catid}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"></i> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
              <!-- <a href="{$admin_file}?task=result&amp;tid={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_result}{$multiLang.button_add_result}{else}No Translate(Key Lang: button_add_result){/if}" style="margin-bottom: 3px;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <a href="{$admin_file}?task=test_topic&amp;tid={$data.id}" class="btn btn-info btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_test_topic}{$multiLang.button_add_test_topic}{else}No Translate (Key Lang: button_add_test_topic){/if}" style="margin-bottom: 3px;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <a href="{$admin_file}?task=test_topic_analysis&amp;tid={$data.id}" class="btn btn-warning btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_test_analysis_topic_less_big}{$multiLang.button_add_test_analysis_topic_less_big}{else}No Translate(Key Lang: button_add_test_analysis_topic_less_big){/if}" style="margin-bottom: 3px;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <a href="{$admin_file}?task=test_topic_answer&amp;tid={$data.id}" class="btn btn-primary btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_test_topic_answer_calculate}{$multiLang.button_add_test_topic_answer_calculate}{else}No Translate(Key Lang: button_add_test_topic_answer_calculate){/if}" style="margin-bottom: 3px;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <a href="{$admin_file}?task=group_answer_question&amp;tid={$data.id}" class="btn btn-warning btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_group_answer_question}{$multiLang.button_add_group_answer_question}{else}No Translate(Key Lang: button_add_group_answer_question){/if}" style="margin-bottom: 3px;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <a href="{$admin_file}?task=response&amp;tid={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_respone_result}{$multiLang.button_respone_result}{else}No Translate (Key Lang: button_respone_result){/if}" style="margin-bottom: 3px;"><i class="fa fa-folder-open" aria-hidden="true"></i></a> -->

              <a href="{$admin_file}?task=result&amp;tid={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_result}{$multiLang.button_add_result}{else}No Translate(Key Lang: button_add_result){/if}" style="margin-bottom: 3px;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <a href="{$admin_file}?task=new_result&amp;tid={$data.id}" class="btn btn-warning btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_new_result}{$multiLang.button_add_new_result}{else}No Translate(Key Lang: button_add_new_result){/if}" style="margin-bottom: 3px;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <a href="{$admin_file}?task=test_topic&amp;tid={$data.id}" class="btn btn-info btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_test_topic}{$multiLang.button_add_test_topic}{else}No Translate (Key Lang: button_add_test_topic){/if}" style="margin-bottom: 3px;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <a href="{$admin_file}?task=test_topic_analysis&amp;tid={$data.id}" class="btn btn-warning btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_test_analysis_topic_less_big}{$multiLang.button_add_test_analysis_topic_less_big}{else}No Translate(Key Lang: button_add_test_analysis_topic_less_big){/if}" style="margin-bottom: 3px;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <a href="{$admin_file}?task=test_topic_answer&amp;tid={$data.id}" class="btn btn-primary btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_test_topic_answer_calculate}{$multiLang.button_add_test_topic_answer_calculate}{else}No Translate(Key Lang: button_add_test_topic_answer_calculate){/if}" style="margin-bottom: 3px;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <a href="{$admin_file}?task=group_answer_question&amp;tid={$data.id}" class="btn btn-warning btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_group_answer_question}{$multiLang.button_add_group_answer_question}{else}No Translate(Key Lang: button_add_group_answer_question){/if}" style="margin-bottom: 3px;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <a href="{$admin_file}?task=test_question_view_order&amp;tid={$data.id}" class="btn btn-primary btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_test_question_view_order}{$multiLang.button_add_test_question_view_order}{else}No Translate(Key Lang: button_add_test_question_view_order){/if}" style="margin-bottom: 3px;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
              <a href="{$admin_file}?task=response&amp;tid={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_respone_result}{$multiLang.button_respone_result}{else}No Translate (Key Lang: button_respone_result){/if}" style="margin-bottom: 3px;"><i class="fa fa-folder-open" aria-hidden="true"></i></a>

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
      {if $smarty.get.catid || $smarty.get.tid}<a href="{$admin_file}?task=test" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate (Key Lang: text_back){/if}</a>{/if}
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
