<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
<?php foreach ($urls as $u): ?>
    <url>
        <loc><?= esc($u['loc']) ?></loc>
<?php if (!empty($u['alternates'])): ?>
<?php foreach ($u['alternates'] as $a): ?>
        <xhtml:link rel="alternate" hreflang="<?= esc($a['hreflang']) ?>" href="<?= esc($a['href']) ?>"/>
<?php endforeach; ?>
<?php endif; ?>
        <lastmod><?= esc($u['lastmod']) ?></lastmod>
        <changefreq><?= esc($u['changefreq']) ?></changefreq>
        <priority><?= esc($u['priority']) ?></priority>
    </url>
<?php endforeach; ?>
</urlset>