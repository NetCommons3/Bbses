<?php
/**
 * Workflow Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowBehavior', 'Workflow.Model/Behavior');

/**
 * Workflow Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package  Bbses\Workflow\Model\Befavior
 */
class BbsesWorkflowBehavior extends WorkflowBehavior {

/**
 * beforeValidate is called before a model is validated, you can use this callback to
 * add behavior validation rules into a models validate array. Returning false
 * will allow you to make the validation fail.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False or null will abort the operation. Any other result will continue.
 * @see Model::save()
 */
	public function beforeValidate(Model $model, $options = array()) {
		if (empty($model->data['BbsArticleTree']['root_id'])) {
			return parent::beforeValidate($model, $options);
		}

		if (Current::permission('content_comment_publishable')) {
			$statuses = WorkflowBehavior::$statuses;
		} else {
			$statuses = WorkflowBehavior::$statusesForEditor;
		}

		$model->validate = ValidateMerge::merge($model->validate, array(
			'status' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'inList' => array(
					'rule' => array('inList', $statuses),
					'message' => __d('net_commons', 'Invalid request.'),
				)
			),
		));

		return true;
	}

}
