{extends file="common/layout.tpl"}
{block name="style"}
<style>
  input[type=radio] {
    width: 16px;
    height: 16px;
  }
  input[type=checkbox] {
    width: 16px;
    height: 16px;
  }
  .tbl_result > tbody > tr > td {
    padding: 10px;
  }
</style>
{/block}
{block name="main"}
<div class="inbox-top">
  {if $test.title}<h3 class="text-center">{$test.title}</h3>{/if}
  <p class="text-center">{$test.description}</p>
  <hr>
  <p class="text-center" style="font-size: 25px;">
    {if $reponseAnswerByTestPsyt|@COUNT gt 0}
    <i class="fa fa-check-circle-o" aria-hidden="true" data-toggle1="tooltip" data-placement="top" title="Completed."></i>
    {else}
    <i class="fa fa-ban" aria-hidden="true" data-toggle1="tooltip" data-placement="top" title="Doesn't Complete."></i>
    {/if}
    PSYCHOLOGIST: {$psychologist.username}
  </p>
</div>
<div class="inbox-test sansserif" style="margin-bottom: 20px;">
  {if $reponseAnswerByTestPsyt|@COUNT gt 0}
    <h2>{if $multiLang.text_result_of_quick}{$multiLang.text_result_of_quick}{else}No Translate (Key Lang: text_result_of_quick){/if}</h2>
    {if $messageResultTopic|@COUNT gt 0}
    <table class="tbl_result">
      {foreach from=$messageResultTopic item=v name=getfirst}
      <tr style="font-size: 18px; line-height: 1.3;">
        {if $v.re_condition|@count gt 0}
          {foreach from=$v.re_condition item=d}
          <td valign="top"> <strong>{$d.topic_name}</strong> </td>
          <td valign="top"> : </td>
          <td> {$d.message|@nl2br}</td>
          {/foreach}
        {else}
          <td valign="top"> <strong>{$v.topic_title}</strong> </td>
          <td valign="top"> : </td>
          <td valign="top"> {$v.message|@nl2br} </td>
        {/if}
      </tr>
      {/foreach}
    </table>
    {else}
      <h4><b>No Result</b></h4>
    {/if}
    <!-- Start Diagram -->
    {if $listTopicDiagram|@count gt 0 and $drawingPointLine|@count gt 0}
    <hr>
    <div id="canvasDiv1" style="width: {$getWidthHeight.width + 3}px; margin:0 auto;">
      <canvas id="myCanvas" width="{$getWidthHeight.width}" height="{$getWidthHeight.height}" style="border:1px solid #ddd; backgroud: #fff;">
      Your browser does not support the canvas element.
      </canvas>
    </div>
    <img src="" id="imgCanvas1" width="100%" />
    {/if}

    {if $listTopicDiagramSecond|@count gt 0}
    <hr>
    <div id="canvasDiv2" style="width: {$getWidthHeightSecond.width + 3}px; height: {$getWidthHeightSecond.height + 10}px; margin:0 auto;">
      <canvas id="myCanvas2" width="{$getWidthHeightSecond.width}" height="{$getWidthHeightSecond.height}" style="border:1px solid #ddd;">
      Your browser does not support the canvas element.
      </canvas>
    </div>
    <img src="" id="imgCanvas2" width="100%" />
    {/if}
    <!-- End Diagram -->
  {else}
  <h2 class="text-center">This test does not complete.</h2>
  {/if}
  <br><br>
  <center>
    <a href="{$admin_file}?task=test_psychologist" class="btn btn-warning btn-sm"><i class="fa fa-backward" aria-hidden="true"></i> {if $multiLang.text_back}{$multiLang.text_back}{else}No Translate(Key Lang: text_back){/if}</a>
  </center>
  <br><br>
</div>
{/block}
{block name="javascript"}

