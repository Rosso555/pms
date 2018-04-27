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
              <input type="hidden" name="task" value="patient">
              <div class="form-group" style="margin-bottom:5px;">
                <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                  <i class="fa fa-plus-circle"></i> Add Patient
                </button>
              </div>
              &nbsp;&nbsp;&nbsp;
              <div class="form-group select2_search_inline" style="margin-bottom:5px;">
                <select class="form-control select2_search" name="psy_id" style="width:100%;">
                  <option value="">---Select Psychologist---</option>
                  {foreach from=$listPsychologist item=v}
                  <option value="{$v.id}" {if $smarty.get.psy_id eq $v.id}selected{/if}>{$v.first_name} {$v.last_name}</option>
                  {/foreach}
                </select>
              </div>
              <div class="form-group select2_search_inline" style="margin-bottom:5px;">
                <select class="form-control select2_search" name="gender" style="width:100%">
                  <option value="">---Select Gender---</option>
                  <option value="1" {if $smarty.get.gender eq 1}selected{/if}>Male</option>
                  <option value="2" {if $smarty.get.gender eq 3}selected{/if}>Female</option>
                </select>
              </div>
              <div class="form-group select2_search_inline" style="margin-bottom:5px;">
                <select class="form-control select2_search" name="status" style="width:100%">
                  <option value="">---Select Status---</option>
                  <option value="1" {if $smarty.get.status eq 1}selected{/if}>Active</option>
                  <option value="2" {if $smarty.get.status eq 2}selected{/if}>Stop</option>
                </select>
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <input type="text" class="form-control" placeholder="Keyword" name="kwd" value="{$smarty.get.kwd}">
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
              </div>
            </form>
          </div>
        </div>
        <div id="demo" class="collapse {if $error OR $editPatient.id}in{/if}">
          {if $editPatient.id}
          <form action="{$admin_file}?task=patient&amp;action=edit&amp;id={$editPatient.id}" method="post">
            {else}
          <form action="{$admin_file}?task=patient&amp;action=add" method="post">
          {/if}
            {if $error.code_existed}
            <br>
            <div class="row">
              <div class="col-md-12">
                <div class="alert alert-danger alert-dismissible">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  * Code is existed, such as:<br>
                  {foreach from=$error.code_existed_data item=v}
                    &nbsp;&nbsp;- {$v.code}<br>
                  {/foreach}
                </div>
              </div>
            </div>
            {/if}
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email"><span style="color:red">*</span> Psychologist:</label> {if $error.psy_id}<span style="color:red">Please select psychologist!</span>{/if}
                  <select class="form-control select2" name="psy_id" style="width:100%;" onchange="getCodePwd(this)">
                    <option value="">---Select Psychologist---</option>
                    {foreach from=$listPsychologist item=v}
                    <option value="{$v.id}" {if $editPatient.psychologist_id}{if $editPatient.psychologist_id eq $v.id}selected{/if}{else}{if $smarty.session.patient.psy_id eq $v.id}selected{/if}{/if}>{$v.first_name} {$v.last_name}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group" style="margin-bottom: 5px;">
                  <label for="code"><span style="color:red">*</span> Code:</label>
                  {if $error.code}<span style="color:red">Please enter code!</span>{/if}
                  {if $error.code_existed}<span style="color:red">Code is existed!</span>{/if}
                  <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i> <span id="code_pat">{if $error OR $editPatient.id}: {$psyCodePat.code_pat}{/if}</span></div>
                    <input type="text" class="form-control" id="code" placeholder="Enter code" name="code" maxlength="7" value="{if $editPatient.code}{$editPatient.code|replace:$psyCodePat.code_pat:''}{else}{$smarty.session.patient.code}{/if}">
                  </div>
                  <span style="color:red">Please enter code one letter and 6 number.</span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="pwd"><span style="color:red">*</span> Password:</label>
                  {if $error.password}<span style="color:red">Please enter password!</span>{/if}
                  <input type="text" class="form-control" id="pwd" placeholder="Enter password" name="password" value="{if $editPatient.password}{$editPatient.password}{else}{$smarty.session.patient.password}{/if}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email">Email:</label>
                  {if $error.invalid_email}<span style="color:red">Your email is not valid!</span>{/if}
                  {if $error.exist_email}<span style="color:red">Your email is existed!</span>{/if}
                  <input type="email" class="form-control" id="email" placeholder="example@domain.com" name="email" value="{if $editPatient.email}{$editPatient.email}{else}{$smarty.session.patient.email}{/if}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="phone"> Phone:</label>
                  <input type="text" class="form-control" id="phone" placeholder="Enter phone" name="phone" value="{if $editPatient.phone}{$editPatient.phone}{else}{$smarty.session.patient.phone}{/if}" onkeyup="NumAndTwoDecimals(event, this);">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email"> Gender:</label>
                  <div class="box">
                    <label class="radio-inline"><input type="radio" name="gender" value="1" {if $editPatient.gender}{if $editPatient.gender eq 1}checked{/if}{else}{if $smarty.session.patient.gender eq 1}checked{/if}{/if}> Male</label>
                    <label class="radio-inline"><input type="radio" name="gender" value="2" {if $editPatient.gender}{if $editPatient.gender eq 2}checked{/if}{else}{if $smarty.session.patient.gender eq 2}checked{/if}{/if}> Female</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="age"> Age:</label>
                  {if $error.age}<span style="color:red">Please enter age!</span>{/if}
                  <input type="text" class="form-control" id="age" placeholder="Example: 123..." name="age" value="{if $editPatient.age}{$editPatient.age}{else}{$smarty.session.patient.age}{/if}" onkeyup="NumAndTwoDecimals(event , this);">
                </div>
              </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    {if $editPatient.id}
                    <input type="hidden" name="id" value="{$editPatient.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> Update</button>
                    <a href="{$admin_file}?task=patient" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> Cancel</a>
                    {else}
                    <button type="submit" name="submit" class="btn btn-info"><i class="fa fa-floppy-o"></i> Save</button>
                    {/if}
                  </div>
                </div>
              </div>
          </form>

        </div><!--End collapse-->
      </div><!--End panel-heading-->
    </div><!--End panel panel-primary-->
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
            <th>Psychologist</th>
            <th>Code</th>
            <th>Password</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Age</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          {if $listPatient|@count gt 0}
          {foreach from=$listPatient item=v}
          <tr>
            <td>{$v.first_name} {$v.last_name}</td>
            <td>{$v.code}</td>
            <td>{$v.password}</td>
            <td>{$v.email}</td>
            <td>{if $v.gender eq 1}Male{elseif $v.gender eq 2}Female{else}~{/if}</td>
            <td>{$v.age}</td>
            <td>
              {if $v.deleted_at eq null}
              {if $v.status eq 1}
              <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#status_{$v.id}" data-toggle1="tooltip" data-placement="top" title="Click Change Status">
                <i class="fa fa-check-circle"></i> Active
              </button>
              {else}
              <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#status_{$v.id}" data-toggle1="tooltip" data-placement="top" title="Click Change Status">
                <i class="fa fa-stop-circle-o" aria-hidden="true"></i> Stoped
              </button>
              {/if}
              {else}
              <button type="button" class="btn btn-warning btn-xs">
                <i class="fa fa-ban" aria-hidden="true"></i> Deleted
              </button>
              {/if}
              <!-- Modal -->
              <div class="modal fade" id="status_{$v.id}" role="dialog">
                <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="panel panel-primary modal-content">
                    <div class="panel-heading modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="panel-title modal-title">Confirmation</h4>
                    </div>
                    <div class="modal-body">
                      <p>
                        Are you sure you want to change status to <b>{if $v.status eq 1}Stop{else}Active{/if}</b>?
                      </p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=patient&amp;action=change_status&amp;id={$v.id|escape}&amp;status={$v.status|escape}" class="btn btn-danger btn-md" style="color: white;">
                        <i class="fa fa-check-circle-o"></i> Yes
                      </a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> Discard</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
            </td>
            <td>
              <a href="{$admin_file}?task=patient&amp;action=edit&amp;id={$v.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate (Key Lang:button_edit){/if}"><i class="fa fa-edit"></i></a>
              <button href="#myModal_{$v.id}" class= "btn btn-danger btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate (Key Lang:button_delete){/if}" data-toggle= "modal"><i class="fa fa-trash-o"></i></button>
              <div class="modal fade" id="myModal_{$v.id}" role="dialog">
                <div class="modal-dialog">
                  <div class="panel panel-primary modal-content">
                    <div class="panel-heading modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate (Key Lang:text_confirmation){/if}</h4>
                    </div>
                    <div class="modal-body">
                      <p>{if $multiLang.text_confirm_delete}{$multiLang.text_confirm_delete}{else}No Translate (Key Lang:text_confirm_delete){/if} {$v.name}?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=patient&amp;action=delete&amp;id={$v.id}" class="btn btn-danger btn-md"><i class="fa fa-check-circle-o"></i> {if $multiLang.button_yes}{$multiLang.button_yes}{else}No Translate (Key Lang:button_yes){/if}</a>
                      <button type="button" class="btn btn-primary collapsed" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate (Key Lang:button_close){/if}</button>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          {/foreach}
          {else}
          <tr>
            <td colspan="8"><h4 style="text-align:center">There is no record</h4></td>
          </tr>
          {/if}
        </tbody>
      </table>
      {include file="common/paginate.tpl"}
    </div><!--table-responsive  -->
  </div><!--End panel-heading-->
</div><!--End panel panel-primary-->

{/block}

{block name="javascript"}
<script>
  function getCodePwd(sel)
  {
    //show Loading gif
    $(".loader").show();
    $.ajax({
      type: "GET",
      url: "{$admin_file}?task=patient&action=get_code_pwd&psy_id="+sel.value,
      dataType:'json',
      success: function(data) {
        console.log(data);
        if(data == false)
        {
          $('#code_pat').text('');
        } else {
          $('#code_pat').text(': '+data.code_pat);
        }
        //hide Loading gif
        $(".loader").hide();
      },
      error: function(){
        //Show error here
        alert("Cannot get data. Please try again later.");
        location.reload();
      }
    });//End Ajax
  }
</script>
{/block}
