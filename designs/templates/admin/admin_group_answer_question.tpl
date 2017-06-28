{extends file="admin/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTest" href="{$admin_file}?task=test">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_group_answer_question}{$multiLang.text_group_answer_question}{else}No Translate(Key Lang: text_group_answer_question){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>

<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_group_answer_question}{$multiLang.text_group_answer_question}{else}No Translate(Key Lang: text_group_answer_question){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}:</b> {$test.title} - {$test_group.title}
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=test_group" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="test">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_group_answer_question}{$multiLang.button_add_group_answer_question}{else}No Translate(Key Lang: button_add_group_answer_question){/if}
            </button>
          </div>
        </form>
        <div id="demo" class="collapse {if $error OR $getGroupAnswerByID.id} in {/if}">
          {if $getGroupAnswerByID.id}
          <form action="{$admin_file}?task=group_answer_question&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$getGroupAnswerByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=group_answer_question&amp;tid={$smarty.get.tid}" method="post">
          {/if}
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_group_answer_question}{$multiLang.text_group_answer_question}{else}No Translate(Key Lang: text_group_answer_question){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}:</label>
                  {if $error.g_answer_title}
                    <span style="color: red">{if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_group_answer_question}{$multiLang.text_group_answer_question}{else}No Translate(Key Lang: text_group_answer_question){/if}</span>
                  {/if}
                  <input type="text" name="g_answer_title" class="form-control" placeholder="{if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}"
                  value="{if $getGroupAnswerByID.g_answer_title}{$getGroupAnswerByID.g_answer_title}{else}{if $smarty.session.g_answer_ques.g_answer_title}{$smarty.session.g_answer_ques.g_answer_title}{/if}{/if}">
                </div>
              </div>
              {if $getGroupAnswerByID.id eq ''}
              <div class="col-md-12">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}:</label>
                  {if $error.test_question}
                    <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</span>
                  {/if}
                  {if $error.is_exist_group_answer_que}
                    <span style="color: red">{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if} {if $multiLang.text_is_existed}{$multiLang.text_is_existed}{else}No Translate(Key Lang: text_is_existed){/if}</span>
                  {/if}
                  <select class="form-control select2_group_answer" multiple="multiple" name="test_question[]" style="width:100%">
                    {foreach from=$listTestQuestion item=data}
                    <option value="{$data.id}" {foreach from=$groupAnswerCheckDisable item=v}{if $v.test_question_id eq $data.id}disabled{/if}{/foreach}>
                      (View Order: {$data.view_order}) {$data.q_title} (Type:{if $data.type eq 1} Text Input{elseif $data.type eq 2}Text Area{elseif $data.type eq 3}Redio{elseif $data.type eq 4}CheckBox{/if})
                    </option>
                    {/foreach}
                  </select>
                </div>
              </div>
              {/if}
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getGroupAnswerByID.id}
                    <input type="hidden" name="id" value="{$getGroupAnswerByID.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=group_answer_question&amp;tid={$smarty.get.tid}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
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
            <th>{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}</th>
            <th>{if $multiLang.text_group_answer_question}{$multiLang.text_group_answer_question}{else}No Translate(Key Lang: text_group_answer_question){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</th>
            <th>{if $multiLang.text_view_top}{$multiLang.text_view_top}{else}No Translate(Key Lang: text_view_top){/if}</th>
            <th width="100">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listGroupAnswer|@count gt 0}
        <tbody>
        {foreach from = $listGroupAnswer item = data key=k}
          <tr id="rowGAnswer{$data.id}">
            <td><span id="que_title{$data.id}">{$data.title}</span></td>
            <td>{$data.g_answer_title}</td>
            <td>
              <button type="button" class="btn btn-primary btn-xs">
                <i class="fa fa-check-circle"></i> Parent
              </button>
            </td>
            <td>
              <a href="{$admin_file}?task=group_answer_question&amp;action=edit&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}">
                <i class="fa fa-edit"></i>
              </a>
              <!-- Trigger the modal with a button -->
              <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$data.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}">
                <i class="fa fa-trash-o"></i>
              </button>
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
                        {if $multiLang.text_delete_test_question_confirmation}{$multiLang.text_delete_test_question_confirmation}{else}No Translate(Key Lang: text_delete_test_question_confirmation){/if}
                         <b>({$data.title|escape})</b>?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=group_answer_question&amp;action=delete&amp;tid={$smarty.get.tid}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
              <button type="button" onclick="getGroupAnswerDetail({$data.id});" id="btn_answer_detail{$data.id}" class="btn btn-success btn-xs" data-toggle="modal" data-target="#groupAnswer" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_detail}{$multiLang.button_detail}{else}No Translate(Key Lang: button_detail){/if}">
                <i class="fa fa-eye" aria-hidden="true"></i>
              </button>

            </td>
          </tr>
        {/foreach}
        </tbody>
        {else}
        <tr>
          <td colspan="3"><h4 style="text-align:center">{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h4></td>
        </tr>
        {/if}
      </table>
        <!-- Modal -->
        <div class="modal fade" id="groupAnswer" role="dialog">
          <div class="modal-dialog">
            <!-- Modal content-->
            <div class="panel panel-primary modal-content">
              <div class="panel-heading modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="panel-title modal-title"> {if $multiLang.text_group_answer_question}{$multiLang.text_group_answer_question}{else}No Translate(Key Lang: text_group_answer_question){/if} {if $multiLang.text_detail}{$multiLang.text_detail}{else}No Translate(Key Lang: text_detail){/if}</h4>
              </div>
              <div class="modal-body" style="padding: 0px;">
                <div class="row" style="padding-left: 10px; padding-right: 10px;">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="title"><span style="color: red">*</span> {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}:</label>
                      <span style="color: red" id="error_test_que_blank"></span>
                      <span style="color: red" id="error_test_que_existed"></span>

                      <select class="form-control select2_group_answer" multiple="multiple" name="test_question[]" style="width:100%" id="test_question">
                        {foreach from=$listTestQuestion item=data}
                        <option value="{$data.id}" id="modalTest_ques{$data.id}" {foreach from=$groupAnswerCheckDisable item=v}{if $v.test_question_id eq $data.id}disabled{/if}{/foreach}>
                          (View Order: {$data.view_order}) {$data.q_title} (Type:{if $data.type eq 1} Text Input{elseif $data.type eq 2}Text Area{elseif $data.type eq 3}Redio{elseif $data.type eq 4}CheckBox{/if})
                        </option>
                        {/foreach}
                      </select>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <button class="btn btn-danger" id="btn_save">
                      <i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate(Key Lang: button_save){/if}
                    </button>
                  </div>
                </div>
                <hr>
                <table class="table">
                  <thead class="theader">
                    <tr>
                      <th class="text-left">{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if} </th>
                      <th>{if $multiLang.text_view_top}{$multiLang.text_view_top}{else}No Translate(Key Lang: text_view_top){/if}</th>
                      <th class="text-left" width="100">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
                    </tr>
                  </thead>
                  <tbody id="dataDetail">

                  </tbody>
                </table>
              </div>
              <div class="modal-footer" style="margin-top: -10px;">
                <a href="#" type="button" class="btn btn-success btn-sm" style="opacity: 0;" id="btn_view_all">
                  View All <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                </a>
                <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal -->
        <!-- Modal Delete-->
        <div class="modal fade" id="ModalDelete" role="dialog">
          <div class="modal-dialog modal-sm">
            <div class="panel panel-primary modal-content" style="border-color: #594747;">
              <div class="panel-heading modal-header" style="background-color: #594747;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate(Key Lang: text_confirmation){/if}</h4>
              </div>
              <div class="modal-body">
                <p>{if $multiLang.text_confirmation_delete}{$multiLang.text_confirmation_delete}{else}No Translate(Key Lang:text_confirmation_delete){/if}<b id="title"></b>?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm collapsed" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang:button_close){/if}</i></button>
                <button type="button" class="btn btn-danger btn-sm collapsed" data-dismiss="modal" id="btn_delete"><i class="fa fa-trash-o"></i> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang:button_delete){/if}</button>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal -->
        <!-- Modal Change Status-->
        <div class="modal fade" id="modalStatus" role="dialog">
          <div class="modal-dialog modal-sm">
            <!-- Modal content -->
            <div class="panel panel-primary modal-content">
              <div class="panel-heading modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate(Key Lang: text_confirmation){/if}</h4>
              </div>
              <div class="modal-body">
                <p>
                  {if $multiLang.text_change_status}{$multiLang.text_change_status}{else}No Translate(Key Lang: text_change_status){/if}
                  <b> Parent </b>?
                </p>
              </div>
              <div class="modal-footer">
                <button class="btn btn-danger btn-md" data-dismiss="modal" style="color: white;" id="btn_change_status">
                  <i class="fa fa-check-circle-o"></i> {if $multiLang.button_yes}{$multiLang.button_yes}{else}No Translate(Key Lang: button_yes){/if}
                </button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_discard}{$multiLang.button_discard}{else}No Translate(Key Lang: button_discard){/if}</button>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal Change Status -->

      <a id="btnBack" href="javascript:history.back()" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
    </div><!--table-responsive  -->
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
  if(url.task === 'test') localStorage.setItem('urlTest',urlBack);
  //Get session url
  var getUrlBack = localStorage.getItem('urlTest');
  if(getUrlBack !== null){
    $("#btnBack").attr("href", getUrlBack);
    $("#bCrumbTest").attr("href", getUrlBack);
  }
  //End previous url

