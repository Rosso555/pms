{extends file="common/layout.tpl"}
{block name="main"}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.language_header}{$multiLang.language_header}{else}No Translate (Key Lang:language_header){/if}</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=add_language" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="add_language">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_lanaguage}{$multiLang.button_add_lanaguage}{else}No Translate (Key Lang:button_add_lanaguage){/if}
            </button>
          </div>
          <div class="input-group" style="float: right;">
            <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="Example: English...">
            <span class="input-group-btn">
              <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang: button_search){/if}</button>
            </span>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getLanguageByID|@count > 0} in {/if}">
          {if $getLanguageByID.id}
          <form action="{$admin_file}?task=add_language&amp;action=edit&amp;id={$getLanguageByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=add_language&amp;action=add" method="post">
          {/if}
            <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="title"><span style="color: red">*</span> {if $multiLang.text_title_language}{$multiLang.text_title_language}{else}No Translate (Key Lang: text_title_language){/if}:</label>
                    {if $error.title}
                      <span style="color: red">Please input title.</span>
                    {/if}
                    <input type="text" class="form-control" name="title" value="{$getLanguageByID.title}" placeholder="Example: English, German" required>
                  </div>
                </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_short_language}{$multiLang.text_short_language}{else}No Translate (Key Lang:text_short_language){/if}:</label>
                  {if $error.language}
                    <span style="color: red">Please input short language.</span>
                  {/if}
                  {if $error.is_lang_name_exist eq 2}
                    <span style="color: red">Short language is exist.</span>
                  {/if}
                  <input type="text" class="form-control" name="language" value="{$getLanguageByID.lang_name}" placeholder="Example: en, de" required>
                </div>
                <span style="color: red">Note: Please input short Language. Example: (English:en), (German:de), (French:fr), (Khmer:km)</span>
            </div>
            </div>
            <div class="row" style="margin-top: 10px;">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getLanguageByID.id}
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate (Key Lang: button_update){/if}</button>
                    <input type="hidden" name="id" value="{$getLanguageByID.id}">
                    <a href="{$admin_file}?task=add_language" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate (Key Lang: button_cancel){/if}</a>
                  {else}
                    <button type="submit" name="butsubmit" class="btn btn-info"><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate (Key Lang: button_save){/if}</button>
                  {/if}
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div><!--panel panel-body-->
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr bgcolor="#eeeeee">
            <th>{if $multiLang.text_title_language}{$multiLang.text_title_language}{else}No Translate (Key Lang: text_title_language){/if}</th>
            <th>{if $multiLang.text_short_language}{$multiLang.text_short_language}{else}No Translate (Key Lang: text_short_language){/if}</th>
            <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate (Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $listLanguage|@count gt 0}
        <tbody>
        {foreach from = $listLanguage item = data key=k}
          <tr>
            <!-- <td>{if $data.lang eq 'en'}English{elseif $data.lang eq 'ge'}German{/if}</td> -->
            <td>{$data.title}</td>
            <td>{$data.lang_name}</td>
            <td style="vertical-align: middle;">
              <a href="{$admin_file}?task=add_language&amp;action=edit&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate (Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
              <!-- Trigger the modal with a button -->
              <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$data.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate (Key Lang: button_delete){/if}"><i class="fa fa-trash-o"></i></button>
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
                      <p>Are you sure you want to delete this title <b>({$data.title|escape})</b>.</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=add_language&amp;action=delete&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate (Key Lang:button_delete){/if}</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate (Key Lang:button_close){/if}</i></button>
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
