<?php

declare(strict_types=1);
?>
    </main>
    <footer class="site-footer">
        <div class="footer-main">
            <div class="container footer-grid">
                <div class="footer-brand">
                    <img src="<?= e(asset_url('img/logo.png')) ?>" alt="Logo WIZNET">
                </div>
                <div>
                    <h3>Nuestros Servicios</h3>
                    <span class="footer-line"></span>
                    <ul class="footer-links">
                        <?php foreach ($site['footer_links']['services'] as $link): ?>
                            <?php
                            $url = (string) $link['url'];
                            $href = preg_match('/^https?:\/\//i', $url) ? $url : page_url($url);
                            $externalAttributes = preg_match('/^https?:\/\//i', $url) ? ' target="_blank" rel="noopener"' : '';
                            ?>
                            <li>
                                <?= render_icon('globe', 'icon--tiny') ?>
                                <a href="<?= e($href) ?>"<?= $externalAttributes ?>><?= e($link['label']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h3>Links de Interés</h3>
                    <span class="footer-line"></span>
                    <ul class="footer-links footer-links--double">
                        <?php foreach ($site['footer_links']['interest'] as $link): ?>
                            <?php
                            $url = (string) $link['url'];
                            $href = preg_match('/^https?:\/\//i', $url) ? $url : page_url($url);
                            $externalAttributes = preg_match('/^https?:\/\//i', $url) ? ' target="_blank" rel="noopener"' : '';
                            ?>
                            <li>
                                <?= render_icon('globe', 'icon--tiny') ?>
                                <a href="<?= e($href) ?>"<?= $externalAttributes ?>><?= e($link['label']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container footer-bottom__content">
                <p>&copy; Copyright <?= e(date('Y')) ?> WIZNET - Todos los derechos reservados.</p>
                <p>Diseñado por Diseño Web Jalisco</p>
            </div>
        </div>
    </footer>
    <script src="<?= e(asset_url('js/main.js')) ?>"></script>
</body>
</html>
