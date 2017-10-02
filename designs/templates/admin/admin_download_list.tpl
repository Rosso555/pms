{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li class="active">{if $multiLang.text_download_list}{$multiLang.text_download_list}{else}No Translate(Key Lang: text_download_list){/if}</li>
</ul>
<div class="alert alert-warning" style="display: none;" id="txtSuccess">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <p style="color:red;">Download processing...! After finish will send to your email.</p>
</div>
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_download_list}{$multiLang.text_download_list}{else}No Translate(Key Lang: text_download_list){/if}</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=download_list" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="download_list">
            <div class="form-group select2_search_inline">
              <select class="form-control select2_search" name="tid" style="width:100%">
                <option value="">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if} ---</option>
                {foreach from=$test item=data}
                <option value="{$data.id}" {if $smarty.get.tid eq $data.id}selected{/if}>{$data.title}</option>
                {/foreach}
              </select>
            </div>
            <div class="form-group select2_search_inline">
              <select class="form-control select2_search" name="is_email" style="width:100%">
                <option value="">--- For Email ---</option>
                <option value="1" {if $smarty.get.is_email eq 1}selected{/if}>No</option>
                <option value="2" {if $smarty.get.is_email eq 2}selected{/if}>Yes</option>
              </select>
            </div>
            <div class="form-group select2_search_inline" style="margin-botton:5px;">
              <select class="form-control select2_search" name="status" style="width:100%">
                <option value="">--- Status ---</option>
                <option value="1" {if $smarty.get.status eq 1}selected{/if}>Pending</option>
                <option value="2" {if $smarty.get.status eq 2}selected{/if}>Completed</option>
              </select>
            </div>
            <div class="form-group" style="margin-botton:5px;">
              <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate(Key Lang: button_search){/if}</button>
              <button type="button" class="btn btn-primary" onclick="export_csv();">{if $multiLang.text_export_all_email}{$multiLang.text_export_all_email}{else}No Translate(Key Lang: text_export_all_email){/if}</button>
            </div>
        </form>
      </div><!--panel panel-body-->
    </div><!--panel panel-default-->

    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
          <th>{if $multiLang.text_test_title}{$multiLang.text_test_title}{else}No Translate(Key Lang: text_test_title){/if}</th>
          <th>{if $multiLang.text_status}{$multiLang.text_status}{else}No Translate(Key Lang: text_status){/if}</th>
          <th>{if $multiLang.text_date}{$multiLang.text_date}{else}No Translate(Key Lang: text_date){/if}</th>
          <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listDownload|@count gt 0}
        <tbody>
        {foreach from=$listDownload item=data key=k}
          <tr>
            <td>{$data.title} {if $data.is_email eq 2}<span class="label label-primary"><i class="fa fa-envelope-o" aria-hidden="true"></i> For Email</span>{/if}</td>
            <td>
              {if $data.status eq 1}
              <span class="label label-warning"><i class="fa fa-ban" aria-hidden="true"></i> Pending..</span>
              {else}
              <span class="label label-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Completed</span>
              {/if}
            </td>
            <td>{$data.created_at}</td>
            <td>
              {if $data.status eq 1}
              <button type="button" name="button" class="btn btn-danger btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_can_not_download}{$multiLang.button_can_not_download}{else}No Translate(Key Lang: button_can_not_download){/if}"><i class="fa fa-ban" aria-hidden="true"></i></button>
              {else}
              <a href="{$site_url}/file_csv/{$data.file_name}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_download}{$multiLang.button_download}{else}No Translate(Key Lang: button_download){/if}"><i class="fa fa-download" aria-hidden="true"></i></a>
              {/if}
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
      <!-- Modal -->
      <div class="modal fade" id="viewTopicSum" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="panel panel-primary modal-content">
            <div class="panel-heading modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="panel-title modal-title">{if $multiLang.text_topic_sum_detail}{$multiLang.text_topic_sum_detail}{else}No Translate(Key Lang: text_topic_sum_detail){/if}</h4>
            </div>
            <div class="modal-body">
              <table class="table">
                <thead class="theader">
                  <tr>
                    <th class="text-left">{if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</th>
                    <th class="text-left" width="150">{if $multiLang.text_value_sum}{$multiLang.text_value_sum}{else}No Translate(Key Lang: text_value_sum){/if}</th>
                  </tr>
                </thead>
                <tbody id="dataDetail">

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal -->

    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}

{block name="javascript"}
<script>

//End previous url
function export_csv(){
  //Show Loading gif
  $(".loader").show();
  $.ajax({
    type: "POST",
    url: "{$admin_file}?task=download_list&action=export_all_email",
    data: { "status":  1 },
    success: function(data){
      // alert(data);
      if(data){
        $('#txtSuccess').attr("style", "display: inherit;");
      }
      //Show Loading gif
      $(".loader").hide();
    },
    error: function(){
     //Show error here
     alert("Cannot show detail. Please try again later.");
     location.reload();
    }
  });//End Ajax
}

</script>
{/block}
