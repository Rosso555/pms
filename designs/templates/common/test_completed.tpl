{extends file="common/layout.tpl"}

{block name="main"}
<div class="inbox-top">
  <h3 class="text-center">{$getTestById.title}</h3>
  <p class="text-center">{$getTestById.description}</p>
</div>
<div class="inbox-test sansserif" style="margin-bottom: 20px; padding: 60px 15px 60px 15px; text-align: center; font-size: 20px; color: #a94442;">
  <span style="font-size: 50px;"><i class="fa fa-check-circle-o" aria-hidden="true"></i> </span><br>
  <p>Test is completed and are now analyzed by your psychologist.</p>
  <br>
  <a href="{$patient_file}" class="btn btn-primary"><i class="fa fa-fw fa-home"></i> {if $multiLang.button_home}{$multiLang.button_home}{else}No Translate (Key Lang: button_home){/if}</a>
</div>

{/block}
