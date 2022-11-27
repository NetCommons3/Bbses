<?php
/**
 * BbsArticleFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * BbsArticleFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Bbses\Test\Fixture
 */
class BbsArticleFixture extends CakeTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '3',
			'is_active' => false,
			'is_latest' => true,
			'key' => 'bbs_article_1',
			'title' => 'Lorem ipsum dolor sit amet',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 1,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 1,
			'modified' => '2015-05-14 07:09:55'
		),
		array(
			'id' => '2',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '1',
			'is_active' => '1',
			'is_latest' => '1',
			'key' => 'bbs_article_2',
			'title' => 'Lorem ipsum dolor sit amet',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 1,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 1,
			'modified' => '2015-05-14 07:09:55'
		),
		//(一般が書いた記事＆一度公開している)
		array(
			'id' => '3',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '1',
			'is_active' => '1',
			'is_latest' => '1',
			'key' => 'bbs_article_3',
			'title' => 'Lorem ipsum dolor sit amet',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 4,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 4,
			'modified' => '2015-05-14 07:09:55'
		),

		//(一般が書いた記事＆一度も公開していない)
		array(
			'id' => '4',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '3',
			'is_active' => false,
			'is_latest' => '1',
			'key' => 'bbs_article_4',
			'title' => 'Lorem ipsum dolor sit amet',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 4,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 4,
			'modified' => '2015-05-14 07:09:55'
		),
		//(chef_userが書いた記事＆一度も公開していない)
		array(
			'id' => '5',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '3',
			'is_active' => false,
			'is_latest' => '1',
			'key' => 'bbs_article_5',
			'title' => 'Lorem ipsum dolor sit amet',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 3,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 3,
			'modified' => '2015-05-14 07:09:55'
		),
		//(chef_userが書いた記事＆公開)
		array(
			'id' => '6',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '1',
			'is_active' => '1',
			'is_latest' => '1',
			'key' => 'bbs_article_6',
			'title' => 'Lorem ipsum dolor sit amet',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 3,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 3,
			'modified' => '2015-05-14 07:09:55'
		),
		//(記事返信が2つある記事)
		array(
			'id' => '7',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '1',
			'is_active' => '1',
			'is_latest' => '1',
			'key' => 'bbs_article_7',
			'title' => '記事1',
			'content' => '記事1です。',
			'created_user' => 1,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 1,
			'modified' => '2015-05-14 07:09:55'
		),
		array(
			'id' => '8',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '1',
			'is_active' => '1',
			'is_latest' => '1',
			'key' => 'bbs_article_8',
			'title' => 'Re:記事1',
			'content' => '返信1です。',
			'created_user' => 1,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 1,
			'modified' => '2015-05-14 07:09:55'
		),
		array(
			'id' => '9',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '1',
			'is_active' => '1',
			'is_latest' => '1',
			'key' => 'bbs_article_9',
			'title' => 'Re2:記事１',
			'content' => '返信2です。',
			'created_user' => 1,
			'created' => '2016-08-12 00:00:00',
			'modified_user' => 1,
			'modified' => '2016-08-12 00:00:00'
		),
		//(根記事が不正)
		array(
			'id' => '10',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '1',
			'is_active' => '1',
			'is_latest' => '1',
			'key' => 'bbs_article_10',
			'title' => '記事10',
			'content' => '記事10です。',
			'created_user' => 1,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 1,
			'modified' => '2015-05-14 07:09:55'
		),
		//(親記事が不正)
		array(
			'id' => '11',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '1',
			'is_active' => '1',
			'is_latest' => '1',
			'key' => 'bbs_article_11',
			'title' => '記事11',
			'content' => '記事11です。',
			'created_user' => 1,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 1,
			'modified' => '2015-05-14 07:09:55'
		),
		//記事（子記事が承認待ち）
		array(
			'id' => '12',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '1',
			'is_active' => true,
			'is_latest' => '1',
			'key' => 'bbs_article_12',
			'title' => 'Lorem ipsum dolor sit amet',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 1,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 1,
			'modified' => '2015-05-14 07:09:55'
		),
		//一般が書いた記事(承認待ち)
		array(
			'id' => '13',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '2',
			'is_active' => false,
			'is_latest' => '1',
			'key' => 'bbs_article_13',
			'title' => 'Lorem ipsum dolor sit amet',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 4,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 4,
			'modified' => '2015-05-14 07:09:55'
		),
		//一般が書いた記事（子記事あり）
		array(
			'id' => '14',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '1',
			'is_active' => true,
			'is_latest' => '1',
			'key' => 'bbs_article_14',
			'title' => 'Lorem ipsum dolor sit amet',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 4,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 1,
			'modified' => '2015-05-14 07:09:55'
		),
		//親記事が一般の記事
		array(
			'id' => '15',
			'bbs_key' => 'bbs_1',
			'block_id' => '2',
			'language_id' => '2',
			'status' => '2',
			'is_active' => false,
			'is_latest' => '1',
			'key' => 'bbs_article_15',
			'title' => 'Lorem ipsum dolor sit amet',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created_user' => 4,
			'created' => '2015-05-14 07:09:55',
			'modified_user' => 4,
			'modified' => '2015-05-14 07:09:55'
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('Bbses') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new BbsesSchema())->tables['bbs_articles'];
		parent::init();
	}

}
