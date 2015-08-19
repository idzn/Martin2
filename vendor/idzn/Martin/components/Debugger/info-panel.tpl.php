<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */
use application\custom\App;

?>
<?php
/**
 * @var Martin\components\Debugger\Debugger $this
 */
?>
<?php
if (APP_ENVIRONMENT == 'local') {
    App::assets()->linkScript(WEB_PATH . '/js/jquery/jquery.js');
} else {
    App::assets()->linkScript(WEB_PATH . '/js/jquery/jquery.min.js');
}
?>
<?php App::assets()->linkScript(WEB_PATH . '/bootstrap/js/bootstrap.min.js'); ?>
<?php App::assets()->linkStylesheet(WEB_PATH . '/bootstrap/css/bootstrap.min.css'); ?>
<?php App::assets()->linkStylesheet(__DIR__ . '/debugger.css'); ?>

<div id="debugger-info-panel">
    <span class="label label-default"><?=APP_ENVIRONMENT?></span>
    <span class="label label-default"><?=sprintf('%0.5f',($this->appStartTime - $this->appEndTime) * 1000) . ' ms'?></span>
    <span class="label label-default"><?=round($this->memoryPeakUsage  / 1024 , 2) . ' KB'?></span>
    <span class="label label-default"><?=$this->responseCode?></span>
    <img id="logo" src="/images/martin-logo.png" width="30" alt=""/>
    <?php if (!empty($this->inspectArray)) { ?>
    <table id="data-ispector">
        <thead>
        <tr>
            <?php foreach ($this->inspectArray as $key => $dump) { ?>
                <th><span class="label label-warning"><?=$key?></span></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <?php foreach ($this->inspectArray as $key => $dump) { ?>
                <td>
                    <div class="var-dump">
                        <?=$dump?>
                    </div>
                </td>
            <?php } ?>
        </tr>
        </tbody>
    </table>
    <?php } ?>
</div>
<script>
    $(document).ready(function(){
        $('#debugger-info-panel').height(0);

        $('#debugger-info-panel').mouseenter(function(){
            $('#debugger-info-panel').css({
                "-webkit-transition": "all 200ms linear",
                "height":"100%",
                "width":"100%"
            });
        });

        $('#debugger-info-panel').mouseleave(function(){
            $('#debugger-info-panel').css({
                "-webkit-transition": "all 200ms linear",
                "height":"0",
                "width":"250px"
            });
        });
    });
</script>