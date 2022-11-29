<?php
/**
 * BbsArticles Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BbsesAppController', 'Bbses.Controller');
App::uses('CakeText', 'Utility');
App::uses('WorkflowComponent', 'Workflow.Controller/Component');
App::uses('BbsFrameSetting', 'Bbses.Model');

/**
 * BbsArticles Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Bbses\Controller
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class BbsArticlesController extends BbsesAppController {

/**
 * サイト内リンクのID
 *
 * @var int
 */
	const LINK_ID_FORMAT = 'bbs-article-%s';

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Bbses.BbsFrameSetting',
		'Bbses.BbsArticle',
		'Bbses.BbsArticleTree',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'add,edit,delete' => 'content_creatable',
				'reply' => 'content_comment_creatable',
				'approve' => 'content_comment_publishable',
			),
		),
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Bbses.BbsesForm',
		'Likes.Like',
		'NetCommons.DisplayNumber',
		'Workflow.Workflow',
	);

/**
 * beforeRender
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		if (! Current::read('Block.id')) {
			$this->setAction('emptyRender');
			return false;
		}
		if (! $bbs = $this->Bbs->getBbs()) {
			$this->setAction('throwBadRequest');
			return false;
		}
		$this->set('bbs', $bbs['Bbs']);
		$this->set('bbsSetting', $bbs['BbsSetting']);

		$bbsFrameSetting = $this->BbsFrameSetting->getBbsFrameSetting(true);
		$this->set('bbsFrameSetting', $bbsFrameSetting['BbsFrameSetting']);
	}

/**
 * index
 *
 * @return void
 * @throws Exception
 */
	public function index() {
		$query = array();
		//条件
		$query['conditions'] = $this->BbsArticle->getWorkflowConditions(array(
			'BbsArticleTree.parent_id' => null,
			'BbsArticle.bbs_key' => $this->viewVars['bbs']['key'],
		));

		//ソート
		$options = array(
			'BbsArticle.created.desc' => array(
				'label' => __d('bbses', 'Latest post order'),
				'sort' => 'BbsArticle.created',
				'direction' => 'desc'
			),
			'BbsArticle.created.asc' => array(
				'label' => __d('bbses', 'Older post order'),
				'sort' => 'BbsArticle.created',
				'direction' => 'asc'
			),
			'BbsArticleTree.bbs_article_child_count.desc' => array(
				'label' => __d('bbses', 'Descending order of comments'),
				'sort' => 'BbsArticleTree.bbs_article_child_count',
				'direction' => 'desc'
			),
		);
		$this->set('options', $options);

		$currentSort = isset($this->params['named']['sort'])
			? $this->params['named']['sort']
			: 'BbsArticle.created';
		$currentDirection = isset($this->params['named']['direction'])
			? $this->params['named']['direction']
			: 'desc';
		if (! isset($options[$currentSort . '.' . $currentDirection])) {
			$currentSort = 'BbsArticle.created';
			$currentDirection = 'desc';
		}
		$this->set('curretSort', $currentSort);
		$this->set('curretDirection', $currentDirection);

		$query['order'] = array($currentSort => $currentDirection);

		//表示件数
		$limit = $this->viewVars['bbsFrameSetting']['articles_per_page'];
		if (isset($this->params['named']['limit'])) {
			$limit = $this->params['named']['limit'];
		}
		$query['limit'] = (int)$limit;

		$this->Paginator->settings = $query;
		$recursive = $this->BbsArticle->recursive;
		$this->BbsArticle->recursive = 0;
		try {
			$bbsArticles = $this->Paginator->paginate('BbsArticle');
		} catch (Exception $ex) {
			CakeLog::error($ex);
			throw $ex;
		}
		$this->BbsArticle->recursive = $recursive;

		//子記事のTreeデータ取得
		$articleTreeIds = [];
		$treeLists = [];
		foreach ($bbsArticles as $bbsArticle) {
			//Treeリスト取得(全件表示場合)
			$treeId = $bbsArticle['BbsArticleTree']['id'];
			if ($this->viewVars['bbsFrameSetting']['display_type'] === BbsFrameSetting::DISPLAY_TYPE_ALL) {
				$conditions = array(
					'BbsArticleTree.root_id' => $treeId,
					'BbsArticleTree.bbs_key' => $this->viewVars['bbs']['key'],
				);
				$treeList = $this->BbsArticleTree->generateTreeList(
					$conditions, null, null, '_', -1
				);
				$treeLists[$treeId] = $treeList;
			}
			//Tree Idをセット(子記事件数を取得するため)
			$articleTreeIds[] = $treeId;
		}
		$this->set('treeLists', $treeLists);

		//子記事件数取得
		$bbsArticles = $this->BbsArticle->getChildrenArticleCounts(
			$this->viewVars['bbs']['key'], $bbsArticles, $articleTreeIds
		);
		$this->set('bbsArticles', $bbsArticles);

		//全件表示の場合子記事データ取得
		if ($this->viewVars['bbsFrameSetting']['display_type'] === BbsFrameSetting::DISPLAY_TYPE_ALL) {
			$bbsArticleTitles = $this->BbsArticle->getChildrenArticleTitles($articleTreeIds);
			$this->set('bbsArticleTitles', $bbsArticleTitles);
		}
	}