<script>
{if $listTopicDiagram|@count gt 0 and $drawingPointLine|@count gt 0}
  var canvas = document.getElementById("myCanvas");
  var ctx = canvas.getContext("2d");
  ctx.strokeRect(0, 0, {$getWidthHeight.width}, {$getWidthHeight.height});

  //Text Total
  ctx.font = "15px Arial";
  ctx.textAlign = "center";
  ctx.fillText("T", 455 + 325, 95);

  //Text Test Graphic
  ctx.font = "17px Arial";
  ctx.textAlign = "left";
  ctx.fillText("{$test.graph_title}", 10, 35);

  ctx.beginPath();
  ctx.lineWidth = 0.5;
  ctx.moveTo(0,60);
  ctx.lineTo({$getWidthHeight.width},60);
  ctx.closePath();
  ctx.strokeStyle = "#383838";
  ctx.stroke();

  //Start Drawing
  ctx.save();
  ctx.lineWidth = 2;
  ctx.strokeStyle = 'red';
  ctx.fillStyle = 'red';
  ctx.beginPath();
  // ctx.moveTo(585, 75);
  {foreach from=$drawingPointLine item=v}
    // draw segment
    ctx.lineTo({$v.drawing_margin_left}, {$v.drawing_margin_top});
    ctx.stroke();
    ctx.closePath();
    ctx.beginPath();
    ctx.arc({$v.drawing_margin_left}, {$v.drawing_margin_top}, 5, 0, 3 * Math.PI, false);
    ctx.fill();
    ctx.closePath();
    // position for next segment
    ctx.beginPath();
    ctx.moveTo({$v.drawing_margin_left}, {$v.drawing_margin_top});
  {/foreach}
    ctx.restore();
  //End Drawing point

  //fetch amount_result
  {foreach from=$drawingPointLine item=v}
  ctx.font = "15px Arial";
  ctx.textAlign = "center";
  ctx.fillText("{$v.amount_result|number_format:2}", {$v.result_margin_left}, {$v.drawing_margin_top});
  {/foreach}

  // fetch line Horizontal
  aawww{foreach from=$listXlineDiagram item=v}
  ctx.beginPath();
  ctx.lineWidth = 1;
  ctx.moveTo(0,{$v.xmargin_top});
  ctx.lineTo({$v.xwidth},{$v.xmargin_top});
  ctx.closePath();
  ctx.strokeStyle = "#383838";
  ctx.stroke();
  {/foreach}

  //fetch text number min & max
  wwwww{foreach from=$listNumberMinMax item=v}
  ctx.font = "14px Arial";
  ctx.textAlign = "center";
  ctx.fillText("{$v.text_number}", {$v.margin_left}, {$v.margin_top});
  {/foreach}

  //fetch text above min & max number
  qqqqq{foreach from=$listTextMinMax item=d}
  ctx.font = "13px Arial";
  ctx.textAlign = "center";
  ctx.fillText("{$d.text_header}", {$d.margin_left}, {$d.margin_top});
  {/foreach}

  //fetch text Topic
  {foreach from=$listTopicDiagram item=v name=getfirst}
    ctx.font = "15px Arial";
    ctx.textAlign = "left";
    ctx.fillText("{$v.topic_title}", {$v.margin_left}, {$v.margin_top});
  {/foreach}

  //fetch small line Vertical
  {$i = 0}
  {foreach from=$listSmallYLineDiagram item=v name=foo key=i}
  ctx.beginPath();
  {if $smarty.foreach.foo.first OR $smarty.foreach.foo.last}
    ctx.moveTo({$v.y_small_margin_left},{$v.y_small_margin_top});
  {else}
    {$q = $i} {$t = $q % 5}
    {if $t eq 0}
      ctx.moveTo({$v.y_small_margin_left},{$v.y_small_margin_top});
    {else}
      ctx.moveTo({$v.y_small_margin_left},{$v.y_small_margin_top} + 10);
    {/if}
  {/if}
  ctx.lineTo({$v.y_small_margin_left}, 110);
  ctx.closePath();
  ctx.stroke();
  {/foreach}

  {foreach from=$listXdiagramCenter item=v}
  ctx.beginPath();
  ctx.lineWidth = 0.5;
  ctx.moveTo({$v.xmargin_left},{$v.xmargin_top});
  ctx.lineTo({$v.xwidth},{$v.xmargin_top});
  ctx.stroke();
  ctx.closePath();
  {/foreach}

  //fetch line Vertical Center
  {foreach from=$listYLineDiagramCenter item=v}
  ctx.beginPath();
  ctx.lineWidth = 0.5;
  ctx.setLineDash([3,2]);
  ctx.moveTo({$v.ymargin_left_center},{$v.ymargin_top_center});
  ctx.lineTo({$v.ymargin_left_center},{$getWidthHeight.height} - 50);
  ctx.stroke();
  ctx.closePath();
  {/foreach}

  //fetch line Vertical
  {foreach from=$listYLineDiagram item=v name=foo}
  ctx.beginPath();
  ctx.lineWidth = 1;
  {if $smarty.foreach.foo.first OR $smarty.foreach.foo.last}
  ctx.setLineDash([0,0]);
  {else}
  ctx.setLineDash([7,5]);
  {/if}
  ctx.moveTo({$v.ymargin_left},{$v.ymargin_top});
  ctx.lineTo({$v.ymargin_left},{$getWidthHeight.height} - 50);
  ctx.stroke();
  ctx.closePath();
  {/foreach}

  //Set backgroud top
  ctx.fillStyle = "rgba(212, 212, 212, 0.1)";
  ctx.fillRect(0, 60, {$getWidthHeight.width}, 50);

  //fetch backgroud
  {foreach from=$listBackgroudColor item=v}
  ctx.fillStyle = "{$v.color}";
  ctx.fillRect({$v.margin_left}, {$v.margin_top}, {$v.width}, {$getWidthHeight.height} - 160);
  {/foreach}
{/if}
</script>

