<?php
/**
 * BbsSettings edit template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>
<?php echo $this->NetCommonsForm->hidden('Block.key'); ?>

<?php echo $this->element('Blocks.block_creatable_setting', array(
		'settingPermissions' => array(
			'content_creatable' => __d('blocks', 'Content creatable roles'),
			'content_comment_creatable' => __d('blocks', 'Content comment creatable roles'),
		),
	)); ?>

<?php echo $this->element('Blocks.block_approval_setting', array(
		'model' => 'BbsSetting',
		'useWorkflow' => 'use_workflow',
		'useCommentApproval' => 'use_comment_approval',
		'settingPermissions' => array(
			'content_comment_publishable' => __d('blocks', 'Content comment publishable roles'),
		),
		'options' => array(
			Block::NEED_APPROVAL => __d('blocks', 'Need approval in both %s and comments ', __d('bbses', 'articles')),
			Block::NEED_COMMENT_APPROVAL => __d('blocks', 'Need only comments approval'),
			Block::NOT_NEED_APPROVAL => __d('blocks', 'Not need approval'),
		),
	));