/**
 * view
 *
 * @return void
 * @throws BadRequestException throw
 */
	public function view() {
		//参照権限チェック
		if (! $this->BbsArticle->canReadWorkflowContent()) {
			return $this->throwBadRequest();
		}

		$bbsArticleKey = isset($this->request->params['key']) ? $this->request->params['key'] : null;

		//カレント記事の取得
		$bbsArticle = $this->BbsArticle->getWorkflowContents('first', array(
			'recursive' => 0,
			'conditions' => array(
				$this->BbsArticle->alias . '.bbs_key' => $this->viewVars['bbs']['key'],
				$this->BbsArticle->alias . '.key' => $bbsArticleKey
			)
		));
		if (! $bbsArticle) {
			return $this->throwBadRequest();
		}

		//事前準備
		$result = $this->__prepare($bbsArticle);
		if (! $result) {
			return $this->throwBadRequest();
		}
		//$this->set('currentBbsArticle', $bbsArticle);

		//新着データを既読にする
		$this->BbsArticle->saveTopicUserStatus($bbsArticle);

		//根記事を指していない場合、根記事＋ページ内リンクに遷移する
		if ($bbsArticle['BbsArticleTree']['article_no'] !== '1') {
			$url = NetCommonsUrl::blockUrl(array(
				'action' => 'view',
				'key' => $this->viewVars['rootBbsArticle']['BbsArticle']['key'],
				'#' => '/' . sprintf(self::LINK_ID_FORMAT, $bbsArticle['BbsArticleTree']['id'])
			));
			return $this->redirect($url);
		}

		//子記事の取得
		$this->BbsArticleTree->Behaviors->load('Tree', array(
			'scope' => $this->BbsArticle->getWorkflowConditions()
		));

		if ($this->viewVars['bbsFrameSetting']['display_type'] === BbsFrameSetting::DISPLAY_TYPE_FLAT) {
			$children = $this->BbsArticleTree->children(
				$bbsArticle['BbsArticleTree']['id'], false, null, 'BbsArticleTree.id DESC', null, 1, 1
			);
		} else {
			$this->viewVars['bbsFrameSetting']['display_type'] = BbsFrameSetting::DISPLAY_TYPE_ROOT;
			$children = $this->BbsArticleTree->children(
				$bbsArticle['BbsArticleTree']['id'], false, null, 'BbsArticleTree.lft ASC', null, 1, 1
				//$bbsArticle['BbsArticleTree']['id'], false, null, 'BbsArticleTree.sort_key ASC', null, 1, 1
			);
			//Treeリスト取得
			$conditions = array(
				'BbsArticleTree.root_id' => $bbsArticle['BbsArticleTree']['id'],
			);
			$treeList = $this->BbsArticleTree->generateTreeList(
				$conditions, null, null, '_', 0
			);
			$this->set('treeList', $treeList);
		}
		$bbsArticleChildren = [];
		foreach ($children as $child) {
			$bbsArticleChildren[$child['BbsArticleTree']['id']] = $child;
		}

		$this->set('bbsArticleChildren', $children);
	}

