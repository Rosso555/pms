{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbMailer" href="{$admin_file}?task=mailerlite">{if $multiLang.text_mailer_lite}{$multiLang.text_mailer_lite}{else}No Translate(Key Lang:text_mailer_lte){/if}</a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_transaction}{$multiLang.text_transaction}{else}No Translate(Key Lang: text_transaction){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
{if $error.is_exist_cat}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot add "<strong>{$error.cat_title}</strong>" because it has been already. <a href="{$admin_file}?task=apitransaction_email&amp;mlid={$smarty.get.mlid}&amp;cid={$error.catid}"> Click Here </a>
  </div>
{/if}
<!-- {if $smarty.cookies.status eq 1}
  <div class="alert alert-success">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Submit email processing...! You can check on https://www.mailerlite.com.
  </div>
{/if} -->
{if $smarty.cookies.status eq 2}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! can't save data. Please try again!
  </div>
{/if}
  <div class="panel panel-primary">
    <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_email_transaction}{$multiLang.text_email_transaction}{else}No Translate(Key Lang: text_email_transaction){/if}</h4></div>
    <div class="panel-body">
      <div class="box_title">
        <b>{if $multiLang.text_mailer_lite}{$multiLang.text_mailer_lite}{else}No Translate(Key Lang: text_mailer_lte){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}:</b> {$mailerLite.title}
      </div>
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <form class="form" role="form" action="{$admin_file}?task=apitransaction" method="GET" style="padding: 1px 0px 12px 1px;">
              <input type="hidden" name="task" value="apitransaction">
              <input type="hidden" name="mlid" value="{$smarty.get.mlid}">
              <div class="col-md-6">
                <div class="form-group">
                  <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_email_transaction}{$multiLang.button_add_email_transaction}{else}No Translate(Key Lang: button_add_email_transaction){/if}
                  </button>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <select class="form-control select2" name="cid" style="width:100%">
                    <option value="">--- {if $multiLang.text_category}{$multiLang.text_category}{else}No Translate(Key Lang: text_category){/if} </option>
                    {foreach from=$listCategory item=data}
                    <option value="{$data.id}" {if $smarty.get.cid eq $data.id}selected{/if}>{$data.name}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate(Key Lang: button_search){/if}</button>
              </div>
            </form>
          </div>
          <div id="demo" class="collapse {if $error or $getEmailApiTranByID.id}in{/if}">
            {if $getEmailApiTranByID.id}
              <form class="from" action="{$admin_file}?task=apitransaction_email&amp;action=edit&amp;mlid={$smarty.get.mlid}&amp;id={$getEmailApiTranByID.id}" method="post">
            {else}
              <form class="from" action="{$admin_file}?task=apitransaction_email&amp;mlid={$smarty.get.mlid}" method="post">
            {/if}
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="title"><span style="color: red">*</span> {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}:</label>
                    {if $error.title}
                      <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</span>
                    {/if}
                    <input type="text" name="title" class="form-control" placeholder="Title"
                    value="{if $getEmailApiTranByID.title}{$getEmailApiTranByID.title}{else}{if $smarty.session.apitran_email.title}{$smarty.session.apitran_email.title}{/if}{/if}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="title"><span style="color: red">*</span> {if $multiLang.text_category}{$multiLang.text_category}{else}No Translate(Key Lang: text_category){/if}:</label>
                    {if $error.catid}
                      <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_category}{$multiLang.text_category}{else}No Translate(Key Lang: text_category){/if}</span>
                    {/if}
                    <select class="form-control select2" name="catid" style="width:100%">
                      <option value="">---{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_category}{$multiLang.text_category}{else}No Translate(Key Lang: text_category){/if}---</option>
                      {foreach from=$listCategory item=v}
                      <option value="{$v.id}" {if $getEmailApiTranByID.category_id}{if $getEmailApiTranByID.category_id eq $v.id}selected{/if}{else}{if $smarty.session.apitran_email.catid eq $v.id}selected{/if}{/if}>{$v.name}</option>
                      {/foreach}
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <label for="title" style="margin-bottom: 0px;"> <span style="color: red">*</span> {if $multiLang.menu_mailerlitegroup}{$multiLang.menu_mailerlitegroup}{else}No Translate(Key Lang: menu_mailerlitegroup){/if}:</label>
                  {if $error.groupid}
                    <span style="color: red">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if} {if $multiLang.text_mailer_lite_gruppe}{$multiLang.text_mailer_lite_gruppe}{else}No Translate(Key Lang: text_mailer_lite_gruppe){/if}</span>
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
                    {if $getEmailApiTranByID.id}
                      <input type="hidden" name="id" value="{$getEmailApiTranByID.id}" />
                      <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                      <a href="{$admin_file}?task=apitransaction_email&amp;mlid={$smarty.get.mlid}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
                    {else}
                      <button type="submit" name="butsubmit" class="btn btn-danger"><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate(Key Lang: button_save){/if}</button>
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
              <td>{$data.cat_title}</td>
              <td><span class="badge" id="countMG_{$data.id}">{$data.count_group}</span></td>
              <td>
                <a href="{$admin_file}?task=apitransaction_email&amp;action=edit&amp;mlid={$smarty.get.mlid}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
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
                          <b>({$data.title|escape} & {$data.cat_title})</b>?
                        </p>
                      </div>
                      <div class="modal-footer">
                        <a href="{$admin_file}?task=apitransaction_email&amp;action=delete&amp;mlid={$smarty.get.mlid}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;">
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

                <button type="button" onclick="getMailGroupSubmit({$data.id}, {$data.category_id});" class="btn btn-danger btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_send_mail_user}{$multiLang.button_send_mail_user}{else}No Translate(Key Lang: button_send_mail_user){/if}"><i class="fa fa-upload" aria-hidden="true"></i></button>

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
          <!-- Modal -->
          <div class="modal fade" id="modelSenMail" role="dialog">
            <div class="modal-dialog">
              <!-- Modal content-->
              <form action="{$admin_file}?task=submit_mailerlite_email&amp;mlid={$smarty.get.mlid}" method="post">
                <input type="hidden" value="" name="catid" id="catid">
                <div class="panel panel-primary modal-content">
                  <div class="panel-heading modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate(Key Lang: text_confirmation){/if}</h4>
                  </div>

                  <div class="modal-body">
                    <p>You want to submit email to Miler Lite Group:</p>
                    <div class="checkbox box" style="margin-top: 5px;" id="dataSenMail">

                    </div>
                  </div>
                  <div class="modal-footer">

                    <button id="btn_subemail" type="submit" class="btn btn-danger btn-md" style="color: white;">
                      <i class="fa fa-check-circle-o" aria-hidden="true"></i> {if $multiLang.button_yes}{$multiLang.button_yes}{else}No Translate(Key Lang: button_yes){/if}</i>
                    </button>
                    <button id="btn_noemail" type="button" class="btn btn-danger btn-md disabled" style="color: white;" data-toggle1="tooltip" data-placement="top" title="Can't Submit, Because No email.">
                      <i class="fa fa-check-circle-o" aria-hidden="true"></i> {if $multiLang.button_yes}{$multiLang.button_yes}{else}No Translate(Key Lang: button_yes){/if}</i>
                    </button>

                    <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- Modal -->

      </div><!--table-responsive  -->
      <br>
      <a id="btnBack" href="{$admin_file}?task=mailerlite" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate (Key Lang: text_back){/if}</a>
      {include file="common/paginate.tpl"}
    </div><!--end panel-body  -->
  </div><!--end panel panel-primary  -->
{/block}

