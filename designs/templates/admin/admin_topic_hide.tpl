{extends file="admin/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTest" href="{$admin_file}?task=test">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</a></li>
  <li><a href="{$admin_file}?task=test_topic&amp;tid={$smarty.get.tid}">{if $multiLang.text_test_topic_view_order}{$multiLang.text_test_topic_view_order}{else}No Translate(Key Lang: text_test_topic_view_order){/if}</a></li>
  <li {if $smarty.get.action neq edit}class="active"{/if}>{if $multiLang.text_topic_hide}{$multiLang.text_topic_hide}{else}No Translate(Key Lang: text_topic_hide){/if}</li>
  {if $smarty.get.action eq edit}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
  <div class="panel panel-primary">
    <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_topic_hide}{$multiLang.text_topic_hide}{else}No Translate(Key Lang: text_topic_hide){/if}</h4></div>
    <div class="panel-body">
      <div class="box_title">
        <b>{if $multiLang.text_test_title}{$multiLang.text_test_title}{else}No Translate(Key Lang: text_test_title){/if}:</b> {$getTest.title}
      </div>
      <div class="panel panel-default">
        <div class="panel-body">
          <form class="form-inline" role="form" action="{$admin_file}?task=topic_hide" method="GET" style="padding: 1px 0px 12px 1px;">
            <input type="hidden" name="task" value="topic_hide">
            <div class="form-group">
              <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_hide_topic}{$multiLang.button_add_hide_topic}{else}No Translate(Key Lang: button_add_hide_topic){/if}
              </button>
            </div>
          </form>
          <div id="demo" class="collapse {if $error or $getTopicQHideByID.id}in{/if}">
            {if $getTopicQHideByID.id}
            <form action="{$admin_file}?task=topic_hide&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$getTopicQHideByID.id}" method="post">
            {else}
            <form action="{$admin_file}?task=topic_hide&amp;tid={$smarty.get.tid}" method="post">
            {/if}
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="title"><span style="color: red">*</span> {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}:</label>
                    {if $error.topic_first}
                      <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}</span>
                    {/if}
                    <select class="form-control" name="topic_first" style="width:100%" onchange="getTopicHide(this);">
                      <option value="">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} ---</option>
                      {foreach from=$listTestTopic item=data}
                      <option value="{$data.topic_id}" {if $getTopicQHideByID.topic_id}{if $getTopicQHideByID.topic_id eq $data.topic_id}selected{/if}{else}{if $data.topic_hide_id}disabled{/if} {/if}>{$data.name}</option>
                      {/foreach}
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="title"><span style="color: red">*</span> Show {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}:</label>
                    {if $error.topic_second}
                      <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}</span>
                    {/if}
                    <select class="form-control" name="topic_second" style="width:100%" id="topic_second">
                      <option value="">--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} Show {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} ---</option>
                      {foreach from=$listTestTopic item=data name=foo}
                        <option value="{$data.topic_id}" {if $getTopicQHideByID.if_topic_id eq $data.topic_id}selected{else}{if $data.topic_hide_id}disabled{/if}{/if}>{$data.name}</option>
                      {/foreach}
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="less_than"><span style="color: red">*</span> {if $multiLang.text_less_than_value}{$multiLang.text_less_than_value}{else}No Translate(Key Lang: text_less_than_value){/if} :</label>
                    {if $error.less_than}
                      <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_less_than_value}{$multiLang.text_less_than_value}{else}No Translate(Key Lang: text_less_than_value){/if}</span>
                    {/if}
                    <input type="text" name="less_than" class="form-control" id="less_than" value="{if $getTopicQHideByID.less_than}{$getTopicQHideByID.less_than}{else}{$smarty.session.topic_hide.less_than}{/if}" placeholder="Example: 123..." onkeyup="NumAndTwoDecimals(event , this);">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="bigger_than"><span style="color: red">*</span> {if $multiLang.text_biger_than_value}{$multiLang.text_biger_than_value}{else}No Translate(Key Lang: text_biger_than_value){/if}:</label>
                    {if $error.bigger_than}
                      <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_biger_than_value}{$multiLang.text_biger_than_value}{else}No Translate(Key Lang: text_biger_than_value){/if}</span>
                    {/if}
                    <input type="text" name="bigger_than" class="form-control" id="bigger_than" value="{if $getTopicQHideByID.bigger_than}{$getTopicQHideByID.bigger_than}{else}{$smarty.session.topic_hide.bigger_than}{/if}" placeholder="Example: 123..." onkeyup="NumAndTwoDecimals(event , this);">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    {if $getTopicQHideByID.id}
                      <input type="hidden" name="id" value="{$getTopicQHideByID.id}" />
                      <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                      <a href="{$admin_file}?task=topic_hide&amp;action=edit&amp;tid={$smarty.get.tid}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
                    {else}
                      <button type="submit" name="butsubmit" class="btn btn-info"><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate(Key Lang: button_save){/if}</button>
                    {/if}
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div><!--panel panel-body-->

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr bgcolor="#eeeeee">
            <th>{if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}</th>
            <th>{if $multiLang.text_less_than_value}{$multiLang.text_less_than_value}{else}No Translate(Key Lang: text_less_than_value){/if}</th>
            <th>{if $multiLang.text_biger_than_value}{$multiLang.text_biger_than_value}{else}No Translate(Key Lang: text_biger_than_value){/if}</th>
            <th>Show {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if}</th>
            <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
            </tr>
          </thead>
          {if $listTestQueTopicHide|@count gt 0}
          <tbody>
          {foreach from = $listTestQueTopicHide item = data key=k}
            <tr>
              <td>{$data.topic_name1}</td>
              <td>{$data.less_than}</td>
              <td>{$data.bigger_than}</td>
              <td>{$data.topic_name2}</td>
              <td>
                <a href="{$admin_file}?task=topic_hide&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
                <!-- Trigger the modal with a button -->
                <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$data.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}"><i class="fa fa-trash-o"></i></button>
                <!-- Modal -->
                <div class="modal fade" id="myModal_{$data.id}" role="dialog">
                  <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="panel panel-primary modal-content">
                      <div class="panel-heading modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate(Key Lang: text_confirmation){/if}</h4>
                      </div>
                      <div class="modal-body">
                        <p>
                          {if $multiLang.text_delete_question}{$multiLang.text_delete_question}{else}No Translate(Key Lang: text_delete_question){/if}
                          <b>({$data.title|escape})</b>?
                        </p>
                      </div>
                      <div class="modal-footer">
                        <a href="{$admin_file}?task=topic_hide&amp;action=delete&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;">
                          <i class="fa fa-trash-o"></i> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}
                        </a>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Modal -->
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
        <a href="{$admin_file}?task=test_topic&amp;tid={$smarty.get.tid}" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
      </div><!--table-responsive  -->
      {include file="common/paginate.tpl"}
    </div><!--end panel-body  -->
  </div><!--end panel panel-primary  -->
{/block}

