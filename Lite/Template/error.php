<!DOCTYPE html>
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>系统错误</title>
<style type="text/css">
* { padding: 0; margin: 0; }
body { background: #f1f1f1; font-family: '微软雅黑'; color: #333; font-size: 14px; }
.tip-box { width: 800px; margin: 150px auto 10px auto; padding: 30px 50px; background: #fff; border: 1 px solid #eaeaea; -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, .13); box-shadow: 0 1px 3px rgba(0, 0, 0, .13); -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, .13); }
a { color: #000; text-decoration: none; }
h1 { font-size: 32px; line-height: 48px; margin-bottom: 5px; }
.content { padding-top: 10px; }
.content .info { margin-bottom: 12px; }
.content .info .title { margin-bottom: 3px; }
.content .info .title h3 { color: #000; font-weight: 700; font-size: 16px; }
.content .info .text { line-height: 24px; }
.copyright { padding-top: 12px; color: #999; text-align: center; width: 100%; }
</style>
</head>
<body>
	<div class="tip-box">
		<h1><?php echo strip_tags($e['message']);?></h1>
		<div class="content">
			<?php if(isset($e['tips'])) echo '<p>'.strip_tags($e['tips']).'</p>';?>
			<?php if(isset($e['file'])) : ?>
			<div class="info">
				<div class="title">
					<h3>错误位置</h3>
				</div>
				<div class="text">
					<p>FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?></p>
				</div>
			</div>
			<?php endif; ?>
			<?php if(isset($e['trace'])): ?>
			<div class="info">
				<div class="title">
					<h3>TRACE</h3>
				</div>
				<div class="text">
					<p><?php echo nl2br($e['trace']);?></p>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="copyright">
		<p>
			<a title="官方网站" href="http://lite.zhuwei.cc" target="_blank">Lite</a> · <?php echo LITE_VERSION ?> · 极简而高效的 PHP 开发框架</p>
	</div>
</body>
</html>