function getGroupAnswerDetail(id)
{
  //Clear value on select2
  var $multiGAnswer = $(".select2_group_answer").select2();
  $multiGAnswer.val(null).trigger("change");
  $("#error_test_que_blank").text("");

  //Show Loading gif
  $(".loader").show();
  //Add function "addGroupAnswer"
  $("#btn_save").attr("onclick", "addGroupAnswer("+id+")");

  $.ajax({
    type: "GET",
    url: "{$admin_file}?task=group_answer_question&action=detail&tid={$smarty.get.tid}&id="+id,
    success: function(data){
      var dataHTML = "";
      if(data.length > 0)
      {
        for (var i = 0; i < data.length; i++) {

          dataHTML += "<tr id='myRow_"+data[i].id+"'>";
            dataHTML += "<td><span id='title"+data[i].id+"'> "+data[i].title+"</span></td>";
            if(data[i].flag == 1){
              dataHTML += "<td><button type='button' class='btn btn-primary btn-xs'><i class='fa fa-check-circle'></i> Parent</button></td>";
            }else{
              dataHTML += "<td><button type='button' class='btn btn-warning btn-xs' onclick='showModalStatus("+data[i].id+","+id+");'><i class='fa fa-ban' aria-hidden='true'></i> Child</button></td>";
            }
            if(data[i].flag == 1 && data.length > 1){
              dataHTML += "<td><button type='button' id='btn_parent' class='btn btn-danger btn-xs' data-toggle1='tooltip' data-placement='top' title='{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}' onclick='showDelete("+data[i].id+","+data[i].test_question_id+")' disabled><i class='fa fa-trash-o'></i></button></td>";
            }else {
              dataHTML += "<td><button type='button' class='btn btn-danger btn-xs' data-toggle1='tooltip' data-placement='top' title='{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}' onclick='showDelete("+data[i].id+","+data[i].test_question_id+")'><i class='fa fa-trash-o'></i></button></td>";
            }

          dataHTML += "</tr>";
        }
        if(data[0].total > 10){
          $("#btn_view_all").attr("style", "opacity:1;");
          $("#btn_view_all").attr("href", "{$admin_file}?task=group_answer_question&action=view_all&tid={$smarty.get.tid}&gaid="+id);
        }
        $("#dataDetail").html(dataHTML);
      }else {
        dataHTML += "<tr>";
          dataHTML += "<td colspan='3'>No Data!</td>";
        dataHTML += "</tr>";
        $("#dataDetail").html(dataHTML);
      }
      //Hide Loading gif
      $(".loader").hide();
    },
    error: function(){
     //Show error here
     alert("Cannot show detail. Please try again later.");
     location.reload();
    }
  });//End Ajax
}

