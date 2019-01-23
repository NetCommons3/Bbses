<?php
/**
 * 返信ボタン
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php if (Current::permission('content_comment_creatable') && $bbsSetting['use_comment']
			&& $status === WorkflowComponent::STATUS_PUBLISHED) : ?>

	<?php echo $this->NetCommonsForm->create('', array(
			'div' => false,
			'class' => 'nc-bbs-edit-link',
			'type' => 'get',
			'url' => NetCommonsUrl::blockUrl(array('action' => 'reply', 'key' => $bbsArticleKey))
		)); ?>

		<?php
			echo $this->NetCommonsForm->checkbox('quote', array(
				'type' => 'checkbox',
				'checked' => true,
				'hiddenField' => false,
				'label' => __d('bbses', 'Quote this posts'),
				'inline' => true,
				'id' => 'quote' . $bbsArticleKey
			));
		?>

		<?php
			echo $this->Button->button(
				__d('bbses', 'Write comment'),
				array(
					'class' => 'btn btn-success btn-xs bbs-write-comment-link',
					'icon' => 'glyphicon-comment',
					'mobileHide' => true
				)
			);
		?>


		<?php echo $this->NetCommonsForm->hidden('frame_id', array(
			'value' => Current::read('Frame.id')
		)); ?>

	<?php echo $this->NetCommonsForm->end(); ?>
<?php endif;
