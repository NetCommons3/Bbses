<?php
/**
 * BbsArticlesController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowControllerViewTest', 'Workflow.TestSuite');

/**
 * BbsArticlesController Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Bbses\Test\Case\Controller\BbsArticlesController
 */
class BbsArticlesControllerViewTest extends WorkflowControllerViewTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.likes.like',
		'plugin.likes.likes_user',
		'plugin.bbses.bbs',
		'plugin.bbses.block_setting_for_bbs',
		'plugin.bbses.bbs_frame_setting',
		'plugin.bbses.bbs_article',
		'plugin.bbses.bbs_article_tree',
		'plugin.workflow.workflow_comment',
	);

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'bbses';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'bbs_articles';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->BbsArticle = ClassRegistry::init('Bbses.BbsArticle');
		$this->BbsArticle->Behaviors->unload('Like');
		$this->BbsArticleTree = ClassRegistry::init('Bbses.BbsArticleTree');
		$this->BbsArticleTree->Behaviors->unload('Like');
	}

/**
 * viewアクションのテスト用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderView() {
		$results = array();

		//ログインなし
		//--コンテンツあり
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_3'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[1] = Hash::merge($results[0], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => false, 'url' => array()),
		));
		$results[2] = Hash::merge($results[0], array( //コメント（なし）
			'assert' => array('method' => 'assertActionLink', 'action' => 'reply', 'linkExist' => false, 'url' => array()),
		));
		//--コンテンツなし
		$results[3] = array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null, 'key' => null),
			'assert' => array('method' => 'assertEquals', 'expected' => 'emptyRender'),
			'exception' => null, 'return' => 'viewFile'
		);

		return $results;
	}

/**
 * viewアクションのテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByCreatable() {
		$results = array();
		//作成権限のみ(一般が書いた記事＆一度公開している)
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_3'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[1] = Hash::merge($results[0], array( //（承認済み記事は編集不可）
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		));
		//作成権限のみ(一般が書いた記事＆公開前)
		$results[2] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_4'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[3] = Hash::merge($results[2], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		));
		//作成権限のみ(他人が書いた記事＆公開中、子記事)（root_idとparent_idが異なる）
		$results[4] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_9'),
			'assert' => array('method' => 'assertRedirect', 'value' => '/bbses/bbs_articles/view/2/bbs_article_7?frame_id=6#/bbs-article-9'),
		);

		//作成権限のみ(他人が書いた記事＆公開中、子記事)（root_idとparent_idが同一）
		$results[7] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_8'),
			'assert' => array('method' => 'assertRedirect', 'value' => '/bbses/bbs_articles/view/2/bbs_article_7?frame_id=6#/bbs-article-8'),
		);
		//--（子記事に'parent_id'あり）
		$results[9] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_7'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[10] = Hash::merge($results[9], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => false, 'url' => array()),
		));
		//作成権限のみ(他人が書いた記事＆公開前)
		$results[11] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_5'),
			'assert' => null,
			'exception' => 'BadRequestException',
		);
		//--コンテンツなし
		$results[12] = array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null, 'key' => null),
			'assert' => array('method' => 'assertEquals', 'expected' => 'emptyRender'),
			'exception' => null, 'return' => 'viewFile'
		);
		//--パラメータ不正(keyに該当する記事が存在しない)
		$results[13] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_99'),
			'assert' => null,
			'exception' => 'BadRequestException',
		);
		//--BBSなし
		$results[14] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_xx'),
			'assert' => 'null',
			'exception' => 'BadRequestException',
		);
		$results[15] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_xx'),
			'assert' => 'null',
			'exception' => 'BadRequestException',
			'return' => 'json'
		);
		// 一般の記事に返信がつくと編集できない
		$results[16] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_14'),
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => false, 'url' => array()),
		);

		return $results;
	}

/**
 * viewアクションのテスト(編集権限、公開権限なし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewByEditable() {
		$results = array();

		//編集権限あり（chef_userが書いた記事一度も公開していない）
		//--コンテンツあり
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_5'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//チェック
		//--編集ボタン
		$results[1] = Hash::merge($results[0], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		));
		//--コメントボタン
		$results[2] = Hash::merge($results[0], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'reply', 'linkExist' => false, 'url' => array()),
		));
		//編集権限あり（chef_userが書いた記事公開）
		//--コンテンツあり
		$results[3] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_6'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//チェック
		//--編集ボタン
		$results[4] = Hash::merge($results[3], array( //なし(公開すると編集不可)
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		));
		//--コメントボタン
		$results[5] = Hash::merge($results[3], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'reply', 'linkExist' => true, 'url' => array()),
		));
		//--コンテンツなし
		$results[6] = array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null, 'key' => null),
			'assert' => array('method' => 'assertEquals', 'expected' => 'emptyRender'),
			'exception' => null, 'return' => 'viewFile'
		);
		//フレームID指定なしテスト
		$results[7] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '2', 'key' => 'bbs_article_3'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[8] = Hash::merge($results[3], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		));
		//根記事が取得できない
		$results[9] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '2', 'key' => 'bbs_article_10'),
			'assert' => 'null',
			'exception' => 'BadRequestException',
		);
		$results[10] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '2', 'key' => 'bbs_article_10'),
			'assert' => 'null',
			'exception' => 'BadRequestException',
			'return' => 'json'
		);
		//親記事が取得できない
		$results[11] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '2', 'key' => 'bbs_article_11'),
			'assert' => 'null',
			'exception' => 'BadRequestException',
		);
		$results[12] = array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '2', 'key' => 'bbs_article_11'),
			'assert' => 'null',
			'exception' => 'BadRequestException',
			'return' => 'json'
		);
		// 一般の記事に返信があっても、編集権限あれば可能
		$results[16] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_14'),
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		);

		return $results;
	}

/**
 * viewアクションのテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderViewError
 * @return void
 */
	public function testViewError($urlOptions, $assert, $exception = null, $return = 'view') {
		//Exception
		ClassRegistry::removeObject('BbsesWorkflowBehavior');
		$workflowBehaviorMock = $this->getMock('BbsesWorkflowBehavior', ['canReadWorkflowContent']);
		ClassRegistry::addObject('BbsesWorkflowBehavior', $workflowBehaviorMock);
		$this->BbsArticle->Behaviors->unload('BbsesWorkflow');
		$this->BbsArticle->Behaviors->load('BbsesWorkflow', $this->BbsArticle->actsAs['Bbses.BbsesWorkflow']);

		$workflowBehaviorMock
			->expects($this->once())
			->method('canReadWorkflowContent')
			->will($this->returnValue(false));

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'view',
		), $urlOptions);

		$this->_testGetAction($url, $assert, $exception, $return);
	}

