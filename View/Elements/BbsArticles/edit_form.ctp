<?php
/**
 * Element of BbsArticles edit
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="panel panel-default">
	<?php echo $this->NetCommonsForm->create('BbsArticle'); ?>
		<div class="panel-body">

			<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
			<?php echo $this->NetCommonsForm->hidden('Frame.block_id'); ?>
			<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>
			<?php echo $this->NetCommonsForm->hidden('Block.key'); ?>
			<?php echo $this->NetCommonsForm->hidden('Bbs.id'); ?>
			<?php echo $this->NetCommonsForm->hidden('Bbs.key'); ?>
			<?php echo $this->NetCommonsForm->hidden('Bbs.name'); ?>
			<?php echo $this->NetCommonsForm->hidden('BbsArticle.id'); ?>
			<?php echo $this->NetCommonsForm->hidden('BbsArticle.key'); ?>
			<?php echo $this->NetCommonsForm->hidden('BbsArticle.language_id'); ?>
			<?php echo $this->NetCommonsForm->hidden('BbsArticle.bbs_key'); ?>
			<?php echo $this->NetCommonsForm->hidden('BbsArticle.block_id'); ?>
			<?php echo $this->NetCommonsForm->hidden('BbsArticleTree.id'); ?>
			<?php echo $this->NetCommonsForm->hidden('BbsArticleTree.bbs_key'); ?>
			<?php echo $this->NetCommonsForm->hidden('BbsArticleTree.bbs_article_key'); ?>
			<?php echo $this->NetCommonsForm->hidden('BbsArticleTree.root_id'); ?>
			<?php echo $this->NetCommonsForm->hidden('BbsArticleTree.parent_id'); ?>

			<?php echo $this->NetCommonsForm->inputWithTitleIcon('BbsArticle.title', 'BbsArticle.title_icon', array(
					'label' => __d('bbses', 'Title'),
					'required' => true,
				)); ?>

			<?php echo $this->NetCommonsForm->wysiwyg('BbsArticle.content', array(
					'label' => __d('bbses', 'Content'),
					'required' => true,
				)); ?>

			<?php if ($this->request->params['action'] === 'add' ||
					$this->request->params['action'] === 'edit' && ! $this->data['BbsArticleTree']['root_id']) : ?>
				<hr />

				<?php echo $this->Workflow->inputComment('BbsArticle.status'); ?>
			<?php endif; ?>
		</div>

		<?php
			if ($this->params['action'] === 'edit' && $this->data['BbsArticleTree']['root_id']) {
				echo $this->BbsesForm->replyEditButtons('BbsArticle.status');
			} else {
				if ($this->params['action'] === 'reply') {
					$key = isset($currentBbsArticle['BbsArticle']['key'])
						? $currentBbsArticle['BbsArticle']['key']
						: null;
					$cancelUrl = NetCommonsUrl::blockUrl(
						array('action' => 'view', 'key' => $key)
					);
				} elseif ($this->params['action'] === 'edit') {
					$key = isset($this->request->data['BbsArticle']['key'])
						? $this->request->data['BbsArticle']['key']
						: null;
					$cancelUrl = NetCommonsUrl::blockUrl(
						array('action' => 'view', 'key' => $key)
					);
				} else {
					$cancelUrl = null;
				}
				echo $this->Workflow->buttons('BbsArticle.status', $cancelUrl);
			}
		?>
	<?php echo $this->NetCommonsForm->end(); ?>

	<?php if ($this->request->params['action'] === 'edit' && $this->Workflow->canDelete('BbsArticle', $this->data)) : ?>
		<div class="panel-footer text-right">
			<?php echo $this->element('BbsArticles/delete_form'); ?>
		</div>
	<?php endif; ?>
</div>

<?php echo $this->Workflow->comments();