function showDelete(id, test_que_id)
{
 var title = $("#title"+id).text();
 $("#title").text(title);
 $("#btn_delete").attr("onclick", "deleteGroupAnswer("+id+","+test_que_id+")");
 $("#ModalDelete").modal("show");
}

function deleteGroupAnswer(id, test_que_id)
{
  //Show Loading gif
  $(".loader").show();
  $.ajax({
    type: "GET",
    url: "{$admin_file}?task=group_answer_question&action=delete_group_anwer&tid={$smarty.get.tid}&id="+id,
    success: function(data){
      var dataHTML = "";
      if(data.status == true){
        $("#myRow_"+id).remove();
      }
      if(data.countGanser == 1){
        $("#btn_parent").removeAttr("disabled");
      }
      if(data.countGanser == 0){
        $("#rowGAnswer"+id).remove();

        dataHTML += "<tr>";
          dataHTML += "<td colspan='3' align='center'>No Data!</td>";
        dataHTML += "</tr>";
        $("#dataDetail").html(dataHTML);
      }
      if(data.countGanser <= 10){
        $("#btn_view_all").attr("style", "opacity:0;");
      }
      $("#error_test_que_blank").text("");
      //hide Loading gif
      $(".loader").hide();
    },
    error: function(){
      //Show error here
      alert("Cannot delete!. Please try again later.");
      location.reload();
    }
  });//End Ajax
}
// flag_id is group_answer "flag equal 1"
function showModalStatus(id, flag_id){
  $("#btn_change_status").attr("onclick", "changeStatus("+id+","+flag_id+")");
  $("#modalStatus").modal("show");
}

