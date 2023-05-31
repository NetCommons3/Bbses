<?php
/**
 * BbsArticle Model
 *
 * @property Bbs $Bbs
 * @property Language $Language
 * @property User $User
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BbsesAppModel', 'Bbses.Model');
App::uses('MailQueueBehavior', 'Mails.Model/Behavior');

/**
 * BbsArticle Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Bbses\Model
 */
class BbsArticle extends BbsesAppModel {

/**
 * Max length of title
 *
 * @var int
 */
	const BREADCRUMB_TITLE_LENGTH = 20;

/**
 * Max length of title
 *
 * @var int
 */
	const LIST_TITLE_LENGTH = 50;

/**
 * Max length of content
 *
 * @var int
 */
	const LIST_CONTENT_LENGTH = 100;

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'Bbses.BbsArticle',
		'Likes.Like',
		'NetCommons.OriginalKey',
		'Workflow.WorkflowComment',
		'Bbses.BbsesWorkflow',
		'Mails.MailQueue' => array(
			'workflowType' => MailQueueBehavior::MAIL_QUEUE_WORKFLOW_TYPE_WORKFLOW,
			'embedTags' => array(
				'X-SUBJECT' => 'BbsArticle.title',
				'X-BODY' => 'BbsArticle.content',
				'X-BBS_NAME' => 'Bbs.name',
				'X-URL' => array('controller' => 'bbs_articles'),
			),
		),
		'Topics.Topics' => array(
			'fields' => array(
				'title' => 'BbsArticle.title',
				'summary' => 'BbsArticle.content',
				'path' => '/:plugin_key/bbs_articles/view/:block_id/:content_key',
			),
		),
		'Wysiwyg.Wysiwyg' => array(
			'fields' => array('content'),
		),
		'M17n.M17n', //多言語
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Block' => array(
			'className' => 'Blocks.Block',
			'foreignKey' => 'block_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'counterCache' => array(
				'content_count' => array(
					'BbsArticle.is_origin' => true,
					'BbsArticle.is_latest' => true
				),
			),
		),
	);

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->loadModels([
			'Bbs' => 'Bbses.Bbs',
			'BbsArticleTree' => 'Bbses.BbsArticleTree',
			'Language' => 'M17n.Language',
		]);

		if ($this->Language->isMultipleLang()) {
			$conditions = [
				'BbsArticle.key = BbsArticleTree.bbs_article_key',
				'OR' => array(
					'BbsArticle.language_id' => Current::read('Language.id', '0'),
					'BbsArticle.is_translation' => false,
				)
			];
		} else {
			$conditions = [
				'BbsArticle.key = BbsArticleTree.bbs_article_key',
				'BbsArticle.language_id' => Current::read('Language.id', '0'),
			];
		}

		$this->bindModel(array(
			'belongsTo' => array(
				'BbsArticleTree' => array(
					'type' => 'INNER',
					'className' => 'Bbses.BbsArticleTree',
					'foreignKey' => false,
					'conditions' => $conditions,
					'fields' => '',
					'order' => ''
				)
			)
		), false);
	}

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->validate = array_merge($this->validate, array(
			'title' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Title')),
					'required' => true
				),
			),
			'content' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Content')),
					'required' => true
				),
			),

			//status to set in PublishableBehavior.

		));

		if (isset($this->data['BbsArticleTree'])) {
			$this->BbsArticleTree->set($this->data['BbsArticleTree']);
			if (! $this->BbsArticleTree->validates()) {
				$this->validationErrors = array_merge(
					$this->validationErrors, $this->BbsArticleTree->validationErrors
				);
				return false;
			}
		}

		return parent::beforeValidate($options);
	}

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 * @see Model::save()
 */
	public function beforeSave($options = array()) {
		if ($this->Behaviors->loaded('Mails.MailQueue') &&
				! empty($this->data['BbsArticleTree']['root_id'])) {
			/** @see MailQueueBehavior::setSetting() */
			$this->setSetting('editablePermissionKey', 'content_comment_editable');
			$this->setSetting('publishablePermissionKey', 'content_comment_publishable');
			$this->setSetting('workflowType', MailQueueBehavior::MAIL_QUEUE_WORKFLOW_TYPE_COMMENT);
			$this->data[$this->alias]['plugin_key'] = Current::read('Plugin.key');
		}

		if (! isset($this->data['Bbs'])) {
			//メールの件名や本文に掲示板名が含まれているため、もし設定されていなかった場合、セットする。
			$bbs = $this->Bbs->getBbs();
			$this->data['Bbs'] = $bbs['Bbs'];
		}

		parent::beforeSave($options);
	}