{block name="javascript"}
<script>
var getUrlBackTest = localStorage.getItem('urlTest');
if(getUrlBackTest !== null) $("#bCrumbTest").attr("href", getUrlBackTest);

function getTopicHide(sel)
{
  $(".loader").show();
  $.ajax({
    type: "GET",
    url: "{$admin_file}?task=topic_hide&action=get_topic_hide&tid={$smarty.get.tid}&tpid="+sel.value,
    success: function(data){
      var dataHTML = "";
      if(data.length > 0)
      {
        dataHTML += "<option value=''>--- Select Topic Second---</option>";
        for (var i = 0; i < data.length; i++) {

          if(data[i].topic_hide_id != null){
            dataHTML += "<option value='"+data[i].topic_id+"' disabled>"+data[i].name+"</option>";
          }else {
            dataHTML += "<option value='"+data[i].topic_id+"'>"+data[i].name+"</option>";
          }

        }
        $("#topic_second").html(dataHTML);
        $(".loader").hide();
      }else {
        dataHTML += "<option value=''>--- {if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} Show {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} ---</option>";

        $("#topic_second").html(dataHTML);
        $(".loader").hide();
      }

    },
    error: function(){
     //Show error here
     alert("Cannot show detail. Please try again later.");
    }
  });//End Ajax

}

</script>
{/block}