/**
 * add
 *
 * @return void
 */
	public function add() {
		$this->view = 'edit';

		if ($this->request->is('post')) {
			$data = $this->data;

			$data['BbsArticle']['status'] = $this->Workflow->parseStatus();
			$data['BbsArticleTree']['article_no'] = 1;
			unset($data['BbsArticle']['id']);

			if ($bbsArticle = $this->BbsArticle->saveBbsArticle($data)) {
				$url = NetCommonsUrl::blockUrl(array(
					'action' => 'view',
					'key' => $bbsArticle['BbsArticle']['key']
				));
				return $this->redirect($url);
			}
			$this->NetCommons->handleValidationError($this->BbsArticle->validationErrors);

		} else {
			$this->request->data += $this->BbsArticle->create(array(
					'bbs_key' => $this->viewVars['bbs']['key'],
				));
			$this->request->data += $this->BbsArticleTree->create(array(
					'bbs_key' => $this->viewVars['bbs']['key'],
					'post_no' => 1,
				));
			$this->request->data['Bbs'] = $this->viewVars['bbs'];
			$this->request->data['Frame'] = Current::read('Frame');
			$this->request->data['Block'] = Current::read('Block');
		}
	}

/**
 * reply
 *
 * @return void
 */
	public function reply() {
		$bbsArticleKey = isset($this->request->params['key']) ? $this->request->params['key'] : null;

		$bbsArticle = $this->BbsArticle->getWorkflowContents('first', array(
			'recursive' => 0,
			'conditions' => array(
				$this->BbsArticle->alias . '.bbs_key' => $this->viewVars['bbs']['key'],
				$this->BbsArticle->alias . '.key' => $bbsArticleKey
			)
		));

		if (!isset($bbsArticle['BbsArticle']['status']) ||
			$bbsArticle['BbsArticle']['status'] !== WorkflowComponent::STATUS_PUBLISHED) {
			return $this->throwBadRequest();
		}

		//事前準備
		$this->set('currentBbsArticle', $bbsArticle);

		if ($this->request->is('post')) {
			$data = $this->data;
			$data['BbsArticle']['status'] = $this->Workflow->parseStatus();

			$articleNo = $this->BbsArticleTree->getMaxNo($data['BbsArticleTree']['root_id']) + 1;
			$data['BbsArticleTree']['article_no'] = $articleNo;
			unset($data['BbsArticle']['id']);

			$bbsArticle = $this->BbsArticle->saveBbsArticle($data);
			if ($bbsArticle) {
				$url = NetCommonsUrl::blockUrl(array(
					'action' => 'view',
					'key' => $bbsArticle['BbsArticle']['key'],
				));
				return $this->redirect($url);
			}
			$this->NetCommons->handleValidationError($this->BbsArticle->validationErrors);

		} else {
			if ($bbsArticle['BbsArticleTree']['root_id'] > 0) {
				$rootId = (int)$bbsArticle['BbsArticleTree']['root_id'];
			} else {
				$rootId = (int)$bbsArticle['BbsArticleTree']['id'];
			}
			$title = $this->BbsArticle->getReplyTitle($bbsArticle['BbsArticle']['title']);
			if (isset($this->params->query['quote']) && $this->params->query['quote']) {
				$content = $this->BbsArticle->getReplyContent($bbsArticle['BbsArticle']['content']);
			} else {
				$content = null;
			}
			$this->request->data += $this->BbsArticle->create(array(
					'bbs_key' => $this->viewVars['bbs']['key'],
					'title' => $title,
					'content' => $content,
				));
			$this->request->data += $this->BbsArticleTree->create(array(
					'bbs_key' => $this->viewVars['bbs']['key'],
					'root_id' => $rootId,
					'parent_id' => $bbsArticle['BbsArticleTree']['id'],
				));
			$this->request->data['Bbs'] = $this->viewVars['bbs'];
			$this->request->data['Frame'] = Current::read('Frame');
			$this->request->data['Block'] = Current::read('Block');
		}
	}

