{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang:text_topic){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
{if $smarty.cookies.checkTopic}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot delete "<strong>{$smarty.cookies.checkTopic}</strong>" because it has been used.
  </div>
{/if}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title"> {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang:text_topic){/if}</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        {if $error}
        <div class="row">
          <div class="col-md-12">
            <span style="color: red">* {if $multiLang.text_please_input}{$multiLang.text_please_input}{else}No Translate(Key Lang: text_please_input){/if} {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang: text_topic){/if} {if $multiLang.text_name}{$multiLang.text_name}{else}No Translate(Key Lang: text_name){/if}.</span>
          </div>
        </div>
        {/if}
        <div class="row">
          <div class="col-md-6">
            {if $getTopicByID.id}
            <form class="form-inline" action="{$admin_file}?task=topic&amp;action=edit&amp;id={$getTopicByID.id}" method="post">
            {else}
            <form class="form-inline" action="{$admin_file}?task=topic&amp;action=add" method="post">
            {/if}
              <div class="form-group">
                <label for="name">{if $multiLang.text_name}{$multiLang.text_name}{else}No Translate(Key Lang:text_name){/if}:</label>
                <input type="text" name="name" class="form-control" value="{$getTopicByID.name}" id="name" placeholder="Enter Topic" required autofocus>
              </div>
              {if $getTopicByID.id}
              <input type="hidden" name="id" value="{$getTopicByID.id}" />
              <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
              <a href="{$admin_file}?task=topic" class="btn btn-danger"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
              {else}
              <button type="submit" class="btn btn-info"><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate(Key Lang: button_save){/if}</button>
              {/if}
            </form>
          </div>
          <div class="col-md-6">
            <form class="form-inline" action="{$admin_file}?task=topic" method="get">
              <input type="hidden" name="task" value="topic">
              <div class="input-group" style="float: right;">
                <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="">
                <span class="input-group-btn">
                  <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate(Key Lang:button_search){/if}</button>
                </span>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div><!--panel panel-body-->
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
          <th>{if $multiLang.text_name}{$multiLang.text_name}{else}No Translate(Key Lang:text_name){/if}</th>
          <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang:text_action){/if}</th>
          </tr>
        </thead>
        {if $listTopic|@count gt 0}
        <tbody>
        {foreach from = $listTopic item = data key=k}
          <tr>
            <td>{$data.name}</td>
            <td>
              <a href="{$admin_file}?task=topic&amp;action=edit&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang:button_edit){/if}"><i class="fa fa-edit"></i></a>
              <!-- Trigger the modal with a button -->
              <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$data.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang:button_delete){/if}"><i class="fa fa-trash-o"></i></button>
              <!-- Modal -->
              <div class="modal fade" id="myModal_{$data.id}" role="dialog">
                <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="panel panel-primary modal-content">
                    <div class="panel-heading modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate(Key Lang:text_confirmation){/if}</h4>
                    </div>
                    <div class="modal-body">
                      <p>{if $multiLang.text_confirmation_delete}{$multiLang.text_confirmation_delete}{else}No Translate(Key Lang:text_confirmation_delete){/if} {if $multiLang.text_topic}{$multiLang.text_topic}{else}No Translate(Key Lang:text_topic){/if} <b>({$data.name|escape})</b> ?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=topic&amp;action=delete&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"></i> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang:button_delete){/if}</a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang:button_close){/if}</button>
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
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
