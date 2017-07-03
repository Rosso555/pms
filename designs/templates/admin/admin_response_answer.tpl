{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{$admin_file}"><i class="fa fa-fw fa-home"></i></a></li>
  <li><a id="bCrumbTest" href="{$admin_file}?task=test">{if $multiLang.text_test}{$multiLang.text_test}{else}No Translate(Key Lang: text_test){/if}</a></li>
  <li><a id="bCrumbResponse" href="{$admin_file}?task=response&amp;tid={$smarty.get.tid}">{if $multiLang.text_response_result}{$multiLang.text_response_result}{else}No Translate(Key Lang: text_response_result){/if}</a></li>
  <li class="active">{if $multiLang.text_response_answer}{$multiLang.text_response_answer}{else}No Translate(Key Lang: text_response_answer){/if}</li>
</ul>
<div class="panel panel-primary">
  <div class="panel-heading"><h4 class="panel-title">{if $multiLang.text_result}{$multiLang.text_result}{else}No Translate(Key Lang: text_result){/if}</h4></div>
  <div class="panel-body">
    <div class="box_title">
      <b>{if $multiLang.text_test_title}{$multiLang.text_test_title}{else}No Translate(Key Lang: text_test_title){/if}:</b> {$test.title}
    </div>

    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr bgcolor="#eeeeee">
          <th>{if $multiLang.text_question_title}{$multiLang.text_question_title}{else}No Translate(Key Lang: text_question_title){/if} </th>
          <th width="400px">{if $multiLang.text_answer_title}{$multiLang.text_answer_title}{else}No Translate(Key Lang: text_answer_title){/if} </th>
          <th>{if $multiLang.text_answer_content}{$multiLang.text_answer_content}{else}No Translate(Key Lang: text_answer_content){/if}</th>
          <th>{if $multiLang.text_score_answer}{$multiLang.text_score_answer}{else}No Translate(Key Lang: text_score_answer){/if}</th>
          <th>{if $multiLang.text_calculate}{$multiLang.text_calculate}{else}No Translate(Key Lang: text_calculate){/if}</th>
          </tr>
        </thead>
        {if $listResponseAnswer|@count gt 0}
        <tbody>
        {foreach from = $listResponseAnswer item = data key=k}
          <tr>
            <td>{$data.que_title}</td>
            <td>{if $data.title}{$data.title}{else}~{/if}</td>
            <td>{if $data.content}{$data.content}{else}~{/if}</td>
            <td>
              {if $data.content}
                ~
              {else}
              <table>
                {foreach from=$data.topic_value item=v}
                <tr>
                  <td>{$v.name}</td>
                  <td>&nbsp; : &nbsp;</td>
                  <td>{$v.amount}</td>
                </tr>
                {/foreach}
              </table>
              {/if}
            </td>
            <td>
              {if $data.calculate eq 1}
              <span class="label label-warning">{if $multiLang.text_no}{$multiLang.text_no}{else}No Translate(Key Lang: text_no){/if}</span>
              {else}
              <span class="label label-primary">{if $multiLang.text_yes}{$multiLang.text_yes}{else}No Translate(Key Lang: text_yes){/if}</span>
              {/if}
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

      <a id="btnBack" href="javascript:history.back()" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
    </div><!--table-responsive  -->
    {include file="common/paginate.tpl"}
  </div><!--end panel-body  -->
</div><!--end panel panel-primary  -->
{/block}
{block name="javascript"}
<script>
  //Get previous url
  var urlBack =  document.referrer;
  var url = '';
  if(urlBack !== '') url = getUrlPrevious(urlBack);
  if(url.task === 'response') localStorage.setItem('urlResponse',urlBack);
  //Get session url
  var getUrlBack = localStorage.getItem('urlResponse');
  var getUrlBackTest = localStorage.getItem('urlTest');
  if(getUrlBack !== null){
    $("#btnBack").attr("href", getUrlBack);
    $("#bCrumbResponse").attr("href", getUrlBack);
  }
  if(getUrlBackTest !== null) $("#bCrumbTest").attr("href", getUrlBackTest);
  //End previous url
</script>
{/block}
