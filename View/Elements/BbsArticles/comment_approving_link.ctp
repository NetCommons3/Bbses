<?php
/**
 * Bbs post view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php if ($bbsArticle['BbsArticle']['status'] === WorkflowComponent::STATUS_APPROVAL_WAITING &&
		Current::permission('content_comment_publishable')) : ?>
	<?php $this->request->data = $bbsArticle; ?>
	<?php echo $this->NetCommonsForm->create('BbsArticle', array(
			'div' => false,
			'class' => 'nc-bbs-edit-link',
			'url' => NetCommonsUrl::blockUrl(array('action' => 'approve', 'key' => $bbsArticle['BbsArticle']['key']))
		)); ?>

		<?php echo $this->NetCommonsForm->hidden('Frame.id', array('value' => Current::read('Frame.id'))); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id', array('value' => Current::read('Block.id'))); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.key', array('value' => Current::read('Block.key'))); ?>
		<?php echo $this->NetCommonsForm->hidden('BbsArticle.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('BbsArticle.bbs_key'); ?>
		<?php echo $this->NetCommonsForm->hidden('BbsArticle.key'); ?>
		<?php echo $this->NetCommonsForm->hidden('BbsArticle.language_id'); ?>
		<?php echo $this->NetCommonsForm->hidden('BbsArticleTree.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('BbsArticleTree.root_id'); ?>

		<?php echo $this->Workflow->publishLinkButton(__d('net_commons', 'Accept'), array(
				'iconSize' => 'btn-xs'
			)); ?>
	<?php echo $this->NetCommonsForm->end(); ?>
<?php endif;
