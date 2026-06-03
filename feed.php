<?php
// RSS 2.0 news feed - no session required, publicly accessible.

require_once 'config/database.php';
require_once 'models/Article.php';

$db           = Database::getInstance();
$articleModel = new Article($db);
$articles     = $articleModel->getLatestPublished(20);

$scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST']
         . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

header('Content-Type: application/rss+xml; charset=UTF-8');

// Output the XML declaration separately to avoid issues with PHP's short-open tags.
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>The Austro-Asian Times</title>
    <link><?= htmlspecialchars($baseUrl . '/index.php') ?></link>
    <description>News for Northern Australia and Southeast Asia</description>
    <language>en-au</language>
    <lastBuildDate><?= date('r') ?></lastBuildDate>
    <atom:link href="<?= htmlspecialchars($baseUrl . '/feed.php') ?>"
               rel="self" type="application/rss+xml"/>

<?php foreach ($articles as $a):
    $link = htmlspecialchars($baseUrl . '/index.php?route=article&action=show&id=' . $a['id']);
    $desc = htmlspecialchars(mb_substr(strip_tags($a['body']), 0, 300));
?>
    <item>
      <title><?= htmlspecialchars($a['title']) ?></title>
      <link><?= $link ?></link>
      <description><?= $desc ?></description>
      <author><?= htmlspecialchars($a['author_name']) ?></author>
      <pubDate><?= date('r', strtotime($a['updated_at'])) ?></pubDate>
      <guid isPermaLink="true"><?= $link ?></guid>
    </item>
<?php endforeach; ?>

  </channel>
</rss>
