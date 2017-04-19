{extends file="admin/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a href="{$admin_file}?task=test">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_test_topic_view_order}{$multiLang.text_test_topic_view_order}{else}No Translate(Key Lang: text_test_topic_view_order){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title"> {if $multiLang.text_test_topic_view_order}{$multiLang.text_test_topic_view_order}{else}No Translate(Key Lang: text_test_topic_view_order){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}:</b> {$test.title}{if $checkViewOrderTopic gt 0}ddd{/if}
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="form-group">
          {if $listTestTopic|@count gt 0}
            <a id="topic_hide" href="{$admin_file}?task=topic_hide&amp;tid={$smarty.get.tid}" class="btn btn-primary" {if $checkViewOrderTopic neq 0}style="display: none;"{/if}>
              {if $multiLang.text_hide_show_topic}{$multiLang.text_hide_show_topic}{else}No Translate(Key Lang: text_hide_show_topic){/if}
            </a>

            <button id="test_topic" type="button" class="btn btn-primary" data-container="body" data-toggle="popover" data-placement="right" data-content="Sorry! You can't add show/hide topic. Please input topic view order all field." {if $checkViewOrderTopic eq 0}style="display: none;"{/if}>
              {if $multiLang.text_hide_show_topic}{$multiLang.text_hide_show_topic}{else}No Translate(Key Lang: text_hide_show_topic){/if}
            </button>
          {else}
            <button id="test_topic" type="button" class="btn btn-primary" data-container="body" data-toggle="popover" data-placement="right" data-content="Sorry! You can't add show/hide topic. Please input topic view order all field.">
              {if $multiLang.text_hide_show_topic}{$multiLang.text_hide_show_topic}{else}No Translate(Key Lang: text_hide_show_topic){/if}
            </button>
          {/if}

        </div>
      </div>
    </div><!--panel panel-body-->
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
          <th>{if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}</th>
          <th width="170">{if $multiLang.text_view_Order}{$multiLang.text_view_Order}{else}No Translate(Key Lang: text_view_Order){/if}</th>
          </tr>
        </thead>
        {if $listTestTopic|@count gt 0}
        <tbody>
        {foreach from=$listTestTopic item=data key=k}
          <tr>
            <td>{$data.name}</td>
            <td>
              <div class="input-group">
                <span class="input-group-addon" style="color:red;"><i id="refresh{$data.topic_id}" class="fa fa-refresh" aria-hidden="true"></i></span>
                <input type="text" id="view_order{$data.topic_id}" class="form-control" value="{$data.view_order}" placeholder="Example: 123..." onchange="updateTestTopic({$data.test_id}, {$data.topic_id});" onkeyup="NumAndTwoDecimals(event , this);">
              </div>
            </td>
          </tr>
        {/foreach}
        </tbody>
        {else}
        <tr>
          <td colspan="2"><h4 style="text-align:center">{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h4></td>
        </tr>
        {/if}
      </table>
      <a href="{$admin_file}?task=test" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}

{block name="javascript"}
<script>

function updateTestTopic(testid, topicid)
{
  var view_order = $("#view_order"+topicid).val();

  $("#refresh"+topicid).attr("class", "fa fa-refresh fa-spin fa-fw");

  if(view_order !== ''){

    var paramdata = { testid : testid, topicid : topicid, view_order : view_order };

    $.ajax({
      type: "POST",
      url: "{$admin_file}?task=test_topic&action=update_test_topic",
      dataType:'json',
      data: paramdata,
      success: function(data){
        if(data.status == true)
        {
          $("#refresh"+topicid).attr("class", "fa fa-refresh");
        }
        if(data.checkCountView == 0){
          $("#topic_hide").show();
          $("#test_topic").hide();
          $('#test_topic').popover('hide');
        }
      },
      error: function(){
       //Show error here
       alert("Cannot show detail. Please try again later.");
      }
    });//End Ajax
  }

}

</script>
{/block}
