<?php
/**
 * 根記事リスト(index)の根記事一覧 Element
 *
 * ## elementの引数
 * * $bbsArticle: 記事データ
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<article class="clearfix bbs-all-list-root">
	<h2 class="pull-left">
		<?php
			//根記事タイトル
			echo $this->element('BbsArticles/index_bbs_article_title', array(
				'bbsArticle' => $bbsArticle,
			));
		?>
	</h2>
	<?php //子記事数  ?>
	<div class="pull-left bbs-root-comment">
		<?php if ($bbsSetting['use_comment']) : ?>
			<div class="inline-block bbses-comment-count">
				<span class="glyphicon glyphicon-comment text-muted" aria-hidden="true"></span>
				<?php if (Hash::get($bbsArticle['BbsArticleTree'], 'approval_bbs_article_child_count')) : ?>
					<?php
						echo __d(
							'bbses',
							'%s comments(%s approval waited comments)',
							$bbsArticle['BbsArticleTree']['bbs_article_child_count'],
							Hash::get($bbsArticle['BbsArticleTree'], 'approval_bbs_article_child_count')
						);
					?>
				<?php else : ?>
					<?php
						echo __d(
							'bbses',
							'%s comments',
							$bbsArticle['BbsArticleTree']['bbs_article_child_count']
						);
					?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php //投稿日時  ?>
	<div class="pull-right bbs-article-creator">
		<span class="bbs-article-created text-muted">
			<?php echo $this->Date->dateFormat($bbsArticle['BbsArticle']['created']); ?>
		</span>
		<span class="bbs-article-handle">
			<?php echo $this->NetCommonsHtml->handleLink($bbsArticle, array('avatar' => true)); ?>
		</span>
	</div>
</article>
<article class="bbs-all-list-children">
	<?php
		//子記事
		if (isset($treeLists[$bbsArticle['BbsArticleTree']['id']])) {
			foreach ($treeLists[$bbsArticle['BbsArticleTree']['id']] as $treeId => $childArticle) {
				echo $this->element(
					'BbsArticles/' . Hash::get($bbsFrameSetting, ['display_type'], 'flat') . '/index_bbs_child_article',
					array(
						'bbsArticle' => $bbsArticleTitles[$treeId],
						'indent' => substr_count($childArticle, '_') + 1
					)
				);
			}
		}
	?>
</article>