{block name="javascript"}
<script>
  //Get previous url
  var urlBack =  document.referrer;
  var url = '';
  if(urlBack !== '') url = getUrlPrevious(urlBack);
  if(url.task === 'mailerlite') localStorage.setItem('urlMailerlite',urlBack);
  //Get session url
  var getUrlBack = localStorage.getItem('urlMailerlite');
  if(getUrlBack !== null){
    $("#btnBack").attr("href", getUrlBack);
    $("#bCrumbMailer").attr("href", getUrlBack);
  }
  //End previous url

function getMailGroupDetail(trid)
{
  $(".loader").show();
  $.ajax({
    type: "GET",
    url: "{$admin_file}?task=apitransaction_email&action=detail&trid="+trid,
    success: function(data){
      var dataHTML = "";
      if(data.length > 0)
      {
        for (var i = 0; i < data.length; i++) {
          dataHTML += "<tr id='myRow_"+data[i].id+"'>";
            dataHTML += "<td><span id='group_title"+data[i].id+"'> "+data[i].group_title+"</span></td>";
            // dataHTML += "<td><span title='Delete' data-toggle='tooltip'><button type='button' class='btn btn-danger btn-xs' onclick='showDeleteSite("+data[i].id+")'><i class='fa fa-trash-o'></i></button></span></td>";
            dataHTML += "<td><button type='button' class='btn btn-danger btn-xs' data-toggle1='tooltip' data-placement='top' title='{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}' onclick='showDelete("+trid+","+data[i].id+")'><i class='fa fa-trash-o'></i></button></td>";

          dataHTML += "</tr>";
        }
        $("#dataDetail").html(dataHTML);
        $(".loader").hide();
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

function getMailGroupSubmit(trid, cid)
{
  $(".loader").show();
 $.ajax({
   type: "GET",
   url: "{$admin_file}?task=submit_mailerlite_email&action=detail&trid="+trid+"&cid="+cid,
   success: function(data){
     var dataHTML = "";
     if(data.mailer_group.length > 0)
     {
       for (var i = 0; i < data.mailer_group.length; i++) {

         dataHTML += " <label><input type='checkbox' disabled value='"+data.mailer_group[i].group_id+"' name='groupid[]' style='margin-top: 1px;' checked='hecked'>"+data.mailer_group[i].group_title+"</label>&nbsp;&nbsp; ";
       }
       $("#dataSenMail").html(dataHTML);
       $(".loader").hide();
     }else {
       dataHTML += "&nbsp;";
       $("#dataSenMail").html(dataHTML);
     }

    if(data.countEmailTest == 0){
      $('#btn_noemail').show();
      $('#btn_subemail').hide();
    } else {
      $('#btn_noemail').hide();
      $('#btn_subemail').show();
    }

    $('#catid').val(cid);
    $('#modelSenMail').modal('show');
   },
   error: function(){
    //Show error here
    alert("Cannot show detail. Please try again later.");
    location.reload();
   }
 });//End Ajax
}

function showDelete(trid, mgid)
{
 var group_title = $("#group_title"+mgid).text();
 $("#group_title").text(group_title);
 $("#btn_delete").attr("onclick", "deleteMgroup("+trid+","+mgid+")");
 $("#ModalDelete").modal("show");
}

function deleteMgroup(trid, mgid)
{
  $(".loader").show();
  $.ajax({
    type: "GET",
    url: "{$admin_file}?task=apitransaction_email&action=delete_mgroup&trid="+trid+"&mgid="+mgid,
    success: function(data){

      $("#countMG_"+trid).text(data.countMG);

      if(data.status == true){
        $("#myRow_"+mgid).remove();
      }
      if(data.countMG == 0){
        $("#myRowTransaction_"+trid).remove();
      }
      $(".loader").hide();
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
