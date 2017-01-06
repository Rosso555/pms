<div class="col-md-12 text-center">
  <div class="pagination pagination-centered" style="font-weight: 700;
    margin-bottom: 10px;
    margin-top: 0;">

  {paginate_prev text="Previous"}&nbsp;

  {if $paginate.total gt $paginate.limit}
    &nbsp;&nbsp;{paginate_first text="First page"}&nbsp;
  {/if}

  {if $paginate.total gt $paginate.limit}
    {paginate_middle page_limit="10" prefix="&nbsp;" suffix="&nbsp;" format="page" }
  {/if}

  {if $paginate.total gt $paginate.limit}
    &nbsp;{paginate_last text="Last page"}
  {/if}
  &nbsp;&nbsp;{paginate_next text="Next"}&nbsp;

  {if $paginate.total gt $paginate.limit}
    [{$paginate.total}/{$paginate.page_total} pages]
  {/if}

  </div>
</div>
