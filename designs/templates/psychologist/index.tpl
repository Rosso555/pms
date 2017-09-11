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
    <div class="table-responsive">
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
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
