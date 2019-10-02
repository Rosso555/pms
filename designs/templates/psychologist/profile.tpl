{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$psychologist_file}"><i class="fa fa-fw fa-home"></i></a></li>
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
        <div id="demo">
          <form action="{$psychologist_file}?task=profile" method="post">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="first_name"><span style="color:red">*</span> First Name:</label>
                  {if $error.first_name}<span style="color:red">Please enter first name!</span>{/if}
                  <input type="text" class="form-control" id="first_name" placeholder="Enter first name" name="first_name" value="{if $editPsychologist.first_name}{$editPsychologist.first_name}{else}{$smarty.session.psychologist.first_name}{/if}">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="last_name"><span style="color:red">*</span> Last Name:</label>
                  {if $error.last_name}<span style="color:red">Please enter last name!</span>{/if}
                  <input type="text" class="form-control" id="last_name" placeholder="Enter last name" name="last_name" value="{if $editPsychologist.last_name}{$editPsychologist.last_name}{else}{$smarty.session.psychologist.last_name}{/if}">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="pwd"><span style="color:red">*</span> Password:</label>
                  {if $error.password}<span style="color:red">Please enter password!</span>{/if}
                  <input type="text" class="form-control" id="pwd" placeholder="Enter password" name="password" value="{if $editPsychologist.password}{$editPsychologist.password}{else}{$smarty.session.psychologist.password}{/if}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="village"><span style="color:red">*</span> Village:</label>
                  {if $error.village}<span style="color:red">Please enter village!</span>{/if}
                  <input type="text" class="form-control" id="village" placeholder="" name="village" value="{if $editPsychologist.village_name}{$editPsychologist.village_name}{else}{$smarty.session.psychologist.village_name}{/if}">
                  <!-- <select class="form-control" name="village" id="village">
                    <option value="">-Select-</option>
                    {foreach from=$village item=v}
                    <option value="{$v.id}" {if $editPsychologist.village_id}{if $editPsychologist.village_id eq $v.id}selected{/if}{else}{if $smarty.session.psychologist.village eq $v.id}selected{/if}{/if}>{$v.name}</option>
                    {/foreach}
                  </select> -->
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="gender"><span style="color:red;">*</span> Gender:</label>
                  {if $error.gender}<span style="color:red">Please select gender!</span>{/if}
                  <select class="form-control" name="gender" id="gender">
                    <option value="1">Male</option>
                    <option value="2">Female</option>
                    <option value="3">Other</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="age"><span style="color:red;">*</span> Age:</label>
                  {if $error.age}<span style="color:red">Please enter age!</span>{/if}
                  <input type="text" name="age" class="form-control" value="{if $editPsychologist.age}{$editPsychologist.age}{else}{$smarty.session.psychologist.age}{/if}" id="age">
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
                  <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> Update</button>
                </div>
              </div>
            </div>
          </form>
        </div><!--End collapse-->
      </div><!--End panel-heading-->
    </div><!--End panel panel-primary-->
  </div><!--End panel-heading-->
</div><!--End panel panel-primary-->

{/block}
