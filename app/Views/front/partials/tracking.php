<?php
// Tags de Google — inertes hasta consentimiento (analytics / marketing).
$GA4_ID = 'G-T8ZD86K5KQ';
$AW_ID  = 'AW-18552336364';
?>
<!-- Google Analytics 4 -->
<script type="text/plain" data-cookieconsent="analytics" async
        src="https://www.googletagmanager.com/gtag/js?id=<?= $GA4_ID ?>"></script>
<script type="text/plain" data-cookieconsent="analytics">
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?= $GA4_ID ?>');
</script>

<!-- Google Ads (remarketing/conversión) -->
<script type="text/plain" data-cookieconsent="marketing">
  // gtag() puede no existir si analytics no está consentido: stub de seguridad
  window.dataLayer = window.dataLayer || [];
  window.gtag = window.gtag || function(){dataLayer.push(arguments);};

  // Carga la librería solo si analytics no la ha cargado ya
  if (!document.querySelector('script[src*="googletagmanager.com/gtag"]')) {
      var s = document.createElement('script');
      s.async = true;
      s.src = 'https://www.googletagmanager.com/gtag/js?id=<?= $AW_ID ?>';
      document.head.appendChild(s);
  }
  gtag('js', new Date());
  gtag('config', '<?= $AW_ID ?>');
</script>