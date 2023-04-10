<?php
/**
 * 掲示板 Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');
App::uses('WorkflowComponent', 'Workflow.Controller/Component');

/**
 * 掲示板 Helper
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Workflow\View\Helper
 */
class BbsesFormHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'Form',
		'Html',
		'NetCommons.Button',
		'NetCommons.LinkButton',
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
	);

/**
 * 返信フォームのボタン表示
 *
 * @param string $statusFieldName ステータスのフィールド名("Modelname.fieldname")
 * @param string $cancelUrl キャンセルボタン
 * @return string ボタンHTML
 */
	public function replyButtons($statusFieldName, $cancelUrl) {
		$output = '';
		$output .= '<div class="panel-footer text-center">';

		$status = null;
		if (isset($this->_View->request->data[$statusFieldName . '_'])) {
			$status = $this->_View->request->data[$statusFieldName . '_'];
		}
		if (isset($this->_View->request->data[$statusFieldName])) {
			$status = $this->_View->request->data[$statusFieldName];
		}

		//変更前のstatusを保持する
		$output .= $this->NetCommonsForm->hidden('status_', array('value' => $status));

		$cancelOptions = array(
			'ng-class' => '{disabled: sending}',
			'ng-click' => 'sending=true',
		);

		$saveTempOptions = array(
			'label' => __d('net_commons', 'Save temporally'),
			'class' => 'btn btn-info' . $this->Button->getButtonSize() . ' btn-workflow',
			'name' => 'save_' . WorkflowComponent::STATUS_IN_DRAFT,
			'ng-class' => '{disabled: sending}'
		);

		if (Current::permission('content_comment_publishable')) {
			$saveOptions = array(
				'label' => __d('net_commons', 'OK'),
				'class' => 'btn btn-primary' . $this->Button->getButtonSize() . ' btn-workflow',
				'name' => 'save_' . WorkflowComponent::STATUS_PUBLISHED,
				'ng-class' => '{disabled: sending}'
			);
		} else {
			$saveOptions = array(
				'label' => __d('net_commons', 'OK'),
				'class' => 'btn btn-primary' . $this->Button->getButtonSize() . ' btn-workflow',
				'name' => 'save_' . WorkflowComponent::STATUS_APPROVAL_WAITING,
				'ng-class' => '{disabled: sending}'
			);
		}

		$output .= $this->Button->cancelAndSaveAndSaveTemp(
			$cancelUrl, $cancelOptions, $saveTempOptions, $saveOptions
		);

		$output .= '</div>';

		return $output;
	}

}
