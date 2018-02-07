{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTest" href="{$admin_file}?task=test">{if $multiLang.text_section}{$multiLang.text_section}{else}No Translate (Key Lang:text_section){/if}</li></a>
  <li {if $smarty.get.action neq 'edit'}class="active"{/if}>{if $multiLang.text_sub_section}{$multiLang.text_sub_section}{else}No Translate (Key Lang:text_sub_section){/if}</li>
  {if $smarty.get.action eq 'edit'}
  <li class="active">{if $multiLang.text_edit}{$multiLang.text_edit}{else}No Translate(Key Lang: text_edit){/if}</li>
  {/if}
</ul>

<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_section}{$multiLang.text_section}{else}No Translate (Key Lang:text_section){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_section}{$multiLang.text_section}{else}No Translate(Key Lang: text_section){/if}:</b>
      {foreach from=$mainSubR.mainSub item=data}
        {$data.name}
      {/foreach}
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-sm-12">
            <form class="form-inline" action="{$admin_file}?task=section" method="get">
              <input type="hidden" name="task" value="category">
              <div class="form-group" style="margin-bottom:5px;">
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                  <i class="fa fa-plus-circle"></i> Add Sub Section
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
            <div class="collapse {if $error Or $getSectionSubByID.id}in{/if}" id="collapseExample" style="margin-top: 10px;">
              {if $getSectionSubByID.id}
              <form class="form" role="form" action="{$admin_file}?task=section_sub&amp;action=edit&amp;tid={$smarty.get.tid}&amp;par_id={$smarty.get.par_id}&amp;id=$getSectionSubByID.id" method="post">
              {else}
              <form class="form" role="form" action="{$admin_file}?task=section_sub&amp;action=add&amp;tid={$smarty.get.tid}&amp;par_id={$smarty.get.par_id}" method="post">
              {/if}
                <input type="hidden" name="tid" value="{$smarty.get.tid}">
                <input type="hidden" name="par_id" value="{$smarty.get.par_id}">
                <div class="form-group">
                  <label for="name"><span style="color: red">*</span> {if $multiLang.text_title}{$multiLang.text_title}{else}No Translate (Key Lang:text_title){/if}:</label>
                  <input type="text" name="name" class="form-control" value="{$getSectionSubByID.name}" id="name" placeholder="Enter title" required>
                </div>
                {if $getSectionSubByID.id}
                <div class="form-group" style="margin-bottom:5px;">
                  <input type="hidden" name="id" value="{$getSectionSubByID.id}" />
                  <button type="submit" class="btn btn-success"><i class="fa fa-pencil-square-o"></i> {if $multiLang.button_update}{$multiLang.button_update}{else}No Translate (Key Lang:button_update){/if}</button>
                  <a href="{$admin_file}?task=section&amp;tid={$smarty.get.tid}" class="btn btn-danger"><i class="fa fa-close"></i>  {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate (Key Lang:button_cancel){/if}</a>
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
            <th>{if $multiLang.text_category_name}{$multiLang.text_category_name}{else}No Translate (Key Lang:text_category_name){/if}</th>
            <th>{if $multiLang.text_sub_section}{$multiLang.text_sub_section}{else}No Translate (Key Lang:text_sub_section){/if}</th>
            <th width="130">{if $multiLang.text_action}{$multiLang.text_action}{else}No Translate (Key Lang:text_action){/if}</th>
          </tr>
        </thead>
        {if $listSectionByTest|@count gt 0}
        <tbody>
          {foreach from = $listSectionByTest item = data key=k}
          <tr>
            <td>{$data.name}</td>
            <td><span class="badge">{$data.members}</span></td>
            <td>
              <a href="{$admin_file}?task=section&amp;action=edit&amp;par_id={$data.parent_id}&amp;id={$data.id}" class="btn btn-success btn-xs" data-toggle1="tooltip" data-placement="top" title="{if $multiLang.button_edit}{$multiLang.button_edit}{else}No Translate (Key Lang:button_edit){/if}"><i class="fa fa-edit"></i></a>
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
                      <p>Are you sure you want to delete this section sub <b>({$data.name|escape})</b> ?</p>
                    </div>
                    <div class="modal-footer">
                      <a href="{$admin_file}?task=section_sub&amp;action=delete&amp;par_id={$smarty.get.par_id}&amp;id={$data.id}" class="btn btn-danger btn-md" style="color: white;"><i class="fa fa-trash-o"> {if $multiLang.button_yes}{$multiLang.button_yes}{else}No Translate (Key Lang:button_yes){/if}</i></a>
                      <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-remove"> {if $multiLang.button_cancel}{$multiLang.button_cancel}{else}No Translate (Key Lang:button_cancel){/if}</i></button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->

              <a href="{$admin_file}?task=section_sub&amp;par_id={$data.id}" class="btn btn-info btn-xs" data-toggle1="tooltip" data-placement="top" title="Add Section Sub"><i class="fa fa-plus-circle"></i></a>

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
      <a id="btnBack" href="{$admin_file}?task=section_sub&amp;action=back&amp;key={$key}" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
{block name="javascript"}
<!-- <script>
  //Get previous url
  var urlBack =  document.referrer;
  var url = '';
  if(urlBack !== '') url = getUrlPrevious(urlBack);
  if(url.task === 'section') localStorage.setItem('urlTest',urlBack);
  //Get session url
  var getUrlBack = localStorage.getItem('urlTest');
  if(getUrlBack !== null){
    $("#btnBack").attr("href", getUrlBack);
    $("#bCrumbTest").attr("href", getUrlBack);
  }
  //End previous url
</script> -->
{/block}
