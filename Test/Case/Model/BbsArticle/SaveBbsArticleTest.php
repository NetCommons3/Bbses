<?php
/**
 * BbsArticle::saveBbsArticle()のテスト
 *
 * @property BbsArticle $BbsArticle
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowSaveTest', 'Workflow.TestSuite');

/**
 * BbsArticle::saveBbsArticle()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Bbses\Test\Case\Model\BbsArticle
 */
class BbsArticleSaveBbsArticleTest extends WorkflowSaveTest {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'bbses';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.likes.like',
		'plugin.likes.likes_user',
		'plugin.bbses.bbs',
		'plugin.bbses.bbs_setting',
		'plugin.bbses.bbs_frame_setting',
		'plugin.bbses.bbs_article',
		'plugin.bbses.bbs_article_tree',
		'plugin.workflow.workflow_comment',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'BbsArticle';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveBbsArticle';

/**
 * テストDataの取得
 *
 * @param string $bbsArticleKey bbsArticleKey
 * @return array
 */
	private function __getData($bbsArticleKey = 'bbs_article_1') {
		$frameId = '6';
		$blockId = '2';
		$bbsKey = 'bbs_1';
		$blockKey = 'block_2';
		$bbsId = '2';
		if ($bbsArticleKey === 'bbs_article_1') {
			$bbsArticleTreeId = '1';
			$bbsArticleId = '1';
			$rootId = null;
		} else {
			$bbsArticleId = null;
			$bbsArticleTreeId = null;
			$rootId = '1';
		}

		$data = array(
			//'save_1' => null,
			'Frame' => array(
				'id' => $frameId,
				'block_id' => $blockId,
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
			),
			'Bbs' => array(
				'id' => $bbsId,
				'key' => $bbsKey,
			),
			'BbsArticle' => array(
				'id' => $bbsArticleId,
				'key' => $bbsArticleKey,
				'language_id' => '2',
				'bbs_id' => $bbsId,
				'title' => 'BBS ARTICLE TITLE',
				'title_icon' => null,
				'content' => '<p>CONTENT</p>',
				'status' => WorkflowComponent::STATUS_PUBLISHED,
			),
			'BbsArticleTree' => array(
				'id' => $bbsArticleTreeId,
				'bbs_key' => $bbsKey,
				'bbs_article_key' => $bbsArticleKey,
				'root_id' => $rootId,
				'parent_id' => null,
			),
			'WorkflowComment' => array(
				'comment' => 'WorkflowComment save test'
			),
		);

		return $data;
	}

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$model = $this->_modelName;
		$this->$model->Behaviors->unload('Like');
		$this->$model->Behaviors->unload('Topics');
		$this->$model->BbsArticleTree = ClassRegistry::init('Bbses.BbsArticleTree');
		$this->$model->BbsArticleTree->Behaviors->unload('Like');
	}

/**
 * SaveのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return void
 */
	public function dataProviderSave() {
		return array(
			array($this->__getData()), //修正
			array($this->__getData(null)), //新規
		);
	}

/**
 * SaveのExceptionErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return void
 */
	public function dataProviderSaveOnExceptionError() {
		return array(
			array($this->__getData(), 'Bbses.BbsArticle', 'save'),
			array($this->__getData(null), 'Bbses.BbsArticleTree', 'save'),
		);
	}

/**
 * SaveのValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *
 * @return void
 */
	public function dataProviderSaveOnValidationError() {
		return array(
			array($this->__getData(), 'Bbses.BbsArticle'),
			array($this->__getData(), 'Bbses.BbsArticleTree'),
		);
	}

/**
 * ValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - field フィールド名
 *  - value セットする値
 *  - message エラーメッセージ
 *  - overwrite 上書きするデータ
 *
 * @return void
 */
	public function dataProviderValidationError() {
		return array(
			array($this->__getData(), 'title', '',
				sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Title'))),
			array($this->__getData(), 'content', '',
				sprintf(__d('net_commons', 'Please input %s.'), __d('bbses', 'Content'))),
		);
	}

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data) {
		$model = $this->_modelName;

		//BbsArticleTreeのテスト前のデータ取得
		if (isset($data['BbsArticleTree']['id'])) {
			$before = $this->$model->BbsArticleTree->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data['BbsArticleTree']['id']),
			));
			$before['BbsArticleTree'] = Hash::remove($before['BbsArticleTree'], 'modified');
			$before['BbsArticleTree'] = Hash::remove($before['BbsArticleTree'], 'modified_user');
		} else {
			$max = $this->$model->BbsArticleTree->find('first', array(
				'recursive' => -1,
				'fields' => 'id',
				'order' => array('id' => 'desc')
			));
			$maxId = $max['BbsArticleTree']['id'] + 1;

			$before['BbsArticleTree'] = $data['BbsArticleTree'];
			$before['BbsArticleTree']['id'] = (string)$maxId;
			$before['BbsArticleTree']['lft'] = '27';
			$before['BbsArticleTree']['rght'] = '28';
			$before['BbsArticleTree']['article_no'] = '1';
			$before['BbsArticleTree']['bbs_article_child_count'] = '0';
		}

		//BBS（登録前）
		$conditions = array(
			'key' => $data['Bbs']['key'],
		);
		$bbs = $this->$model->Bbs->find('first', array(
			'recursive' => -1,
			'conditions' => $conditions,
		));
		$beforeCount = $bbs['Bbs']['bbs_article_count'];

		//テスト実施
		$latest = parent::testSave($data);

		//登録処理後のBbsArticleTreeのチェック
		if (isset($data['BbsArticleTree']['id'])) {
			$after = $this->$model->BbsArticleTree->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data['BbsArticleTree']['id']),
			));

		} else {
			$before['BbsArticleTree']['bbs_article_key'] = $latest[$this->$model->alias]['key'];

			$after = $this->$model->BbsArticleTree->find('first', array(
				'recursive' => -1,
				'order' => array('id' => 'desc')
			));
			$after['BbsArticleTree'] = Hash::remove($after['BbsArticleTree'], 'created');
			$after['BbsArticleTree'] = Hash::remove($after['BbsArticleTree'], 'created_user');
		}

		$after['BbsArticleTree'] = Hash::remove($after['BbsArticleTree'], 'modified');
		$after['BbsArticleTree'] = Hash::remove($after['BbsArticleTree'], 'modified_user');

		$this->assertEquals($before['BbsArticleTree'], $after['BbsArticleTree']);

		//BBSチェック
		$bbs = $this->$model->Bbs->find('first', array(
			'recursive' => -1,
			'conditions' => $conditions,
		));
		if (isset($data['BbsArticleTree']['id'])) {
			//更新
			$this->assertEquals(($beforeCount), $bbs['Bbs']['bbs_article_count']);
		} else {
			//新規
			$this->assertEquals(($beforeCount + 1), $bbs['Bbs']['bbs_article_count']);
		}
	}

}
