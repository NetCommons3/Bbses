<?php
/**
 * 根記事リスト(index)のフラット表示 Element
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
<article>
	<h2 class="clearfix">
		<?php
			//根記事タイトル
			echo $this->element('BbsArticles/index_bbs_article_title', array(
				'bbsArticle' => $bbsArticle,
			));
		?>
	</h2>
	<article>
		<?php
			//記事内容
			echo $bbsArticle['BbsArticle']['content'];
		?>
	</article>
	<?php //記事フッター  ?>
	<footer class="clearfix">
		<div class="pull-left">
			<?php if ($bbsSetting['use_comment']) : ?>
				<div class="inline-block bbses-comment-count">
					<span class="glyphicon glyphicon-comment text-muted" aria-hidden="true"></span>
					<?php if (!empty($bbsArticle['BbsArticleTree']['approval_bbs_article_child_count'])) : ?>
						<?php
							echo __d(
								'bbses',
								'%s comments(%s approval waited comments)',
								$bbsArticle['BbsArticleTree']['bbs_article_child_count'],
								$bbsArticle['BbsArticleTree']['approval_bbs_article_child_count']
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
			<?php echo $this->Like->buttons('BbsArticle', $bbsSetting, $bbsArticle); ?>
		</div>

		<div class="pull-right">
			<span class="bbs-article-created text-muted">
				<?php echo $this->Date->dateFormat($bbsArticle['BbsArticle']['created']); ?>
			</span>
			<span class="bbs-article-handle">
				<?php echo $this->NetCommonsHtml->handleLink($bbsArticle, array('avatar' => true)); ?>
			</span>
		</div>
	</footer>

</article>