function changeStatus(id, flag_id){
  //Show Loading gif
  $(".loader").show();
  $.ajax({
    type: "GET",
    url: "{$admin_file}?task=group_answer_question&action=change_flag&tid={$smarty.get.tid}&id="+id+"&flag_id="+flag_id,
    success: function(data){
      var dataHTML = "";
      if(data.length > 0)
      {
        for (var i = 0; i < data.length; i++) {
          dataHTML += "<tr id='myRow_"+data[i].id+"'>";
            dataHTML += "<td><span id='title"+data[i].id+"'> "+data[i].title+"</span></td>";
            if(data[i].flag == 1){
              $("#que_title"+flag_id).text(data[i].title);
              $("#que_title"+flag_id).attr("id", "que_title"+data[i].id);
              dataHTML += "<td><button type='button' class='btn btn-primary btn-xs'><i class='fa fa-check-circle'></i> Parent</button></td>";
            }else{
              dataHTML += "<td><button type='button' class='btn btn-warning btn-xs' onclick='showModalStatus("+data[i].id+","+id+");'><i class='fa fa-ban' aria-hidden='true'></i> Child</button></td>";
            }
            if(data[i].flag == 1 && data.length > 1){
              dataHTML += "<td><button type='button' id='btn_parent' class='btn btn-danger btn-xs' data-toggle1='tooltip' data-placement='top' title='{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}' onclick='showDelete("+data[i].id+","+data[i].test_question_id+")' disabled><i class='fa fa-trash-o'></i></button></td>";
            }else {
              dataHTML += "<td><button type='button' class='btn btn-danger btn-xs' data-toggle1='tooltip' data-placement='top' title='{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}' onclick='showDelete("+data[i].id+","+data[i].test_question_id+")'><i class='fa fa-trash-o'></i></button></td>";
            }

          dataHTML += "</tr>";
        }
        if(data[0].total > 10){
          $("#btn_view_all").attr("style", "opacity:1;");
          $("#btn_view_all").attr("href", "{$admin_file}?task=group_answer_question&action=view_all&tid={$smarty.get.tid}&gaid="+id);
        }
        $("#btn_answer_detail"+flag_id).attr("onclick", "getGroupAnswerDetail("+id+");");
        $("#btn_answer_detail"+flag_id).attr("id", "btn_answer_detail"+id+"");
        $("#dataDetail").html(dataHTML);
      }else {
        dataHTML += "<tr>";
          dataHTML += "<td colspan='3'>No Data!</td>";
        dataHTML += "</tr>";
        $("#dataDetail").html(dataHTML);
      }
      //hide Loading gif
      $(".loader").hide();
    },
    error: function(){
      //Show error here
      alert("Cannot change status. Please try again later.");
      location.reload();
    }
  });//End Ajax
}

