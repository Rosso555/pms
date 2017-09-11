{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$patient_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li class="active">{if $multiLang.text_home}{$multiLang.text_home}{else}No Translate(Key Lang: text_home){/if}</li>
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h3 class="panel-title">Welcome! {$smarty.session.is_patient_username}</h3></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$psychologist_file}" method="GET">
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
          <p><i class="fa fa-tag" aria-hidden="true"></i> {$data.catName|escape}</p>
          <p class="small">{$data.description|truncate:350:"...":true|escape}</p>
          <div class="body-test-footer">
            <a href="{$patient_file}?task=test_question&amp;tid={$data.test_id}&amp;id={$data.id}" type="button" class="btn btn-default btn-sm btn-block">Start Test &nbsp;&nbsp;<i class="fa fa-chevron-circle-right" aria-hidden="true"></i></a>
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
  </div><!--panel-body-->
</div><!--panel-primary-->
{/block}
