{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>Patient</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate (Key Lang:text_edit){/if}</li>
  {/if}
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h3 class="panel-title">Patient.</h3></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-12">
            <form class="form-inline" action="{$admin_file}?task=patient" method="get">
              <input type="hidden" name="task" value="psychologist_activity">
              <div class="form-group select2_search_inline" style="margin-bottom:5px;">
                <select class="form-control select2_search" name="psy_id" style="width:100%;">
                  <option value="">---Select Psychologist---</option>
                  {foreach from=$listPsychologist item=v}
                  <option value="{$v.id}" {if $smarty.get.psy_id eq $v.id}selected{/if}>{$v.username}</option>
                  {/foreach}
                </select>
              </div>
              <div class="form-group select2_search_inline" style="margin-bottom:5px;">
                <select class="form-control select2_search" name="gender" style="width:100%;">
                  <option value="">---Select Gender---</option>
                  <option value="1" {if $smarty.get.gender eq 1}selected{/if}>Male</option>
                  <option value="2" {if $smarty.get.gender eq 2}selected{/if}>Female</option>
                </select>
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <input type="text" class="form-control" placeholder="Keyword..." name="kwd">
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
              </div>
            </form>
          </div>
        </div>
      </div><!--End panel-heading-->
    </div><!--End panel panel-primary-->
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
            <th>Psychologist</th>
            <th>Content</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
        {if $resultPsyActivity|@count gt 0}
          {foreach from=$resultPsyActivity item=v}
          <tr>
            <td>{$v.username}</td>
            <td>{$v.content}</td>
            <td>{$v.activity_date}</td>
          </tr>
          {/foreach}
        {else}
          <tr>
            <td colspan="3"><h4 style="text-align:center">There is no record</h4></td>
          </tr>
        {/if}
        </tbody>
      </table>
      {include file="common/paginate.tpl"}
    </div><!--table-responsive  -->
  </div><!--End panel-heading-->
</div><!--End panel panel-primary-->

{/block}
