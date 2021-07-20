<?php
include_partial('tmcTwitterBootstrap/assets');
include_component('tmcTwitterBootstrap', 'header');
?>
<div>
    <?php include_partial('vtarea/flashes') ?>
    <?php include_partial('vtarea/filters', array('importForm' => $importForm)) ?>
    <?php if(isset($dataErrors) && isset($countSuccess)) : ?>
    <?php include_partial('vtarea/importErrors', array('dataErrors' => $dataErrors, 'countSuccess' => $countSuccess))?>
    <?php endif ?>
</div>

<?php include_component('tmcTwitterBootstrap', 'footer') ?>
