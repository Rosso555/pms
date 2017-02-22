{extends file="admin/layout.tpl"}
{block name="main"}
{if $smarty.cookies.checkQuestion}
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Sorry! you cannot delete "<strong>{$smarty.cookies.checkQuestion}</strong>" because it has been used.
  </div>
{/if}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li class="active">{if $multiLang.text_question}{$multiLang.text_question}{else}No Translate (Key Lang:text_question){/if}</li>
</ul>
  <div class="panel panel-primary">
    <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_question}{$multiLang.text_question}{else}No Translate(Key Lang: text_question){/if}</h4></div>
    <div class="panel-body">
      <div class="panel panel-default">
        <div class="panel-body">
          <form class="form-inline" role="form" action="{$admin_file}?task=question" method="GET" style="padding: 1px 0px 12px 1px;">
            <input type="hidden" name="task" value="question">
            <div class="form-group">
              <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_question}{$multiLang.button_add_question}{else}No Translate(Key Lang: button_add_question){/if}
              </button>
            </div>
            <div class="input-group" style="float: right;">
              <input type="text" class="form-control" name="kwd" value="{$smarty.get.kwd|escape}" placeholder="{if $multiLang.text_search_by}{$multiLang.text_search_by}{else}No Translate(Key Lang: text_search_by){/if} {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}...">
              <span class="input-group-btn">
                <button class="btn btn-info" type="submit"><i class="fa fa-search"></i> {if $multiLang.button_search}{$multiLang.button_search}{else}No Translate(Key Lang: button_search){/if}</button>
              </span>
            </div>
          </form>
          <div id="demo" class="collapse {if $error or $getQuestionByID.id}in{/if}">
            {if $getQuestionByID.id}
            <form action="{$admin_file}?task=question&amp;action=edit&amp;id={$getQuestionByID.id}" method="post">
            {else}
            <form action="{$admin_file}?task=question" method="post">
            {/if}
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label for="title"><span style="color: red">*</span> {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate(Key Lang: text_title){/if}:</label>
                    {if $error.title}
                      <span style="color: red">{if $multiLang.text_title_empty}{$multiLang.text_title_empty}{else}No Translate(Key Lang: text_title_empty){/if}</span>
                    {/if}
                    <input type="text" name="title" class="form-control" placeholder="Title"
                    value="{if $smarty.session.question.title}{$smarty.session.question.title}{elseif $getQuestionByID.title}{$getQuestionByID.title}{/if}">
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <label for="title"><span style="color: red">*</span> {if $multiLang.text_type}{$multiLang.text_type}{else}No Translate(Key Lang: text_type){/if}:</label>
                    {if $error.type}
                      <span style="color: red">{if $multiLang.text_please_select}{$multiLang.text_please_select}{else}No Translate(Key Lang: text_please_select){/if} {if $multiLang.text_type}{$multiLang.text_type}{else}No Translate(Key Lang: text_type){/if}</span>
                    {/if}
                    <select class="form-control" name="type">
                      <option value="">---{if $multiLang.text_select}{$multiLang.text_select}{else}No Translate(Key Lang: text_select){/if} {if $multiLang.text_type}{$multiLang.text_type}{else}No Translate(Key Lang: text_type){/if}---</option>
                      <option value="1" {if $smarty.session.question.type}{if $smarty.session.question.type eq 1} selected {/if}{else}{if $getQuestionByID.type eq 1} selected {/if}{/if}>Text Input</option>
                      <option value="2" {if $smarty.session.question.type}{if $smarty.session.question.type eq 2} selected {/if}{else}{if $getQuestionByID.type eq 2} selected {/if}{/if}>Textarea</option>
                      <option value="3" {if $smarty.session.question.type}{if $smarty.session.question.type eq 3} selected {/if}{else}{if $getQuestionByID.type eq 3} selected {/if}{/if}>Radio</option>
                      <option value="4" {if $smarty.session.question.type}{if $smarty.session.question.type eq 4} selected {/if}{else}{if $getQuestionByID.type eq 4} selected {/if}{/if}>Check</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <label for="title" style="margin-bottom: 0px;"> {if $multiLang.text_email}{$multiLang.text_email}{else}No Translate(Key Lang: text_email){/if}:</label>
                  <div class="checkbox box" style="margin-top: 5px;">
                    <label><input type="checkbox" value="1" name="is_email" {if $smarty.session.question.is_email}{if $smarty.session.question.is_email eq 1} checked {/if}{else}{if $getQuestionByID.is_email eq 1}checked{/if}{/if}>{if $multiLang.text_yes}{$multiLang.text_yes}{else}No Translate(Key Lang: text_yes){/if}</label>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label><span style="color: red">*</span> {if $multiLang.text_description}{$multiLang.text_description}{else}No Translate(Key Lang: text_description){/if}:</label>
                    {if $error.desc}
                      <span style="color: red">{if $multiLang.text_description_empty}{$multiLang.text_description_empty}{else}No Translate(Key Lang: text_description_empty){/if}</span>
                    {/if}
                    <textarea class="form-control" name="description" rows="5" placeholder="Write something" style="overflow:auto; resize:none;">{if $smarty.session.question.description}{$smarty.session.question.description}{elseif $getQuestionByID.description}{$getQuestionByID.description}{/if}</textarea>
                  </div>
                </div>
              </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      {if $getQuestionByID.id}
                        <input type="hidden" name="id" value="{$getQuestionByID.id}" />
                        <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                        <a href="{$admin_file}?task=question" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
                      {else}
                        <button type="submit" name="butsubmit" class="btn btn-danger"><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate(Key Lang: button_save){/if}</button>
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
            <th>{if $multiLang.text_type}{$multiLang.text_type}{else}No Translate(Key Lang: text_type){/if}</th>
            <th>{if $multiLang.text_email}{$multiLang.text_email}{else}No Translate(Key Lang: text_email){/if}</th>
            <th>{if $multiLang.text_description}{$multiLang.text_description}{else}No Translate(Key Lang: text_description){/if}</th>
            <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang: text_action){/if}</th>
            </tr>
          </thead>
          {if $listQuestion|@count gt 0}
          <tbody>
          {foreach from = $listQuestion item = question key=k}
            <tr>
              <td>{$question.title}</td>
              <td>{if $question.type eq 1}Text Input{elseif $question.type eq 2}Textarea{elseif $question.type eq 3}Radio{else}Check{/if}</td>
              <td>{if $question.is_email eq 1}Yes{else}No{/if}</td>
              <td>{$question.description}</td>
              <td>
                <a href="{$admin_file}?task=question&amp;action=edit&amp;id={$question.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang: button_edit){/if}"><i class="fa fa-edit"></i></a>
                <!-- Trigger the modal with a button -->
                <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$question.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}"><i class="fa fa-trash-o"></i></button>
                <!-- Modal -->
                <div class="modal fade" id="myModal_{$question.id}" role="dialog">
                  <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="panel panel-primary modal-content">
                      <div class="panel-heading modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate(Key Lang: text_confirmation){/if}</h4>
                      </div>
                      <div class="modal-body">
                        <p>
                          {if $multiLang.text_delete_question}{$multiLang.text_delete_question}{else}No Translate(Key Lang: text_delete_question){/if}
                          <b>({$question.title|escape})</b>?
                        </p>
                      </div>
                      <div class="modal-footer">
                        <a href="{$admin_file}?task=question&amp;action=delete&amp;id={$question.id}" class="btn btn-danger btn-md" style="color: white;">
                          <i class="fa fa-trash-o"></i> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang: button_delete){/if}
                        </a>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"></i> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang: button_close){/if}</button>
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
