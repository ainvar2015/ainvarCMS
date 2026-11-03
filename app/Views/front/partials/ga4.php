<?php
// GA4 — inerte hasta que el visitante consienta la categoría "analytics".
$GA4_ID = 'G-T8ZD86K5KQ'; // <-- pon aquí tu Measurement ID
?>
<!-- Google tag (gtag.js) -->
<script type="text/plain" data-cookieconsent="analytics" async src="https://www.googletagmanager.com/gtag/js?id=<?= $GA4_ID ?>"></script>
<script type="text/plain" data-cookieconsent="analytics">
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?= $GA4_ID ?>');
</script>
<!-- Google Ads (remarketing/conversión) → marketing -->
<script type="text/plain" data-cookieconsent="marketing"
  src="https://www.googletagmanager.com/gtag/js?id=AW-18552336364"></script>