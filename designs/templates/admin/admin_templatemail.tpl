{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a href="{$admin_file}?task=category" id="bCrumbTest">{if $multiLang.text_category}{$multiLang.text_category}{else}No Translate(Key Lang:text_category){/if}</a></li>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_templatemail}{$multiLang.text_templatemail}{else}No Translate(Key Lang:text_templatemail){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>
  {if $error.exists}
  <div class="alert alert-danger">
    Email template is existed! Please update it.
  </div>
  {/if}
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_templatemail}{$multiLang.text_templatemail}{else}No Translate(Key Lang:text_templatemail){/if}</h4></div>
  <div class="panel-body">
    <div class="panel panel-default">
      <div class="panel-body">
        <form class="form-inline" role="form" action="{$admin_file}?task=templatemail" method="GET" style="padding: 1px 0px 12px 1px;">
          <input type="hidden" name="task" value="question">
          <div class="form-group">
            <button class="btn btn-primary collapsed" type="button" data-toggle="collapse" data-target="#demo" aria-expanded="false" aria-controls="collapseExample">
              <i class="fa fa-plus-circle"></i> {if $multiLang.button_add_question}{$multiLang.button_add_question}{else}No Translate(Key Lang: button_add_question){/if}
            </button>
          </div>
        </form>
        <div id="demo" class="collapse {if $error or $getEmailTemByID.id}in{/if}">
          {if $getEmailTemByID.id}
          <form action="{$admin_file}?task=templatemail&amp;action=edit&amp;cid={$smarty.get.cid}&amp;id={$getEmailTemByID.id}" method="post">
          {else}
          <form action="{$admin_file}?task=templatemail&amp;cid={$smarty.get.cid}" method="post">
          {/if}
          <input type="hidden" name="cid" value="{$smarty.get.cid}"/>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="title"><span style="color: red">*</span> {if $multiLang.text_subject}{$multiLang.text_subject}{else}No Translate(Key Lang: text_subject){/if}:</label>
                  {if $error.subject}
                    <span style="color: red">{if $multiLang.text_subject_empty}{$multiLang.text_subject_empty}{else}No Translate(Key Lang: text_subject_empty){/if}</span>
                  {/if}
                  <input type="text" name="subject" class="form-control" placeholder="Subject"
                  value="{if $smarty.session.email_temp.subject}{$smarty.session.email_temp.subject}{elseif $getEmailTemByID.subject}{$getEmailTemByID.subject}{/if}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label><span style="color: red">*</span> {if $multiLang.text_content}{$multiLang.text_content}{else}No Translate(Key Lang: text_content){/if}:</label>
                  {if $error.content}
                    <span style="color: red">{if $multiLang.text_content_empty}{$multiLang.text_content_empty}{else}No Translate(Key Lang: text_content_empty){/if}</span>
                  {/if}
                  <textarea id="summernote" class="form-control" name="content" rows="5" placeholder="Write something">{if $smarty.session.email_temp.content}{$smarty.session.email_temp.content}{elseif $getEmailTemByID.content}{$getEmailTemByID.content}{/if}</textarea>
                </div>
              </div>
            </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    {if $getEmailTemByID.id}
                      <input type="hidden" name="id" value="{$getEmailTemByID.id}"/>
                      <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate(Key Lang: button_update){/if}</button>
                      <a href="{$admin_file}?task=templatemail&amp;cid={$smarty.get.cid}{if $smarty.get.kwd}&amp;kwd={$smarty.get.kwd}{/if}{if $smarty.get.next}&amp;next={$smarty.get.next}{/if}" class="btn btn-danger" style="color: white;"><i class="fa fa-close"></i> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate(Key Lang: button_cancel){/if}</a>
                    {else}
                      <button {if $resultemail.id} type="button" {else} type="submit" {/if} name="butsubmit" class="btn btn-danger" {if $resultemail.id}disabled{/if}><i class="fa fa-floppy-o"></i> {if $multiLang.button_save}{$multiLang.button_save}{else}No Translate(Key Lang: button_save){/if}</button>
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
          <th>{if $multiLang.text_subject}{$multiLang.text_subject}{else}No Translate(Key Lang:text_subject){/if}</th>
          <th>{if $multiLang.text_content}{$multiLang.text_content}{else}No Translate(Key Lang:text_content){/if}</th>
          <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate(Key Lang:text_action){/if}</th>
          </tr>
        </thead>
        {if $resultemail}
        <tbody>
          <tr>
            <td>{$resultemail.subject}</td>
            <td>{$resultemail.content}</td>
            <td>
              <a href="{$admin_file}?task=templatemail&amp;action=edit&amp;cid={$smarty.get.cid}&amp;id={$resultemail.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate(Key Lang:button_edit){/if}"><i class="fa fa-edit"></i></a>
              <!-- Trigger the modal with a button -->
              <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_{$resultemail.id}" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang:button_delete){/if}"><i class="fa fa-trash-o"></i></button>
              <!-- Modal -->
              <div class="modal fade" id="myModal_{$resultemail.id}" role="dialog">
                <div class="modal-dialog">
                  <!-- Modal content-->
                  <div class="panel panel-primary modal-content">
                    <div class="panel-heading modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="panel-title modal-title">{if $multiLang.text_confirmation}{$multiLang.text_confirmation}{else}No Translate(Key Lang:text_confirmation){/if}</h4>
                    </div>
                    <div class="modal-body">
                      <p>{if $multiLang.text_confirmation_delete}{$multiLang.text_confirmation_delete}{else}No Translate(Key Lang:text_confirmation_delete){/if} {if $multiLang.text_templatemail}{$multiLang.text_templatemail}{else}No Translate(Key Lang:text_templatemail){/if} <b>({$resultemail.subject|escape})</b> ?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=templatemail&amp;action=delete&amp;cid={$smarty.get.cid}&amp;id={$resultemail.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_delete}{$multiLang.button_delete}{else}No Translate(Key Lang:button_delete){/if}</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_close}{$multiLang.button_close}{else}No Translate(Key Lang:button_close){/if}</i></button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
            </td>
          </tr>
        </tbody>
        {else}
        <tr>
          <td colspan="7"><h4 style="text-align:center">{if $multiLang.text_there_are_no_record}{$multiLang.text_there_are_no_record}{else}No Translate (Key Lang: text_there_are_no_record){/if}</h4></td>
        </tr>
        {/if}
      </table>

      <a id="btnBack" href="javascript:history.back()" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->

{/block}

{block name="javascript"}
<script>
  $(document).ready(function() {
    //Get previous url
    var urlBack =  document.referrer;
    var url = '';
    if(urlBack !== '') url = getUrlPrevious(urlBack);
    if(url.task === 'category') localStorage.setItem('urlCategory',urlBack);
    //Get session url
    var getUrlBack = localStorage.getItem('urlCategory');
    if(getUrlBack !== null){
      $("#btnBack").attr("href", getUrlBack);
      $("#bCrumbTest").attr("href", getUrlBack);
    }
    //End previous url
    //E
    $('#summernote').summernote({
      height: 150,
      toolbar: [
        // [groupName, [list of button]]
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough', 'superscript', 'subscript']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']]
      ]
    });
  });

</script>
{/block}
