{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTest" href="{$admin_file}?task=test">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</a></li>
  <li class="active">{if $multiLang.text_response_result}{$multiLang.text_response_result}{else}No Translate(Key Lang: text_response_result){/if}</li>
</ul>
<div class="alert alert-warning" style="display: none;" id="txtSuccess">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <p style="color:red;">Download processing...! After finish will send to your email. <a class="btn btn-success btn-xs" href="{$admin_file}?task=download_list" >Click Here</a></p>
</div>
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_response_result}{$multiLang.text_response_result}{else}No Translate(Key Lang: text_response_result){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <div class="row">
        <div class="col-md-10">
          <b>{if $multiLang.text_test_title}{$multiLang.text_test_title}{else}No Translate(Key Lang: text_test_title){/if}:</b> {$test.title}
        </div>
        <div class="col-md-2">
          <button type="button" class="btn btn-primary pull-right" onclick="export_csv();">{if $multiLang.text_export_csv}{$multiLang.text_export_csv}{else}No Translate(Key Lang: text_export_csv){/if}</button>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
          <th>{if $multiLang.text_date}{$multiLang.text_date}{else}No Translate(Key Lang: text_date){/if}</th>
          <th>{if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}</th>
          <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listResponse|@count gt 0}
        <tbody>
        {foreach from = $listResponse item = data key=k}
          <tr>
            <td>{$data.created_at}</td>
            <td>
              <table>
              {foreach from=$data.topic_result item=v}
                  <tr>
                    <td>{$v.topic_title} </td>
                    <td>&nbsp; : &nbsp; </td>
                    <td> {$v.amount}</td>
                  </tr>
              {/foreach}
              </table>
            </td>
            <td>
              <a href="{$admin_file}?task=response_answer&amp;tid={$smarty.get.tid}&amp;rid={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_response_answer}{$multiLang.button_response_answer}{else}No Translate(Key Lang: button_response_answer){/if}"><i class="fa fa-eye" aria-hidden="true"></i></a>
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
      <a id="btnBack" href="javascript:history.back()" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
{block name="javascript"}
<script type="text/javascript">
  //Get previous url
  var urlBack =  document.referrer;
  var url = '';
  if(urlBack !== '') url = getUrlPrevious(urlBack);
  if(url.task === 'test') localStorage.setItem('urlTest',urlBack);
  //Get session url
  var getUrlBack = localStorage.getItem('urlTest');
  if(getUrlBack !== null){
    $("#btnBack").attr("href", getUrlBack);
    $("#bCrumbTest").attr("href", getUrlBack);
  }
  //End previous url

  function export_csv(){
    //Show Loading gif
    $(".loader").show();
    $.ajax({
      type: "POST",
      url: "{$admin_file}?task=response&action=export&tid={$smarty.get.tid}",
      data: { "tid":  {$smarty.get.tid} },
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