<script>
{if $listTopicDiagramSecond|@count gt 0}
  var canvas2 = document.getElementById("myCanvas2");
  var ctx2 = canvas2.getContext("2d");
  ctx2.strokeRect(0, 0, {$getWidthHeightSecond.width}, {$getWidthHeightSecond.height});

  //Text Test Graphic
  ctx2.font = "17px Arial";
  ctx2.textAlign = "left";
  ctx2.fillText("{$test.graph_title}", 10, 35);

  ctx2.beginPath();
  ctx2.lineWidth = 0.5;
  ctx2.moveTo(0,60);
  ctx2.lineTo({$getWidthHeightSecond.width}, 60);
  ctx2.closePath();
  ctx2.strokeStyle = "#383838";
  ctx2.stroke();

  // fetch line Horizontal second
  {foreach from=$listXlineDiagramSecond item=v}
    ctx2.beginPath();
    ctx2.lineWidth = 1;
    ctx2.moveTo(0,{$v.xmargin_top});
    ctx2.lineTo({$v.xwidth},{$v.xmargin_top});
    ctx2.closePath();
    ctx2.strokeStyle = "#383838";
    ctx2.stroke();
  {/foreach}

  {foreach from=$listXlineDiagramSecondCenter item=v}
    ctx2.beginPath();
    ctx2.lineWidth = 1;
    ctx2.moveTo({$v.xmargin_left},{$v.xmargin_top});
    ctx2.lineTo({$v.xwidth},{$v.xmargin_top});
    ctx2.strokeStyle = "#383838";
    ctx2.stroke();
    ctx2.closePath();
  {/foreach}

  //fetch text Topic
  {foreach from=$listTopicDiagramSecond item=v name=getfirst}
    ctx2.font = "15px Arial";
    ctx2.textAlign = "left";
    ctx2.fillText("{$v.topic_title}", {$v.margin_left}, {$v.margin_top});
  {/foreach}

  //List Vertical line
  {foreach from=$listYLineDiagramSecond item=v name=foo}
    ctx2.beginPath();
    ctx2.lineWidth = 1;
    {if $smarty.foreach.foo.first OR $smarty.foreach.foo.last}
    ctx2.setLineDash([0,0]);
    {else}
    ctx2.setLineDash([7,5]);
    {/if}
    ctx2.moveTo({$v.ymargin_left},{$v.ymargin_top});
    ctx2.lineTo({$v.ymargin_left},{$getWidthHeightSecond.height});
    ctx2.strokeStyle = "#383838";
    ctx2.stroke();
    ctx2.closePath();
  {/foreach}

  {foreach from=$listRotateLineDiagramSecond item=v}
    ctx2.beginPath();
    ctx2.lineWidth = 1;
    ctx2.moveTo({$v.move_to_left},{$v.move_to_top});
    ctx2.lineTo({$v.line_to_left},{$v.line_to_top});
    ctx2.strokeStyle = "#383838";
    ctx2.stroke();
    ctx2.closePath();

  {/foreach}

  //fetch topic Analysis rotate
  {foreach from=$listTopicAnalysis item=v}
    // ctx.fillStyle = 'black';
    ctx2.save();
    ctx2.translate({$v.margin_left}, {$v.margin_top});
    ctx2.rotate(-Math.PI / 3.1);
    ctx2.font = "15px Arial"
    ctx2.textAlign = 'left';
    ctx2.fillText('{$v.topic_analysis_title}', 0, 15 / 2);
    ctx2.restore();
  {/foreach}
  //fetch text Analysis result
  {foreach from=$drawingLineResultDiagramSecond item=v}
    ctx2.font = "15px Arial";
    ctx2.textAlign = "left";
    ctx2.fillText("{$v.topic_analysis_title}", {$v.result_margin_left}, {$v.drawing_margin_top} + 5);
  {/foreach}

    //Start Drawing
    ctx2.save();
    ctx2.lineWidth = 1.5;
    ctx2.strokeStyle = 'red';
    ctx2.fillStyle = 'red';
    ctx2.beginPath();
    // ctx2.moveTo(475, 155);
  {foreach from=$drawingLineResultDiagramSecond item=v}
    // draw segment
    ctx2.lineTo({$v.drawing_margin_left}, {$v.drawing_margin_top});
    ctx2.stroke();
    ctx2.closePath();
    ctx2.beginPath();
    ctx2.arc({$v.drawing_margin_left}, {$v.drawing_margin_top}, 8, 0, 2 * Math.PI, false);
    ctx2.fill();
    ctx2.closePath();
    // position for next segment
    ctx2.beginPath();
    ctx2.moveTo({$v.drawing_margin_left}, {$v.drawing_margin_top});
  {/foreach}
    ctx2.restore();
  //End Drawing point

  //fetch backgroud
  {foreach from=$listBackgroudColorSecond item=v}
    ctx2.fillStyle = "{$v.color}";
    ctx2.fillRect({$v.margin_left}, {$v.margin_top}, {$v.width}, {$getWidthHeightSecond.height} - 190);
  {/foreach}

  ctx2.fillStyle = "#cbcbcb4d";
  ctx2.fillRect(0,60, {$getWidthHeightSecond.width}, 150);
{/if}
</script>

