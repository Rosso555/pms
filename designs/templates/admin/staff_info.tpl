{extends file="admin/layout.tpl"}
{block name="main"}
<div class="row">
  <div class="col-lg-12">
    <ul class="breadcrumb">
      <li><a href="{$admin_file_name}"><i class="fa fa-fw fa-home"></i></a></li>
      <li class="active">Staff Information</li>
    </ul>
    <div class="panel panel-primary">
      <div class="panel-heading"><h3 class="panel-title">Staff Information</h3></div>
        <div class="panel-body">
          <div class="panel panel-default">
  					<div class="panel-body">
              <form class="form-inline" role="form" action="{$admin_file_name}" method="GET">
              <div class="form-group">
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                  <i class="fa fa-plus-circle"></i> Add Profiles
                </button>
              </div>
              <div class="form-group" float="right">
                <input type="hidden" name="task" value="staff_info"/>
                <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd}" />
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
              </div>
            </form>
            </div>
          </div>
          <form class="form" role="form" action="{$admin_file_name}?task=staff_info" method="post" enctype="multipart/form-data">
            {if $error Or $result.id}
            <div class="collapse in" id="collapseExample">
            {else}
            <div class="collapse" id="collapseExample">
            {/if}
              <div class="form-group">
                <label>Full Name:</label><span class="text-danger"> *{if $error.name eq 1}Please write name.{/if}</span>
                <input type="text" class="form-control" name="name" value="{if $smarty.session.staff_info.name|escape}{$smarty.session.staff_info.name|escape}{/if}" />
              </div>
              <div class="form-group">
                <label>Pasword: </label>
                <span class="text-danger">
                  {if $error.pass eq 1}Please write password.{/if}
                  {if $error.password eq 2}Your password is not valid.{/if}
                </span>
                <input type="password" id='password' class="form-control" name="password" value="{if $smarty.session.staff_info.password|escape}{$smarty.session.staff_info.password|escape}{/if}" />
                <input type="checkbox" onchange="document.getElementById('password').type = this.checked ? 'text' : 'password'"> Show password
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-danger">* More than 8 character with number and letter</span>
              </div>
              <label>Gender</label><span class="text-danger"> *{if $error.gender}Please choice gender.{/if}</span>
              <div class="radio">
                <label class="radio-inline">
                  <input type="radio" name="gender" id="inlineRadio1" value="1"{if $result.gender eq 1}checked{/if}{if $smarty.session.staff_info.gender eq 1}checked{/if} checked> Male
                </label>
                <label class="radio-inline">
                  <input type="radio" name="gender" id="inlineRadio2" value="2"{if $result.gender eq 2}checked{/if}{if $smarty.session.staff_info.gender eq 2}checked{/if}> Female
                </label>
              </div>
              <div class="form-group">
                <label>Phone Number:</label><span class="text-danger"> *{if $error.phone eq 1}Please write phone number.{/if}</span>
                <input type="text" class="form-control" name="phone" value="{if $smarty.session.staff_info.phone|escape}{$smarty.session.staff_info.phone|escape}{/if}" placeholder="+855 123 456" />
              </div>
              <div class="form-group">
                <label>staff Role</label><span class="text-danger"> *{if $error.staff_role eq 1}Please choice position.{/if}</span>
                <select class="form-control select2" name="staff_role" style="width:100%">
                  <option value="">Choice staff role</option>
                  {foreach item=v from=$staff_role}
                  <option value="{$v.id}" {if $result.staff_role_id eq $v.id} selected{/if}{if $smarty.session.staff_info.staff_role eq $v.id}selected{/if}>{$v.name}</option>
                  {/foreach}
                </select>
              </div>
              <div class="row">
                <div class="col-md-2">
                <div class="form-group">
                  <label>Upload photo :</label><span class="text-danger">{if $error.no_image}Please insert photo.{/if}</span>
                  <input type="file" name="fileToUpload" id="file" onchange="readURL(this);" />
                </div>
              </div>
              <div class="col-md-9">
              <img class="img-thumbnail img-middle" id="blah" src="{if $edit.id}/images/org/thumbnail__{$edit.logo} {else} /images/user-default.png {/if}" width="100" style="height:103px">
              </div>
              </div>
              <div class="form-group">
                {if $result.id}
                  <input type="hidden" name="id" value="{$result.id}">
                  <button type="submit" class="btn btn-success"><i class="fa fa-check-circle-o"></i> Update</button>
                  <a href="{$admin_file_name}?task=staff_info&amp;action=add" class="btn btn-danger"><i class="fa fa-close"></i> Cancel</a>
                {else}
                  <button type="submit" class="btn btn-info"><i class="fa fa-check-circle-o"></i> Save</button>
                {/if}
              </div>
            </div>
          </form>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr bgcolor="#eeeeee">
                  <th>Full Name</th>
                  <th>Gender</th>
                  <th>Position</th>
                  <th>Phone Number</th>
                  <th>Status</th>
                  <th class="text-center" width="100px">Action</th>
                </tr>
              </thead>
            <tbody>
              {foreach from=$list_staff item= v }
              <tr>
                <td valign="top">{$v.name}</td>
                <td valign="top">{if $v.gender eq 1}Male{else}Female{/if}</td>
                <td valign="top">{$v.role_name}</td>
                <td valign="top">{$v.phone}</td>
                <td>{if $v.status eq 0}
                <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#myModal_{$v.id}" data-toggle1="tooltip" data-placement="top" title="Click to change"><i class="fa fa-circle" aria-hidden="true"></i> Active</button>
                {elseif $v.status eq 1}
                <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$v.id}" data-toggle1="tooltip" data-placement="top" title="Click to change"><i class="fa fa-square" aria-hidden="true"></i> Stoped</button>
                {/if}
                <!-- Trigger the modal with a button -->
                <div class="modal fade" id="myModal_{$v.id}" role="dialog">
                  <div class="modal-dialog">
                    <div class="panel panel-primary modal-content">
                      <div class="panel-heading modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="panel-title modal-title">Confirmation</h4>
                      </div>
                      <div class="modal-body">
                        <p>{if $v.status eq 1}Are you sure you want change status to activity?
                          {elseif $v.status eq 0}Are you sure you want change status to stop?{/if}</p>
                      </div>
                      <div class="modal-footer">
                        <a href="{$admin_file}?task=staff_info&amp;action=change_status&amp;id={$v.id|escape}&amp;status={$v.status|escape}" class="btn btn-danger btn-md"><i class="fa fa-check-circle-o"></i> Yes</a>
                        <button type="button" class="btn btn-primary collapsed" data-dismiss="modal"><i class="fa fa-remove"></i> Close</button>
                      </div>
                    </div>
                  </div>
                </div>
              <!-- Modal -->
              </td>
                <td class="text-center" valign="top">
                  <button href="#View_Profile_{$v.id}" class= "btn btn-info btn-xs" data-toggle1="tooltip" data-placement="top" title="View Profile" data-toggle= "modal">
   								<i class="fa fa-eye"></i></button>
                  <!-- Modal -->
                  <div class="modal fade" id="View_Profile_{$v.id}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title" id="myModalLabel">Profile Information</h4>
                        </div>
                        <div class="modal-body">
                        <img class="img-circle" src="/image/thumbnail__{$v.photo}" width="20%">
                         <dl class="dl-horizontal">
                            <dt>Full Name:</<dt>
                            <dd class="text-left">{$v.name}</dd>
                            <dt>Gender</</dt>
                            <dd class="text-left">{if $v.gender eq 1}Male{else}Female{/if}</dd>
                            <dt>Brith date</dt>
                            <dd class="text-left">{$v.birth_date}</dd>
                            <dt>Nationality</dt>
                            <dd class="text-left">{$v.nationality}</dd>
                            <dt>Education</dt>
                            <dd class="text-left">{$v.highest_education}</dd>
                            <dt>Position</dt>
                            <dd class="text-left">{$v.title}</dd>
                            <dt>Marital Status</dt>
                            <dd class="text-left">{if $v.marital_status eq 3}Single{else}Married{/if}</dd>
                            <dt>Current Address</dt>
                            <dd class="text-left">{$v.current_address}</dd>
                          </dl>
                        <legend>Account Information</legend>
                        <label>Email    :</label> {$v.email}<br />
                        <label>Password :</label> {$v.password}
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                        </div>
                      </div>
                    </div>
                  </div>
                  <a href="{$admin_file_name}?task=staff_info&amp;action=add&amp;id={$v.id}" class="btn btn-success btn-xs"
                     data-toggle1="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i>
                  </a>
                </td>
              </tr>
              {/foreach}
            </tbody>
          </table>
        </div>
          {include file = "common/paginate.tpl"}
      </div>
    </div>
{/block}
{block name="javascript"}
<script>
$('#birth_date').datetimepicker({ locale: 'en', format: 'YYYY-MM-DD' });
 $('[data-toggle="popover"]').popover();
	$(function () {
    $('[data-toggle1="tooltip"]').tooltip()
  });
</script>
{/block}
