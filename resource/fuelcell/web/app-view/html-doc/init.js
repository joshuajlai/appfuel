<?php
	$config   = $this->get('js-yui-config', '');
	$requires = $this->get('js-app-dependencies', '');
	$inline   = $this->get('js-init-content', '');
?>

YUI(<?php echo $config?>).use(<?php echo $requires; ?>, function(Y) {
	<?php $this->render('js-pre-int', ''); ?>
	<?php echo $inline; ?>
	<?php $this->render('js-post-int', ''); ?>
});
