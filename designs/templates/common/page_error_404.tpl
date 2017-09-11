{extends file="common/layout.tpl"}
{block name="main"}
<ul class="breadcrumb">
  <li><a href="{if $mode eq 'patient'}{$patient_file}{/if}{if $mode eq 'psychologist'}{$psychologist_file}{/if}"><i class="fa fa-fw fa-home"></i></a></li>
  <li class="active">Page Not Page.</li>
</ul>
<div class="jumbotron text-center" style=" background-color: #fcfcfc; color: #606060;">
  <i class="fa fa-info-circle" aria-hidden="true" style="color: red; font-size:40px;"></i>
    <h2>Page Not Found!</h2>
    <p>Sorry! We can't find result.</p>
    <a href="{if $mode eq 'patient'}{$patient_file}{/if}{if $mode eq 'psychologist'}{$psychologist_file}{/if}" class="btn btn-primary btn-sm">Home <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
</div>

{/block}