<script>
$(document).ready( function(){
  //Run function when browser resizes
  $(window).resize( resizeBrowserImageCanvas );

  function resizeBrowserImageCanvas()
  {
    var width = window.innerWidth;
    var height = window.innerHeight;
    var canvas1 = document.getElementById("myCanvas");
    var canvas2 = document.getElementById("myCanvas2");

    if(canvas1 != null){
      var myImage1 = canvas1.toDataURL();
    }
    if(canvas2 != null){
      var myImage2 = canvas2.toDataURL();
    }
    $('#imgCanvas1').attr('src', myImage1);
    $('#imgCanvas2').attr('src', myImage2);

    if(width < 992) {
      //Show imgCanvas1 & imgCanvas2
      $('#imgCanvas1').show();
      $('#imgCanvas2').show();
      //Hide canvasDiv1 & canvasDiv2
      $('#canvasDiv1').hide();
      $('#canvasDiv2').hide();
    } else {
      //Hide canvasDiv1 & canvasDiv2
      $('#canvasDiv1').show();
      $('#canvasDiv2').show();
      //Hide imgCanvas1 & imgCanvas2
      $('#imgCanvas1').hide();
      $('#imgCanvas2').hide();
    }
  }
  //Run function after finished loading
  resizeBrowserImageCanvas();

});
</script>

{/block}
