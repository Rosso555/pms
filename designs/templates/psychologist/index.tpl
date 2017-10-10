{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$psychologist_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li class="active">{if $multiLang.text_home}{$multiLang.text_home}{else}No Translate(Key Lang: text_home){/if}</li>
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">Test Patient Not Complete</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$psychologist_file}" method="GET" style="padding: 1px 0px 12px 1px;">
          <div class="form-group select2_search_inline" style="margin-bottom:5px;">
            <select class="form-control select2_search" name="pat_id" style="width:100%;">
              <option value="">---Select {if $multiLang.text_patient}{$multiLang.text_patient}{else}No Translate(Key Lang: text_patient){/if}---</option>
              {foreach from=$patient item=v}
              <option value="{$v.id}" {if $smarty.get.pat_id eq $v.id}selected{/if}>{$v.username}</option>
              {/foreach}
            </select>
          </div>
          <div class="form-group select2_search_inline" style="margin-bottom:5px;">
            <select class="form-control select2_search" name="tid" style="width:100%;">
              <option value="">---Select Test---</option>
              {foreach from=$test item=v}
              <option value="{$v.id}" {if $smarty.get.tid eq $v.id}selected{/if}>{$v.title}</option>
              {/foreach}
            </select>
          </div>
          <div class="form-group" style="margin-bottom:5px;">
            <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
          </div>
        </form>
      </div>
    </div><!--panel panel-body-->
    {if $testPatient|@count gt 0}
    <div class="row">
      {foreach from=$testPatient item=data key=k}
      <div class="col-md-6 col-sm-12">
        <div class="body-test">
          <h3 class="margin-clear"> {$data.title|escape}</h3>
          <p class="small-90">
            <i class="fa fa-tag" aria-hidden="true"></i> {$data.catName|escape} &nbsp;/&nbsp;
            <i class="fa fa-calendar" aria-hidden="true"></i> {$data.created_at|date_format:"%B %e, %Y"} &nbsp;/&nbsp;
            <i class="fa fa-user" aria-hidden="true"></i> <a href="{$psychologist_file}?pat_id={$data.patient_id}">{$data.username}</a> &nbsp;/&nbsp;
            {if $data.test_tmp_status}
            <span class="label label-warning"><i class="fa fa-ban" aria-hidden="true"></i> Not Completed</span>
            {else}
            <span class="label label-info"><i class="fa fa-file-text" aria-hidden="true"></i> New Assign</span>
            {/if}
            <!-- <span class="label label-warning"><i class="fa fa-ban" aria-hidden="true"></i> Not Completed</span> -->
          </p>
          <p class="small">{$data.description|truncate:350:"...":true|escape}</p>
          <div class="body-test-footer">
            <a href="{$psychologist_file}?task=test_question_patient&amp;tid={$data.test_id}&amp;pat_id={$data.patient_id}&amp;id={$data.id}" type="button" class="btn btn-default btn-sm btn-block">
              {if $data.test_tmp_status}Continue Test{else}Start Test{/if} &nbsp;&nbsp;<i class="fa fa-chevron-circle-right" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>
      {/foreach}
    </div>
    {else}
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="body-test text-center">
          <h4>{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h4>
        </div>
      </div>
    </div>
    {/if}
    <br>

    <!-- <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
            <th>{if $multiLang.text_patient}{$multiLang.text_patient}{else}No Translate(Key Lang: text_patient){/if}</th>
            <th>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</th>
            <th>{if $multiLang.text_status}{$multiLang.text_status}{else}No Translate(Key Lang: text_status){/if}</th>
            <th width="100">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $testPatient|@count gt 0}
        <tbody>
        {foreach from = $testPatient item = data key=k}
          <tr>
            <td>{$data.username}</td>
            <td>{$data.title}</td>
            <td>
              {if $data.status eq 1}
              <button type="button" class="btn btn-danger btn-xs">
                <i class="fa fa-stop-circle-o" aria-hidden="true"></i> Pendding...
              </button>
              {else}
              <button type="button" class="btn btn-success btn-xs">
                <i class="fa fa-check-circle"></i> Completed
              </button>
              {/if}
            </td>
            <td>
              <a href="{$psychologist_file}?task=test_question&amp;tid={$data.test_id}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top"
              title="{if $multiLang.button_view}{$multiLang.button_view}{else}No Translate(Key Lang: button_view){/if}"><i class="fa fa-eye"></i></a>
            </td>
          </tr>
        {/foreach}
        </tbody>
        {else}
        <tr>
          <td colspan="4"><h4 style="text-align:center">{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h4></td>
        </tr>
        {/if}
      </table>
    </div> -->
    <!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
