{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_mailer_lite}{$multiLang.text_mailer_lite}{else}No Translate(Key Lang:text_mailer_lite){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
{if $smarty.cookies.checkmailerlite}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot delete "<strong>{$smarty.cookies.checkmailerlite}</strong>" because it has been used.
  </div>
{/if}
  <div class="panel panel-primary">
    <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_mailer_lite}{$multiLang.text_mailer_lite}{else}No Translate(Key Lang:text_mailer_lite){/if}</h4></div>
    <div class="panel-body">
      <div class="panel panel-default">
        <div class="panel-body">
          <form class="form-inline" role="form" action="{$admin_file}?task=mailerlite" method="GET" style="padding: 1px 0px 12px 1px;">
            <input type="hidden" name="task" value="mailerlite">
            <div class="form-group">
              <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_mailerlite}{$multiLang.button_add_mailerlite}{else}No Translate(Key Lang: button_add_mailerlite){/if}
              </button>
            </div>
            <div class="input-group" style="float: right;">
              <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="{if $multiLang.text_search_by}{$multiLang.text_search_by}{else}No Translate(Key Lang: text_search_by){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}">
              <span class="input-group-btn">
                <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate(Key Lang: button_search){/if}</button>
              </span>
            </div>
          </form>
          <div id="demo" class="collapse {if $error or $getMailerliteByID.id}in{/if}">
            {if $getMailerliteByID.id}
            <form action="{$admin_file}?task=mailerlite&amp;action=edit&amp;id={$getMailerliteByID.id}" method="post">
            {else}
            <form action="{$admin_file}?task=mailerlite" method="post">
            {/if}
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="title"><span style="color: red">*</span> {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}:</label>
                    {if $error.title}
                      <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}</span>
                    {/if}
                    <input type="text" name="title" class="form-control" placeholder="Title"
                    value="{if $smarty.session.mailerlite.title}{$smarty.session.mailerlite.title}{elseif $getMailerliteByID.title}{$getMailerliteByID.title}{/if}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="title"><span style="color: red">*</span>{if $multiLang.text_mailerlite_key}{$multiLang.text_mailerlite_key}{else}No Translate(Key Lang: text_mailerlite_key){/if} :</label>
                    {if $error.api_key}
                      <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_mailerlite_key}{$multiLang.text_mailerlite_key}{else}No Translate(Key Lang: text_mailerlite_key){/if}</span>
                    {/if}
                    <input type="text" name="api_key" class="form-control" placeholder="Key"
                    value="{if $smarty.session.mailerlite.api_key}{$smarty.session.mailerlite.api_key}{elseif $getMailerliteByID.api_key}{$getMailerliteByID.api_key}{/if}">
                  </div>
                </div>
              </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      {if $getMailerliteByID.id}
                        <input type="hidden" name="id" value="{$getMailerliteByID.id}" />
                        <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                        <a href="{$admin_file}?task=mailerlite" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
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
            <th>{if $multiLang.text_mailerlite_key}{$multiLang.text_mailerlite_key}{else}No Translate(Key Lang: text_mailerlite_key){/if}</th>
            <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
            </tr>
          </thead>
          {if $listMailerlite|@count gt 0}
          <tbody>
          {foreach from = $listMailerlite item = mailerlite key=k}
            <tr>
              <td>{$mailerlite.title}</td>
              <td>{$mailerlite.api_key}</td>
              <td>
                <a href="{$admin_file}?task=mailerlite&amp;action=edit&amp;id={$mailerlite.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
                <!-- Trigger the modal with a button -->
                <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$mailerlite.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}"><i class="fa fa-trash-o"></i></button>
                <!-- Modal -->
                <div class="modal fade" id="myModal_{$mailerlite.id}" role="dialog">
                  <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="panel panel-primary modal-content">
                      <div class="panel-heading modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate(Key Lang: text_confirmation){/if}</h4>
                      </div>
                      <div class="modal-body">
                        <p>
                          {if $multiLang.text_confirmation_delete}{$multiLang.text_confirmation_delete}{else}No Translate(Key Lang:text_confirmation_delete){/if} {if $multiLang.text_mailer_lite}{$multiLang.text_mailer_lite}{else}No Translate(Key Lang:text_mailer_lite){/if}
                          <b>({$mailerlite.title|escape})</b>?
                        </p>
                      </div>
                      <div class="modal-footer">
                        <a href="{$admin_file}?task=mailerlite&amp;action=delete&amp;id={$mailerlite.id}" class="btn btn-danger btn-md" style="color: white;">
                          <i class="fa fa-trash-o"></i> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}
                        </a>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Modal -->
                <!-- <a href="{$admin_file}?task=mailerlitegroup&amp;mlid={$mailerlite.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_mailerlitegroup}{$multiLang.button_add_mailerlitegroup}{else}No Translate(Key Lang: button_add_mailerlitegroup){/if}"><i class="fa fa-plus-circle" aria-hidden="true"></i></a> -->
                <a href="{$admin_file}?task=apitransaction&amp;mlid={$mailerlite.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_add_transaction}{$multiLang.button_add_transaction}{else}No Translate(Key Lang: button_add_transaction){/if}"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>

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