/**
 * Called after each successful save operation.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @throws InternalErrorException
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
 * @see Model::save()
 */
	public function afterSave($created, $options = array()) {
		//BbsArticleTree登録
		if (isset($this->BbsArticleTree->data['BbsArticleTree'])) {
			if (! $this->BbsArticleTree->data['BbsArticleTree']['bbs_article_key']) {
				$key = $this->data[$this->alias]['key'];
				$this->BbsArticleTree->data['BbsArticleTree']['bbs_article_key'] = $key;
			}
			if (! $this->BbsArticleTree->save(null, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}

		//Bbsのbbbs_article_modified
		if (isset($this->data['Bbs']['id']) && isset($this->data['Bbs']['key'])) {
			$this->updateBbsByBbsArticle(
				$this->data['Bbs']['key'], $this->data[$this->alias]['language_id']
			);
		}

		//コメント数の更新
		if (isset($this->data['BbsArticleTree']['root_id']) && $this->data['BbsArticleTree']['root_id']) {
			$this->updateBbsArticleChildCount(
				$this->data['BbsArticleTree']['root_id'],
				$this->data[$this->alias]['language_id']
			);
		}

		parent::afterSave($created, $options);
	}

/**
 * Save BbsArticle
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveBbsArticle($data) {
		//トランザクションBegin
		$this->begin();

		//バリデーション
		$this->set($data);
		if (! $this->validates()) {
			return false;
		}

		try {
			//登録処理
			$bbsArticle = $this->save(null, false);
			if (! $bbsArticle) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//$this->BbsArticleTree->recover('parent');

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return $bbsArticle;
	}

/**
 * Delete BbsArticle
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function deleteBbsArticle($data) {
		$this->set($data);

		$bbsArticleTree = $this->BbsArticleTree->find('first', array(
			'recursive' => -1,
			'fields' => array('lft', 'rght'),
			//'fields' => array('sort_key'),
			'conditions' => array(
				'bbs_article_key' => $this->data['BbsArticle']['key']
			),
		));
		if (! $bbsArticleTree) {
			return false;
		}

		$bbsArticleTrees = $this->BbsArticleTree->find('list', array(
			'recursive' => -1,
			'fields' => array('id', 'bbs_article_key'),
			'conditions' => array(
				//'sort_key LIKE' => '' . $bbsArticleTree['BbsArticleTree']['lft'] . '%',
				'lft >=' => $bbsArticleTree['BbsArticleTree']['lft'],
				'rght <=' => $bbsArticleTree['BbsArticleTree']['rght'],
			),
		));

		//トランザクションBegin
		$this->begin();
		try {
			//BbsArticleの削除
			$this->contentKey = $bbsArticleTrees;
			$conditions = array(
				$this->alias . '.key' => array_values($bbsArticleTrees)
			);
			if (! $this->deleteAll($conditions, false, true)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//Treeデータの削除
			$conditions = array(
				$this->BbsArticleTree->alias . '.bbs_article_key' => $this->data['BbsArticle']['key']
			);
			if (! $this->BbsArticleTree->deleteAll($conditions, false, true)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//Bbsのbbs_article_modified
			$this->updateBbsByBbsArticle(
				$data['Bbs']['key'], $data['BbsArticle']['language_id']
			);

			if (isset($this->data['BbsArticleTree']['root_id']) &&
					$this->data['BbsArticleTree']['root_id']) {
				$this->updateBbsArticleChildCount(
					$this->data['BbsArticleTree']['root_id'],
					$this->data[$this->alias]['language_id']
				);
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		return true;
	}

/**
 * コンテンツの編集権限があるかどうかのチェック
 *
 * WorkflowBehavior::canEditWorkflowContent をoverride
 * - 編集権限あり(content_editable)
 * - 子記事がない
 * - 自分自身のコンテンツ
 *
 * @param array $data BbsArticle data
 * @return bool true:編集可、false:編集不可
 */
	public function canEditWorkflowContent($data) {
		if (Current::permission('content_editable')) {
			return true;
		}
		if (! isset($data['BbsArticle']['created_user'])) {
			return false;
		}

		$childArticle = $this->BbsArticleTree->findByParentId(
			$data['BbsArticleTree']['id'],
			'id',
			null,
			-1
		);
		if ($childArticle) {
			return false;
		}

		if (empty($data['BbsArticleTree']['root_id'])) {
			$permission = 'content_creatable';
		} else {
			$permission = 'content_comment_creatable';
		}

		return (Current::permission($permission) &&
			((int)$data['BbsArticle']['created_user'] === (int)Current::read('User.id')));
	}

}