/**
 * edit
 *
 * @return void
 */
	public function edit() {
		$this->view = 'edit';

		$bbsArticleKey = isset($this->request->params['key']) ? $this->request->params['key'] : null;
		if ($this->request->is('put')) {
			$bbsArticleKey = $this->data['BbsArticle']['key'];
		}

		$bbsArticle = $this->BbsArticle->getWorkflowContents('first', array(
			'recursive' => 0,
			'conditions' => array(
				$this->BbsArticle->alias . '.bbs_key' => $this->viewVars['bbs']['key'],
				$this->BbsArticle->alias . '.key' => $bbsArticleKey
			)
		));
		if (empty($bbsArticle)) {
			return $this->throwBadRequest();
		}

		// WorkflowBehavior::canEditWorkflowContentをBbsArticle::canEditWorkflowContentでoverride
		if (! $this->BbsArticle->canEditWorkflowContent($bbsArticle)) {
			return $this->throwBadRequest();
		}

		if ($this->request->is('put')) {
			$data = $this->data;
			$data['BbsArticle']['status'] = $this->Workflow->parseStatus();
			unset($data['BbsArticle']['id']);

			$bbsArticle = $this->BbsArticle->saveBbsArticle($data);
			if ($bbsArticle) {
				$url = NetCommonsUrl::blockUrl(array(
					'action' => 'view',
					'key' => $bbsArticle['BbsArticle']['key']
				));
				return $this->redirect($url);
			}
			$this->NetCommons->handleValidationError($this->BbsArticle->validationErrors);

		} else {
			$this->request->data = $bbsArticle;
			$this->request->data['Bbs'] = $this->viewVars['bbs'];
			$this->request->data['Frame'] = Current::read('Frame');
			$this->request->data['Block'] = Current::read('Block');
		}

		$comments = $this->BbsArticle->getCommentsByContentKey($this->request->data['BbsArticle']['key']);
		$this->set('comments', $comments);
	}

/**
 * delete
 *
 * @return void
 */
	public function delete() {
		if (! $this->request->is('delete')) {
			return $this->throwBadRequest();
		}

		$bbsArticle = $this->BbsArticle->getWorkflowContents('first', array(
			'recursive' => 0,
			'conditions' => array(
				$this->BbsArticle->alias . '.bbs_key' => $this->viewVars['bbs']['key'],
				$this->BbsArticle->alias . '.key' => $this->data['BbsArticle']['key']
			)
		));

		//削除権限チェック
		if (! $this->BbsArticle->canDeleteWorkflowContent($bbsArticle)) {
			return $this->throwBadRequest();
		}

		//親記事の取得
		if ($bbsArticle['BbsArticleTree']['parent_id'] > 0) {
			$parentBbsArticle = $this->BbsArticle->getWorkflowContents('first', array(
				'recursive' => 0,
				'conditions' => array(
					$this->BbsArticleTree->alias . '.id' => $bbsArticle['BbsArticleTree']['parent_id'],
				)
			));
			if (! $parentBbsArticle) {
				return $this->throwBadRequest();
			}
		}

		if (! $this->BbsArticle->deleteBbsArticle($this->data)) {
			return $this->throwBadRequest();
		}

		if (isset($parentBbsArticle)) {
			$url = NetCommonsUrl::blockUrl(array(
				'action' => 'view',
				'key' => $parentBbsArticle['BbsArticle']['key']
			));
		} else {
			$url = NetCommonsUrl::backToPageUrl();
		}
		return $this->redirect($url);
	}

