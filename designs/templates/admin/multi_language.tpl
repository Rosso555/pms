{extends file="common/layout.tpl"}
{block name="main"}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.multil_language_title}{$multiLang.multil_language_title}{else}No Translate(Key Lang: multil_language_title){/if}</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=multi_language" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="multi_language">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_translate}{$multiLang.button_add_translate}{else}No Translate (Key Lang: button_add_translate){/if}
            </button>
          </div>
          <div class="input-group" style="float: right;">
            <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="Example: add_question, button_save" style="width: 265px">
            <span class="input-group-btn">
              <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate (Key Lang: button_search){/if}</button>
            </span>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getMultilangByUID|@count > 0} in {/if}">
        {if $getMultilangByUID|@count > 0}
          <form action="{$admin_file}?task=multi_language&amp;action=edit&amp;unique_id={$smarty.get.unique_id}" method="post">
        {else}
          <form action="{$admin_file}?task=multi_language&amp;unique_id={$smarty.get.unique_id}" method="post">
        {/if}
            <div class="row">
            {foreach from=$language item=v}
              <div class="col-md-6">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate (Key Lang: text_title){/if} {$v.title}:</label>
                  {if $error.title_en}
                    <span style="color: red">Please input title {$v.title}.</span>
                  {/if}
                  {if $getMultilangByUID|@count > 0}
                    <input type="text" class="form-control" name="title[]" value="{foreach from=$getMultilangByUID item=ml}{if $ml.lang eq $v.lang_name}{$ml.title}{/if}{/foreach}" placeholder="Example: Title, Question, Add Question..." required>
                  {else}
                    <input type="text" class="form-control" name="title[]" value="{foreach from=$smarty.session.translate.title item=sv key=k}{if $smarty.session.translate.lang.$k eq $v.lang_name}{$smarty.session.translate.title.$k}{/if}{/foreach}" placeholder="Example: Title, Question, Add Question..." required>

                  {/if}
                  <input type="hidden" value="{$v.lang_name}" name="lang[]">
                  <input type="hidden" value="{$v.id}" name="language_id[]">
                  <input type="hidden" name="id[]" value="{foreach from=$getMultilangByUID item=ml}{if $ml.lang eq $v.lang_name}{$ml.id}{/if}{/foreach}"/>

                </div>
              </div>
            {/foreach}
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group" style="margin-bottom: 0px;">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.multi_language_key_lang}{$multiLang.multi_language_key_lang}{else}No Translate (Key Lang: multi_language_key_lang){/if}:</label>
                  {if $error.key_lang}
                    <span style="color: red"> Please input key lang.</span>
                  {/if}
                  {if $error.is_key_lang_exist}
                    <span style="color: red"> Key Language is exist already!</span>
                  {/if}
                  <input type="text" class="form-control" name="key_lang" value="{if $getMultilangByUID}{foreach from=$getMultilangByUID item=ml name=foo}{if $smarty.foreach.foo.first}{$ml.key_lang}{/if}{/foreach}{else}{if $smarty.session.translate.key_lang}{$smarty.session.translate.key_lang}{/if}{/if}" placeholder="Example: text_title, question_title, add_question..."/>
                </div>
                <span style="color: red">Note: Please input english key language. Example: text_title, button_save, button_delete...</span>
              </div>
            </div>
            <div class="row" style="margin-top: 10px;">
              <div class="col-md-12">
                <div class="form-group">
                  {if $getMultilangByUID|@count > 0}
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate (Key Lang: button_update){/if}</button>
                    <a href="{$admin_file}?task=multi_language" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate (Key Lang: button_cancel){/if}</a>
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
            <th>{if $multiLang.text_language}{$multiLang.text_language}{else}No Translate (Key Lang: text_language){/if}</th>
            <th>{if $multiLang.text_title}{$multiLang.text_title}{else}No Translate (Key Lang: text_title){/if}</th>
            <th>{if $multiLang.multi_language_key_lang}{$multiLang.multi_language_key_lang}{else}No Translate (Key Lang: multi_language_key_lang){/if}</th>
            <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate (Key Lang: text_action){/if}</th>
          </tr>
        </thead>
        {if $langs|@count gt 0}
        <tbody>
        {foreach from = $langs item = data key=k}
        {$q = $k+1} {$t = $q % $language|@count}
          <tr>
            <td>{$data.language_title}</td>
            <td>{$data.title}</td>
            <td>{$data.key_lang}</td>

            {if $t eq 1}
            <td rowspan="{$language|@count}" style="vertical-align: middle;">
              <a href="{$admin_file}?task=multi_language&amp;action=edit&amp;unique_id={$data.unique_id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate (Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
              <!-- Trigger the modal with a button -->
              <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$data.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate (Key Lang: button_delete){/if}"><i class="fa fa-trash-o"></i></button>
              <!-- Modal -->
              <div class="modal fade" id="myModal_{$data.id}" role="dialog">
                <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="panel panel-primary modal-content">
                    <div class="panel-heading modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="panel-title modal-title">Confirmation</h4>
                    </div>
                    <div class="modal-body">
                      <p>Are you sure you want to delete this title <b>({$data.title|escape})</b>.</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=multi_language&amp;action=delete&amp;unique_id={$data.unique_id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> Delete</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> Close</i></button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
            </td>
          {/if}

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
    <div class="pull-right"> {include file="common/paginate.tpl"}</div>
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
