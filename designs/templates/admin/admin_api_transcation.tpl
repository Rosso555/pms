{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a href="{$admin_file}?task=mailerlite">{if $multiLang.text_mailer_lite}{$multiLang.text_mailer_lite}{else}No Translate(Key Lang:text_mailer_lite){/if}</a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_transaction}{$multiLang.text_transaction}{else}No Translate(Key Lang: text_transaction){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
{if $error.is_exist_test}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot add "<strong>{$error.test_title}</strong>" because it has been already. <a href="{$admin_file}?task=apitransaction&amp;mlid={$smarty.get.mlid}&amp;tid={$error.test_id}"> Click Here </a>
  </div>
{/if}
  <div class="panel panel-primary">
    <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_transaction}{$multiLang.text_transaction}{else}No Translate(Key Lang: text_transaction){/if}</h4></div>
    <div class="panel-body">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <form class="form" role="form" action="{$admin_file}?task=apitransaction" method="GET" style="padding: 1px 0px 12px 1px;">
              <input type="hidden" name="task" value="apitransaction">
              <input type="hidden" name="mlid" value="{$smarty.get.mlid}">
              <div class="col-md-6">
                <div class="form-group">
                  <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_transaction}{$multiLang.button_add_transaction}{else}No Translate(Key Lang: button_add_transaction){/if}
                  </button>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <select class="form-control select2" name="tid" style="width:100%">
                    <option value="">--- {if $multiLang.button_add_transaction}{$multiLang.button_add_transaction}{else}No Translate(Key Lang: button_add_transaction){/if} Test ---</option>
                    {foreach from=$listTest item=data}
                    <option value="{$data.id}" {if $smarty.get.tid eq $data.id}selected{/if}>{$data.title}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate(Key Lang: button_search){/if}</button>
              </div>
            </form>
          </div>
          <div id="demo" class="collapse {if $error or $getApiTranByID.id}in{/if}">
            {if $getApiTranByID.id}
              <form class="from" action="{$admin_file}?task=apitransaction&amp;action=edit&amp;mlid={$smarty.get.mlid}&amp;id={$getApiTranByID.id}" method="post">
            {else}
              <form class="from" action="{$admin_file}?task=apitransaction&amp;action=add&amp;mlid={$smarty.get.mlid}" method="post">
            {/if}
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="title"><span style="color: red">*</span> {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}:</label>
                    {if $error.title}
                      <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</span>
                    {/if}
                    <input type="text" name="title" class="form-control" placeholder="Title"
                    value="{if $getApiTranByID.title}{$getApiTranByID.title}{else}{if $smarty.session.apitransaction.title}{$smarty.session.apitransaction.title}{/if}{/if}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="title"><span style="color: red">*</span> {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</label>
                    {if $error.testid}
                      <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</span>
                    {/if}
                    <select class="form-control select2" name="testid" style="width:100%">
                      <option value="">---{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}---</option>
                      {foreach from=$listTest item=v}
                      <option value="{$v.id}" {if $getApiTranByID.test_id}{if $getApiTranByID.test_id eq $v.id}selected{/if}{else}{if $smarty.session.apitransaction.testid eq $v.id}selected{/if}{/if}>{$v.title}</option>
                      {/foreach}
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <label for="title" style="margin-bottom: 0px;"> <span style="color: red">*</span> {if $multiLang.menu_mailerlitegroup}{$multiLang.menu_mailerlitegroup}{else}No Translate(Key Lang: menu_mailerlitegroup){/if}:</label>
                  {if $error.groupid}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_mailer_lite_gruppe}{$multiLang.text_mailer_lite_gruppe}{else}No Translate(Key Lang: text_mailer_lite_gruppe){/if}</span>
                  {/if}
                  <div class="checkbox box" style="margin-top: 5px;">
                    {foreach from=$mailerliteGroup item=d}
                      {if $d->id != ''}
                        <label><input type="checkbox" value="{$d->id}" name="groupid[]" {foreach from=$listEditMailerlite item=v}{if $v.group_id eq $d->id}checked{/if}{/foreach} style="margin-top: 1px;"> {$d->name}</label>
                      {else}
                        &nbsp;
                      {/if}
                    {/foreach}
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    {if $getApiTranByID.id}
                      <input type="hidden" name="id" value="{$getApiTranByID.id}" />
                      <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                      <a href="{$admin_file}?task=apitransaction&amp;mlid={$smarty.get.mlid}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
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
            <th>{if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</th>
            <th>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</th>
            <th>{if $multiLang.menu_mailerlitegroup}{$multiLang.menu_mailerlitegroup}{else}No Translate(Key Lang: menu_mailerlitegroup){/if}</th>
            <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
            </tr>
          </thead>
          {if $listTransaction|@count gt 0}
          <tbody>
          {foreach from = $listTransaction item=data key=k}
            <tr id="myRowTransaction_{$data.id}">
              <td>{$data.title}</td>
              <td>{$data.test_title}</td>
              <td><span class="badge" id="countMG_{$data.id}">{$data.count_group}</span></td>
              <td>
                <a href="{$admin_file}?task=apitransaction&amp;action=edit&amp;mlid={$smarty.get.mlid}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
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
                        {if $multiLang.text_confirmation_delete}{$multiLang.text_confirmation_delete}{else}No Translate(Key Lang:text_confirmation_delete){/if} {if $multiLang.text_transaction}{$multiLang.text_transaction}{else}No Translate(Key Lang:text_transaction){/if}
                          <b>({$data.title|escape} & {$data.test_title})</b>?
                        </p>
                      </div>
                      <div class="modal-footer">
                        <a href="{$admin_file}?task=apitransaction&amp;action=delete&amp;mlid={$smarty.get.mlid}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;">
                          <i class="fa fa-trash-o"></i> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}
                        </a>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Modal -->
                <a class="btn btn-success btn-xs" onclick="getMailGroupDetail({$data.id});" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_view_mailerlitegroup}{$multiLang.button_view_mailerlitegroup}{else}No Translate(Key Lang: button_view_mailerlitegroup){/if}">
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </a>
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
          <div class="modal fade" id="ModalDetail" role="dialog">
            <div class="modal-dialog">
              <!-- Modal content-->
              <div class="panel panel-primary modal-content">
                <div class="panel-heading modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="panel-title modal-title"> {if $multiLang.text_mailer_lite_group_detail}{$multiLang.text_mailer_lite_group_detail}{else}No Translate(Key Lang: text_mailer_lite_group_detail){/if}</h4>
                </div>
                <div class="modal-body" style="padding: 0px;">
                  <table class="table">
                    <thead class="theader">
                      <tr>
                        <th class="text-left">{if $multiLang.text_group_title}{$multiLang.text_group_title}{else}No Translate(Key Lang: text_group_title){/if} </th>
                        <th class="text-left" width="100">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
                      </tr>
                    </thead>
                    <tbody id="dataDetail">

                    </tbody>
                  </table>
                </div>
                <div class="modal-footer" style="margin-top: -10px;">
                  <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End Modal -->
          <!-- Modal -->
          <div class="modal fade" id="ModalDelete" role="dialog">
            <div class="modal-dialog modal-sm">
              <div class="panel panel-primary modal-content" style="border-color: #594747;">
                <div class="panel-heading modal-header" style="background-color: #594747;">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate(Key Lang: text_confirmation){/if}</h4>
                </div>
                <div class="modal-body">
                  <p>{if $multiLang.text_confirmation_delete}{$multiLang.text_confirmation_delete}{else}No Translate(Key Lang:text_confirmation_delete){/if}<b id="group_title"></b>?</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary btn-sm collapsed" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang:button_close){/if}</i></button>
                  <button type="button" class="btn btn-danger btn-sm collapsed" data-dismiss="modal" id="btn_delete"><i class="fa fa-trash-o"></i> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang:button_delete){/if}</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End Modal -->
        <a href="{$admin_file}?task=mailerlite" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate (Key Lang: text_back){/if}</a>
      </div><!--table-responsive  -->
      {include file="common/paginate.tpl"}
    </div><!--end panel-body  -->
  </div><!--end panel panel-primary  -->
{/block}