/**
 * approve
 *
 * @return mixed
 */
	public function approve() {
		if (! $this->request->is('put')) {
			return $this->throwBadRequest();
		}

		$bbsArticle = $this->BbsArticle->getWorkflowContents('first', array(
			'recursive' => 0,
			'conditions' => array(
				$this->BbsArticle->alias . '.bbs_key' => $this->data['BbsArticle']['bbs_key'],
				$this->BbsArticle->alias . '.key' => $this->data['BbsArticle']['key']
			)
		));
		if (! $bbsArticle) {
			return $this->throwBadRequest();
		}
		//ステータスチェック
		if ($bbsArticle['BbsArticle']['status'] !== WorkflowComponent::STATUS_APPROVAL_WAITING) {
			return $this->throwBadRequest();
		}

		$data['BbsArticle'] = $bbsArticle['BbsArticle'];
		unset($data['BbsArticle']['created'], $data['BbsArticle']['created_user']);
		unset($data['BbsArticle']['modified'], $data['BbsArticle']['modified_user']);
		unset($data['BbsArticle']['id']);
		$data['BbsArticle']['status'] = $this->Workflow->parseStatus();

		//リクエストのステータスチェック
		if ($data['BbsArticle']['status'] !== WorkflowComponent::STATUS_PUBLISHED) {
			return $this->throwBadRequest();
		}

		$result = $this->BbsArticle->saveBbsArticle($data);
		if ($result) {
			$this->NetCommons->setFlashNotification(
				__d('net_commons', 'Successfully saved.'), array('class' => 'success')
			);

			$url = NetCommonsUrl::blockUrl(array(
				'action' => 'view',
				'key' => $this->data['BbsArticle']['key']
			));
			return $this->redirect($url);
		}
		$this->NetCommons->handleValidationError($this->BbsArticle->validationErrors);

		return $this->throwBadRequest();
	}

/**
 * 事前準備
 *
 * @param array $bbsArticle 記事データ
 * @return bool
 */
	private function __prepare($bbsArticle) {
		//根記事の取得
		if ($bbsArticle['BbsArticleTree']['root_id'] > 0) {
			$result = $this->__setBbsArticleByTreeId(
				'rootBbsArticle', $bbsArticle['BbsArticleTree']['root_id']
			);
			if (! $result) {
				return false;
			}
		} else {
			$this->set('rootBbsArticle', $bbsArticle);
		}

		if (! $bbsArticle['BbsArticleTree']['parent_id']) {
			return true;
		}

		//親記事の取得
		if ($bbsArticle['BbsArticleTree']['parent_id'] !== $bbsArticle['BbsArticleTree']['root_id']) {
			$result = $this->__setBbsArticleByTreeId(
				'parentBbsArticle', $bbsArticle['BbsArticleTree']['parent_id']
			);
			if (! $result) {
				return false;
			}
		} else {
			$this->set('parentBbsArticle', $this->viewVars['rootBbsArticle']);
			$result = true;
		}

		//親の親記事の取得
		if ($this->viewVars['parentBbsArticle']['BbsArticleTree']['parent_id'] > 0) {
			$result = $this->__setBbsArticleByTreeId(
				'parentParentBbsArticle', $this->viewVars['parentBbsArticle']['BbsArticleTree']['parent_id']
			);
		}
		return $result;
	}

/**
 * 記事データをviewにセットする
 *
 * @param string $viewVarsKey viewVarsのキー
 * @param int $bbsArticleTreeId BbsArticleTreeId
 * @return bool
 */
	private function __setBbsArticleByTreeId($viewVarsKey, $bbsArticleTreeId) {
		$bbsArticle = $this->BbsArticle->getWorkflowContents('first', array(
			'recursive' => 0,
			'conditions' => array(
				$this->BbsArticleTree->alias . '.id' => $bbsArticleTreeId,
			)
		));
		if (! $bbsArticle) {
			return false;
		}
		$this->set($viewVarsKey, $bbsArticle);

		return true;
	}

}