function addGroupAnswer(id){
  //Show Loading gif
  $(".loader").show();
  var test_que = document.getElementById("test_question");
  var data = [];
  var error = [];

  for (var i = 0; i < test_que.options.length; i++) {
   if(test_que.options[i].selected == true){
     error.push({ rowIndex: test_que.options[i].value });
     data.push({ test_que_id: test_que.options[i].value });
    }
  }
  var paramdata = { gans_flag_id: id, test_id: {$smarty.get.tid}, data: JSON.stringify(data) };

  if(error.length > 0){
    jQuery.ajax({
      type: 'POST',
      url:'{$admin_file}?task=ajax&action=save_group_answer',
      dataType:'json',
      data: paramdata,
      error: function (request, status, error) {
        alert("Cannot add test question. Please try again later.");
        location.reload();
      },
      success: function (data, error) {
        //Clear value on select2
        var $multiGAnswer = $(".select2_group_answer").select2();
        $multiGAnswer.val(null).trigger("change");

        var dataHTML = "";
        if(data.length > 0 && data.is_exist_group_answer_que == undefined)
        {
          for (var i = 0; i < data.length; i++) {
            dataHTML += "<tr id='myRow_"+data[i].id+"'>";
              dataHTML += "<td><span id='title"+data[i].id+"'> "+data[i].title+"</span></td>";
              if(data[i].flag == 1){
                dataHTML += "<td><button type='button' class='btn btn-primary btn-xs'><i class='fa fa-check-circle'></i> Parent</button></td>";
              }else{
                dataHTML += "<td><button type='button' class='btn btn-warning btn-xs' onclick='showModalStatus("+data[i].id+","+id+");'><i class='fa fa-ban' aria-hidden='true'></i> Child</button></td>";
              }
              if(data[i].flag == 1 && data.length > 1){
                dataHTML += "<td><button type='button' id='btn_parent' class='btn btn-danger btn-xs' data-toggle1='tooltip' data-placement='top' title='{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}' onclick='showDelete("+data[i].id+","+data[i].test_question_id+")' disabled><i class='fa fa-trash-o'></i></button></td>";
              }else {
                dataHTML += "<td><button type='button' class='btn btn-danger btn-xs' data-toggle1='tooltip' data-placement='top' title='{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}' onclick='showDelete("+data[i].id+","+data[i].test_question_id+")'><i class='fa fa-trash-o'></i></button></td>";
              }

            dataHTML += "</tr>";
          }
          if(data[0].total > 10){
            $("#btn_view_all").attr("style", "opacity:1;");
            $("#btn_view_all").attr("href", "{$admin_file}?task=group_answer_question&action=view_all&tid={$smarty.get.tid}&gaid="+id);
          }
          $("#dataDetail").html(dataHTML);
        }else {
          if(data.is_exist_group_answer_que == 1){
            $("#error_test_que_existed").text("{if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if} {if $multiLang.text_is_existed}{$multiLang.text_is_existed}{else}No Translate(Key Lang: text_is_existed){/if}");
          }else {
            dataHTML += "<tr>";
              dataHTML += "<td colspan='3'>No Data!</td>";
            dataHTML += "</tr>";
            $("#dataDetail").html(dataHTML);
          }
        }
        $("#error_test_que_blank").text("");
        //Hide Loading gif
        $(".loader").hide();
      }
    });
  }else {
    $("#error_test_que_blank").text("{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_test_question}{$multiLang.text_test_question}{else}No Translate(Key Lang: text_test_question){/if}");
    $(".loader").hide();
  }


}

</script>
{/block}
