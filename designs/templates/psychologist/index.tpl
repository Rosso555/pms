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
            <select class="form-control select2_search" name="stus" style="width:100%;">
              <option value="">---Select Status---</option>
              <!-- assign value 3 is new assign test to patient -->
              <option value="3" {if $smarty.get.stus eq 3}selected{/if}>New</option>
              <option value="1" {if $smarty.get.stus eq 1}selected{/if}>Not Completed</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:5px;">
						<div class="input-group">
							<input id="f_date" class="form-control" type="text" placeholder="From Date" name="f_date" value="{$smarty.get.f_date}">
              <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
						</div>
					</div>
          <div class="form-group" style="margin-bottom:5px;">
						<div class="input-group">
							<input id="t_date" class="form-control" type="text" placeholder="To Date" name="t_date" value="{$smarty.get.t_date}">
              <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
						</div>
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
            {if $data.test_tmp_status}
            <span class="label label-warning"><i class="fa fa-ban" aria-hidden="true"></i> Not Completed</span>
            {else}
            <span class="label label-info"><i class="fa fa-file-text" aria-hidden="true"></i> New</span>
            {/if}
          </p>
          <p class="small">{$data.description|truncate:350:"...":true|escape}</p>
          <div class="body-test-footer">
            <a href="{$patient_file}?task=test_question&amp;tid={$data.test_id}&amp;id={$data.id}" type="button" class="btn btn-default btn-sm btn-block">
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
    {include file="common/paginate.tpl"}
  </div><!--panel-body-->
</div><!--panel-primary-->
{/block}

{block name="javascript"}
<script>
  $(document).ready(function(){
    $('#f_date').datetimepicker({ locale: 'en', format: 'YYYY-MM-DD'});
    $('#t_date').datetimepicker({ locale: 'en', format: 'YYYY-MM-DD'});
  });
</script>
{/block}
