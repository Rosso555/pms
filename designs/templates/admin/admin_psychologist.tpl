{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>Psychologist</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate (Key Lang:text_edit){/if}</li>
  {/if}
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h3 class="panel-title">Psychologist.</h3></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-12">
            <form class="form-inline" action="{$admin_file}?task=psychologist" method="get">
              <input type="hidden" name="task" value="psychologist">
              <div class="form-group" style="margin-bottom:5px;">
                <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                  <i class="fa fa-plus-circle"></i> Add Psychologist
                </button>
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <select class="form-control select2" name="psy_id">
                  <option value="">---Select Psychologist---</option>
                  {foreach from=$listPsychologist item=v}
                  <option value="{$v.id}" {if $smarty.get.psy_id eq $v.id}selected{/if}>{$v.username}</option>
                  {/foreach}
                </select>
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <select class="form-control select2" name="status">
                  <option value="">---Select Status---</option>
                  <option value="1" {if $smarty.get.status eq 1}selected{/if}>Unconfirm Or Stop</option>
                  <option value="2" {if $smarty.get.status eq 2}selected{/if}>Confirm Or Active</option>
                </select>
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <input type="text" class="form-control" placeholder="example@domain.com" name="kwd" value="{$smarty.get.kwd}">
                <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
              </div>
            </form>
          </div>
        </div>
        <div id="demo" class="collapse {if $error OR $editPsychologist.id}in{/if}">
          {if $editPsychologist.id}
          <form action="{$admin_file}?task=psychologist&amp;action=edit&amp;id={$editPsychologist.id}" method="post">
          {else}
          <form action="{$admin_file}?task=psychologist&amp;action=add" method="post">
          {/if}
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="username"><span style="color:red">*</span> Name:</label>
                  {if $error.username}<span style="color:red">Please enter username!</span>{/if}
                  <input type="text" class="form-control" id="username" placeholder="Enter name" name="username" value="{if $editPsychologist.username}{$editPsychologist.username}{else}{$smarty.session.psychologist.username}{/if}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="pwd"><span style="color:red">*</span> Password:</label>
                  {if $error.password}<span style="color:red">Please enter password!</span>{/if}
                  <input type="text" class="form-control" id="pwd" placeholder="Enter password" name="password" value="{if $editPsychologist.password}{$editPsychologist.password}{else}{$smarty.session.psychologist.password}{/if}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email"><span style="color:red">*</span> Email:</label>
                  {if $error.email}<span style="color:red">Please enter email!</span>{/if}
                  {if $error.invalid_email}<span style="color:red">Your email is not valid!</span>{/if}
                  {if $error.exist_email}<span style="color:red">Your email is existed!</span>{/if}
                  <input type="email" class="form-control" id="email" placeholder="example@domain.com" name="email" value="{if $editPsychologist.email}{$editPsychologist.email}{else}{$smarty.session.psychologist.email}{/if}">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="job"><span style="color:red">*</span> Job:</label>
                  {if $error.job}<span style="color:red">Please enter job!</span>{/if}
                  <input type="text" class="form-control" id="job" placeholder="Enter job" name="job" value="{if $editPsychologist.job}{$editPsychologist.job}{else}{$smarty.session.psychologist.job}{/if}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="address"><span style="color:red">*</span> Address:</label>
                  {if $error.address}<span style="color:red">Your email is address !!!</span>{/if}

                  <textarea class="form-control" rows="4" id="address" name="address">{if $editPsychologist.address}{$editPsychologist.address}{else}{$smarty.session.psychologist.address}{/if}</textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  {if $editPsychologist.id}
                    <input type="hidden" name="id" value="{$editPsychologist.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> Update</button>
                    <a href="{$admin_file}?task=psychologist" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> Cancel</a>
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
            <th>Email</th>
            <th>Password</th>
            <th>Status</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
        {if $listPsychologistData|@count gt 0}
          {foreach from=$listPsychologistData item=v}
          <tr>
            <td>{$v.username}</td>
            <td>{$v.email}</td>
            <td>{$v.password}</td>
            <td>
              {if $v.status eq 1}
              <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#status_{$v.id}" data-toggle1="tooltip" data-placement="top" title="Click Change Status">
                <i class="fa fa-stop-circle-o" aria-hidden="true"></i> Unconfirm Or Stop
              </button>
              {else}
              <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#status_{$v.id}" data-toggle1="tooltip" data-placement="top" title="Click Change Status">
                <i class="fa fa-check-circle"></i> Confirm Or Active
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
                        Are you sure you want to change status to <b>{if $v.status eq 2}Stop{else}Active{/if}</b>?
                      </p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=psychologist&amp;action=change_status&amp;id={$v.id|escape}&amp;status={$v.status|escape}" class="btn btn-danger btn-md" style="color: white;">
                        <i class="fa fa-check-circle-o"></i> Yes
                      </a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> Discard</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
            </td>
            <td class="text-center">
              <button href="#View_Profile_{$v.id}" class= "btn btn-info btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_view_pro}{$multiLang.button_view_pro}{else}No Translate (Key Lang:button_view_pro){/if}" data-toggle= "modal"><i class="fa fa-eye"></i></button>
              <!-- Modal -->
              <div class="modal fade" id="View_Profile_{$v.id}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel">{if $multiLang.text_pro_info}{$multiLang.text_pro_info}{else}No Translate (Key Lang:text_pro_info){/if}</h4>
                    </div>
                    <div class="modal-body">
                     <dl class="dl-horizontal">
                        <dt>{if $multiLang.text_title_name}{$multiLang.text_title_name}{else}No Translate (Key Lang:text_title_name){/if}:</dt>
                        <dd class="text-left">{$v.username}</dd>

                        <dt>{if $multiLang.text_password}{$multiLang.text_password}{else}No Translate (Key Lang:text_password){/if}:</dt>
                        <dd class="text-left">{$v.password}</dd>

                        <dt>{if $multiLang.text_email}{$multiLang.text_email}{else}No Translate (Key Lang:text_email){/if}:<dt>
                        <dd class="text-left">{$v.email}</dd>

                        <dt>{if $multiLang.text_job}{$multiLang.text_job}{else}No Translate (Key Lang:text_job){/if}:</dt>
                        <dd class="text-left">{$v.job}</dd>

                        <dt>{if $multiLang.text_address}{$multiLang.text_address}{else}No Translate (Key Lang:text_address){/if}:</dt>
                        <dd class="text-left">{$v.address}</dd>
                      </dl>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=psychologist&amp;action=edit&amp;id={$v.id}" class="btn btn-success"><i class="fa fa-edit"></i> {if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate (Key Lang:button_edit){/if}</a>
                      <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle" aria-hidden="true"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate (Key Lang:button_close){/if}</button>
                    </div>
                  </div>
                </div>
              </div>
              <a href="{$admin_file}?task=psychologist&amp;action=edit&amp;id={$v.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate (Key Lang:button_edit){/if}"><i class="fa fa-edit"></i></a>
              <!-- <button href="#myModal_{$v.id}" class= "btn btn-danger btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate (Key Lang:button_delete){/if}" data-toggle= "modal"><i class="fa fa-trash-o"></i></button>
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
                      <a href="{$admin_file}?task=psychologist&amp;action=delete&amp;id={$v.id}" class="btn btn-danger btn-md"><i class="fa fa-check-circle-o"></i> {if $multiLang.button_yes}{$multiLang.button_yes}{else}No Translate (Key Lang:button_yes){/if}</a>
                      <button type="button" class="btn btn-primary collapsed" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate (Key Lang:button_close){/if}</button>
                    </div>
                  </div>
                </div>
              </div> -->
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
