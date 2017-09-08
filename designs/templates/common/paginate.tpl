{if $paginate.total gt $paginate.limit}<hr style="border-top: 2px solid #eee; border-radius: 4px;">{/if}
<div class="col-md-12 text-center">
  <div class="pagination pagination-centered" style="font-weight: 700; margin-bottom: 0px; margin-top: 0;">
    {if $paginate.total gt $paginate.limit}
    <ul class="pagination" style="margin:0px; float: left;">
      {if $paginate.total gt $paginate.limit}
        {if $paginate.first eq 1}
        <li class="disabled"><a data-toggle1="tooltip" data-placement="top" title="Frist Page"><i class='fa fa-angle-left' aria-hidden='true'></i><i class='fa fa-angle-left' aria-hidden='true'></i></a></li>
        {else}
        <li>{paginate_first text="<i class='fa fa-angle-left' aria-hidden='true'></i><i class='fa fa-angle-left' aria-hidden='true'></i>" data-toggle1="tooltip" data-placement="top" title="Frist Page"}</li>
        {/if}
      {/if}

      <li>{paginate_prev text="<i class='fa fa-chevron-left' aria-hidden='true'></i>" data-toggle1="tooltip" data-placement="top" title="Previous Page"}</li>
      <!--
      #active_link_prefix && #active_link_suffix
      Custom in "external_libs\Smarty-3.1.14\libs\plugins\function.paginate_middle.php"
      -->
      {paginate_middle page_limit="10" format="page" prefix="" suffix="" link_prefix="<li>" link_suffix="</li>" active_link_prefix="<li class='active'><a>" active_link_suffix="</li></a>"}

      <li>{paginate_next text="<i class='fa fa-chevron-right' aria-hidden='true'></i>" data-toggle1="tooltip" data-placement="top" title="Next Page"}</li>

      {if $paginate.total gt $paginate.limit}
        {if $paginate.total eq $paginate.last}
        <li class="disabled"><a data-toggle1="tooltip" data-placement="top" title="Last Page"><i class='fa fa-angle-right' aria-hidden='true'></i><i class='fa fa-angle-right' aria-hidden='true'></i></a></li>
        {else}
        <li>{paginate_last text="<i class='fa fa-angle-right' aria-hidden='true'></i><i class='fa fa-angle-right' aria-hidden='true'></i>" data-toggle1="tooltip" data-placement="top" title="Last Page"}</li>
        {/if}
      {/if}
    </ul>

    <div style="margin:0px; margin-left: 10px; float: left; padding: 6px 12px; border: 1px solid; border-radius: 4px; border-color: #ddd;">
      {$paginate.total}/{$paginate.page_total} Pages
    </div>
    {/if}

  <!-- {paginate_prev text="Previous"}&nbsp;

  {if $paginate.total gt $paginate.limit}
    &nbsp;&nbsp;{paginate_first text="First page"}&nbsp;
  {/if}

  {if $paginate.total gt $paginate.limit}
    {paginate_middle page_limit="10" prefix="&nbsp;" suffix="&nbsp;j" format="page"}
  {/if}

  {if $paginate.total gt $paginate.limit}
    &nbsp;{paginate_last text="Last page"}
  {/if}
  &nbsp;&nbsp;{paginate_next text="Next"}&nbsp;

  {if $paginate.total gt $paginate.limit}
    [{$paginate.total}/{$paginate.page_total} pages]
  {/if} -->

  </div>
</div>