{block name="javascript"}
<script>
function getMailGroupDetail(tid)
{
  $.ajax({
    type: "GET",
    url: "{$admin_file}?task=apitransaction&action=detail&tid="+tid,
    success: function(data){
      var dataHTML = "";
      if(data.length > 0)
      {
        for (var i = 0; i < data.length; i++) {
          dataHTML += "<tr id='myRow_"+data[i].id+"'>";
            dataHTML += "<td><span id='group_title"+data[i].id+"'> "+data[i].group_title+"</span></td>";
            // dataHTML += "<td><span title='Delete' data-toggle='tooltip'><button type='button' class='btn btn-danger btn-xs' onclick='showDeleteSite("+data[i].id+")'><i class='fa fa-trash-o'></i></button></span></td>";
            dataHTML += "<td><button type='button' class='btn btn-danger btn-xs' data-toggle1='tooltip' data-placement='top' title='{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}' onclick='showDelete("+tid+","+data[i].id+")'><i class='fa fa-trash-o'></i></button></td>";

          dataHTML += "</tr>";
        }
        $("#dataDetail").html(dataHTML);
      }else {
        dataHTML += "<tr>";
          dataHTML += "<td>No Data!</td>";
        dataHTML += "</tr>";
        $("#dataDetail").html(dataHTML);
      }
      $('#ModalDetail').modal('show');
    },
    error: function(){
     //Show error here
     alert("Cannot show detail. Please try again later.");
     location.reload();
    }
  });//End Ajax
 }


function showDelete(tid, mgid)
{
 var group_title = $("#group_title"+mgid).text();
 $("#group_title").text(group_title);
 $("#btn_delete").attr("onclick", "deleteMgroup("+tid+","+mgid+")");
 $("#ModalDelete").modal("show");
}

function deleteMgroup(tid, mgid)
{
  $.ajax({
    type: "GET",
    url: "{$admin_file}?task=apitransaction&action=delete_mgroup&tid="+tid+"&mgid="+mgid,
    success: function(data){

      $("#countMG_"+tid).text(data.countMG);

      if(data.status == true){
        $("#myRow_"+mgid).remove();
      }
      if(data.countMG == 0){
        $("#myRowTransaction_"+tid).remove();
      }

    },
    error: function(){
      //Show error here
      alert("Cannot delete!. Please try again later.");
      location.reload();
    }
  });//End Ajax
}

</script>
{/block}
