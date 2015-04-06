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

<?php echo $this->Form->hidden('BbsPost.id', array(
		'value' => isset($bbsPost['id']) ? (int)$bbsPost['id'] : null,
	)); ?>

<?php echo $this->Form->hidden('Frame.id', array(
		'value' => $frameId,
	)); ?>

<?php echo $this->Form->hidden('BbsPost.bbs_key', array(
		'value' => $bbs['key'],
	)); ?>

<?php echo $this->Form->hidden('BbsPost.key', array(
		'value' => $bbsPost['key'],
	)); ?>

<?php echo $this->Form->hidden('BbsPost.root_id', array(
		'value' => isset($bbsPost['rootId']) ? (int)$bbsPost['rootId'] : null,
	)); ?>

<?php echo $this->Form->hidden('BbsPost.parent_id', array(
		'value' => isset($bbsPost['parentId']) ? (int)$bbsPost['parentId'] : null,
	)); ?>

<?php echo $this->Form->hidden('BbsPostI18n.id', array(
		'value' => isset($bbsPostI18n['id']) ? (int)$bbsPostI18n['id'] : null,
	)); ?>

<?php echo $this->Form->hidden('BbsPostI18n.bbs_post_id', array(
		'value' => isset($bbsPost['id']) ? (int)$bbsPost['id'] : null,
	)); ?>

<?php echo $this->Form->hidden('BbsPostI18n.language_id', array(
		'value' => $languageId,
	)); ?>

<div class="row form-group">
	<div class="col-xs-12">
		<?php echo $this->Form->input(
				'BbsPostI18n.title', array(
					'type' => 'text',
					'label' => __d('bbses', 'Title') . $this->element('NetCommons.required'),
					'error' => false,
					'class' => 'form-control',
					'value' => (isset($bbsPostI18n['title']) ? $bbsPostI18n['title'] : '')
				)
			); ?>
	</div>

	<div class="col-xs-12">
		<?php echo $this->element(
			'NetCommons.errors', [
				'errors' => $this->validationErrors,
				'model' => 'BbsPostI18n',
				'field' => 'title',
			]); ?>
	</div>
</div>

<div class="row form-group">
	<div class="col-xs-12">
		<label class="control-label">
			<?php echo __d('bbses', 'Content'); ?>
			<?php echo $this->element('NetCommons.required'); ?>
		</label>
	</div>
	<div class="col-xs-12">
		<?php echo $this->Form->textarea('BbsPostI18n.content', array(
				'label' => false,
				'class' => 'form-control',
				'ui-tinymce' => 'tinymce.options',
				'ng-model' => 'bbsPostI18n.content',
				'rows' => 5,
				'required' => 'required',
			)); ?>
	</div>
	<div class="col-xs-12">
		<?php echo $this->element(
			'NetCommons.errors', [
				'errors' => $this->validationErrors,
				'model' => 'BbsPostI18n',
				'field' => 'content',
			]); ?>
	</div>
</div>
