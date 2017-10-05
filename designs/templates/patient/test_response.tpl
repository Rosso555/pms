{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$patient_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li>{if $multiLang.text_home}{$multiLang.text_home}{else}No Translate(Key Lang: text_home){/if}</li>
  <li class="active">Test Response</li>
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h3 class="panel-title">Test Response</h3></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
              <form class="form-inline" role="form" action="{$patient_file}?task=test_response" method="GET">
                <input type="hidden" name="task" value="test_response">
                <div class="form-group select2_search_inline" style="margin-bottom:5px;">
                  <select class="form-control select2_search" name="category" style="width:100%;">
                    <option value="">---Select Category---</option>
                  </select>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
		   <input type="text" class="form-control" placeholder="Search title" name="kwd">
		</div>
                <div class="form-group" style="margin-bottom:5px;">
                  <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
                </div>
              </form>
      </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                    <tr bgcolor="#eeeee">
                        <th style="width:10%">Title</th>
                        <th>Category</th>
                        <th>Discription</th>
                        <th>Action</th>
                    </tr>
            </thead>
            <tbody>
                    {if $response_result|@count gt 0}
                    {foreach from=$response_result item=value}
                    <tr>
                            <td>{$value.title}</td>
                            <td>{$value.cate_name}</td>
                            <td>{$value.description}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-xs" data-toggle1="tooltip" title="Delete Test"><i class="fa fa-trash-o"></i></button>
                            </td>
                    </tr>
                    {/foreach}
                    {else}
                    <tr>
                      <td colspan="4"><h4 style="text-align:center">There is no record</h4></td>
                    </tr>
                    {/if}
            </tbody>
        </table>
   </div>
  </div>
</div>
{/block}
