{extends file="common/layout.tpl"}
{block name="main"}
<div class="panel panel-primary">
  <div class="panel-heading"><h3 class="panel-title">Welcome!</h3></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}" method="GET" style="padding: 1px 0px 12px 1px;">
          <div class="form-group select2_search_inline" style="margin-bottom:5px;">
            <select class="form-control select2_search" name="cid">
              <option value="">---Select Category---</option>
              {foreach from=$category item=v}
              <option value="{$v.id}" {if $smarty.get.cid eq $v.id}selected{/if}>{$v.name}</option>
              {/foreach}
            </select>
          </div>
          <div class="form-group select2_search_inline" style="margin-bottom:5px;">
            <select class="form-control select2_search" name="tid">
              <option value="">---Select Test---</option>
              {foreach from=$test item=v}
              <option value="{$v.id}" {if $smarty.get.tid eq $v.id}selected{/if}>{$v.title}</option>
              {/foreach}
            </select>
          </div>
          <div class="form-group select2_search_inline" style="margin-bottom:5px;">
            <select class="form-control select2_search" name="status">
              <option value="">--- Select Status ---</option>
              <option value="1" {if $smarty.get.status eq 1}selected{/if}>New & Pendding...</option>
              <option value="2" {if $smarty.get.status eq 2}selected{/if}>Completed</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:5px;">
            <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
          </div>
        </form>
      </div><!--panel panel-body-->
    </div><!--panel panel-default-->

    {if $listTestPsychologist|@count gt 0}
    <div class="row">
      {foreach from=$listTestPsychologist item=data key=k}
      <div class="col-md-6 col-sm-12">
        <div class="body-test">
          <div class="body-test-body">
            <h3 class="margin-clear"> {$data.title|escape}</h3>
            <p class="small-90">
              <i class="fa fa-tag" aria-hidden="true"></i> {$data.catName|escape} &nbsp;/&nbsp;
              <i class="fa fa-calendar" aria-hidden="true"></i> {$data.created_at|date_format:"%B %e, %Y"} &nbsp;/&nbsp;
              <i class="fa fa-user-plus" aria-hidden="true"></i> <a href="{$admin_file}?psy_id={$data.psychologist_id}">{$data.first_name} {$data.last_name}</a> &nbsp;/&nbsp;
              {if $data.analysis_file}
                <span class="label label-success"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Analysis File (Completed)</span>
              {else}
                <span class="label label-warning"><i class="fa fa-ban" aria-hidden="true"></i> Analysis File (Uncompleted)</span>
              {/if}
            </p>
            <p class="small">{$data.description|truncate:350:"...":true|escape}</p>
          </div>
          <div class="body-test-footer">
            <a id="btn-test-footer" href="{$admin_file}?task=result_test_psychologist&amp;tid={$data.test_id}&amp;psy_id={$data.psychologist_id}&amp;id={$data.id}" type="button" class="btn btn-default btn-sm btn-block">
              Check Result &nbsp;&nbsp;<i class="fa fa-registered" aria-hidden="true"></i>
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

    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