/**
 * viewアクション用DataProvider
 *
 * #### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewError() {
		$results = array();

		// 参照不可のテスト
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_10'),
			'assert' => null,
			'exception' => 'BadRequestException',
		);
		$results[1] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_10'),
			'assert' => null,
			'exception' => 'BadRequestException',
			'return' => 'json'
		);
		return $results;
	}

/**
 * viewアクション(コメントボタンの確認)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderViewGetByPublishable
 * @return void
 */
	public function testEditGetByPublishable($urlOptions, $assert, $exception = null, $return = 'view') {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR);

		$this->testView($urlOptions, $assert, $exception, $return);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * viewアクション(コメントボタンの確認)用DataProvider
 *
 * #### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderViewGetByPublishable() {
		//公開中の記事
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_3'),
			'assert' => null
		);
		//チェック
		//--編集ボタン
		$results[1] = Hash::merge($results[0], array( //あり
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		));
		//--コメントボタン
		$results[2] = Hash::merge($results[0], array( //あり
			'assert' => array('method' => 'assertActionLink', 'action' => 'reply', 'linkExist' => true, 'url' => array()),
		));

		//公開前の記事
		$results[3] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_4'),
			'assert' => null
		);
		//チェック
		//--編集ボタン
		$results[4] = Hash::merge($results[3], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		));
		//--コメントボタン
		$results[5] = Hash::merge($results[3], array( //なし
			'assert' => array('method' => 'assertActionLink', 'action' => 'reply', 'linkExist' => false, 'url' => array()),
		));
		//--未承認のコメント（承認ボタン）
		$results[6] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_12', '#' => '/bbs_article_13'),
			'assert' => array(
				'method' => 'assertInput', 'type' => 'button',
				'name' => 'save_' . WorkflowComponent::STATUS_PUBLISHED, 'value' => null
			),
		);
		$results[7] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2', 'key' => 'bbs_article_12', '#' => '/bbs_article_13'),
			'assert' => array(
				'method' => 'assertInput', 'type' => 'form',
				'name' => null, 'value' => '/bbses/bbs_articles/approve/2/bbs_article_13?frame_id=6'
			),
		);

		return $results;
	}

}
