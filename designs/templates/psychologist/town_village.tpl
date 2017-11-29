{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$psychologist_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>Town/Village</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
{if $smarty.cookies.checkVillage}
<div class="alert alert-danger">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  Sorry! you cannot delete "<strong>{$smarty.cookies.checkVillage}</strong>" because it has been used.
</div>
{/if}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_town_village}{$multiLang.text_town_village}{else}No Translate (Key Lang:text_town_village){/if}</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-sm-12">
            <form class="form-inline" action="{$psychologist_file}?task=town_village" method="get">
              <input type="hidden" name="task" value="town_village">
              <div class="form-group" style="margin-bottom:5px;">
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                  <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_town_village}{$multiLang.button_add_town_village}{else}No Translate (Key Lang:button_add_town_village){/if}
                </button>
              </div>
              &nbsp;&nbsp;&nbsp;
              <div class="form-group" style="margin-bottom:5px;">
                <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="">
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <span class="form-group-btn">
                  <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang:button_search){/if}</button>
                </span>
              </div>
            </form>
          </div>
          <div class="col-md-12">
            <div class="collapse {if $error Or $getVillageByID.id}in{/if}" id="collapseExample" style="margin-top: 10px;">
              {if $getVillageByID.id}
              <form class="form" role="form" action="{$psychologist_file}?task=town_village&amp;action=edit&amp;id=$getVillageByID.id" method="post">
                {else}
                <form class="form" role="form" action="{$psychologist_file}?task=town_village&amp;action=add" method="post">
                  {/if}
                  <div class="form-group">
                    <label for="name">{if $multiLang.text_name}{$multiLang.text_name}{else}No Translate (Key Lang:text_name){/if} {if $multiLang.text_town_village}{$multiLang.text_town_village}{else}No Translate (Key Lang:text_town_village){/if}:</label>
                    <input type="text" name="name" class="form-control" value="{$getVillageByID.name}" id="name" placeholder="" required>
                  </div>
                  {if $getVillageByID.id}
                  <div class="form-group" style="margin-bottom:5px;">
                    <input type="hidden" name="id" value="{$getVillageByID.id}" />
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate (Key Lang:button_update){/if}</button>
                    <a href="{$psychologist_file}?task=town_village" class="btn btn-danger"><i class="fa fa-close"></i>  {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate (Key Lang:button_cancel){/if}</a>
                  </div>
                  {else}
                  <div class="form-group" style="margin-bottom:5px;">
                    <button type="submit" class="btn btn-info"><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate (Key Lang:button_save){/if}</button>
                  </div>
                  {/if}
                </form>
              </div>
            </div>
          </div>
        </div>
      </div><!--panel panel-body-->
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr bgcolor="#eeeeee">
              <th>{if $multiLang.text_name}{$multiLang.text_name}{else}No Translate (Key Lang:text_name){/if} {if $multiLang.text_town_village}{$multiLang.text_town_village}{else}No Translate (Key Lang:text_town_village){/if}</th>
              <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate (Key Lang:text_action){/if}</th>
            </tr>
          </thead>
          {if $listVillage|@count gt 0}
          <tbody>
            {foreach from = $listVillage item = data key=k}
            <tr>
              <td>{$data.name}</td>
              <td>
                <a href="{$psychologist_file}?task=town_village&amp;action=edit&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate (Key Lang:button_edit){/if}"><i class="fa fa-edit"></i></a>
                <!-- Trigger the modal with a button -->
                <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$data.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate (Key Lang:button_delete){/if}"><i class="fa fa-trash-o"></i></button>
                <!-- Modal -->
                <div class="modal fade" id="myModal_{$data.id}" role="dialog">
                  <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="panel panel-primary modal-content">
                      <div class="panel-heading modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate (Key Lang:text_confirmation){/if}</h4>
                      </div>
                      <div class="modal-body">
                        <p>Are you sure you want to delete this town/village <b>({$data.name|escape})</b> ?</p>
                      </div>
                      <div class="modal-footer">
                        <a href="{$psychologist_file}?task=town_village&amp;action=delete&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_yes}{$multiLang.button_yes}{else}No Translate (Key Lang:button_yes){/if}</i></a>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate (Key Lang:button_cancel){/if}</i></button>
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
            <td colspan="7"><h4 style="text-align:center">There is no record</h4></td>
          </tr>
          {/if}
        </table>
      </div><!--table-responsive  -->
      {include file="common/paginate.tpl"}
    </div><!--end panel-body  -->
  </div><!--end panel panel-primary  -->
  {/block}
