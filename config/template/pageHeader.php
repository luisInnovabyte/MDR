<?php
/**
 * Componente: Page Header
 * Descripción: Header de página con breadcrumb, título, subtítulo y botones de acción
 * Uso: include_once('../../config/template/pageHeader.php');
 * 
 * Variables requeridas:
 * @param string $pageIcon - Icono Font Awesome (ej: 'fa-ruler-combined')
 * @param string $pageTitle - Título principal de la página
 * @param string $pageSubtitle - Subtítulo descriptivo
 * @param array $breadcrumbs - Array de breadcrumbs [['url' => '#', 'icon' => 'fa-home', 'text' => 'Dashboard'], ...]
 * @param array $actionButtons - Array de botones (opcional) [['url' => '#', 'text' => 'Nuevo', 'icon' => 'fa-plus', 'class' => 'btn-primary'], ...]
 * @param array $helpModal - Modal de ayuda (opcional) ['target' => '#modalAyuda', 'title' => 'Ayuda']
 */

// Valores por defecto
$pageIcon = $pageIcon ?? 'fa-file';
$pageTitle = $pageTitle ?? 'Título de página';
$pageSubtitle = $pageSubtitle ?? '';
$breadcrumbs = $breadcrumbs ?? [];
$actionButtons = $actionButtons ?? [];
$helpModal = $helpModal ?? null;
?>

<!-- Page Header Component -->
<div class="br-pageheader">
    <nav class="breadcrumb pd-0 mg-0 tx-12">
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
            <?php if (isset($crumb['active']) && $crumb['active']): ?>
                <span class="breadcrumb-item active">
                    <?php if (!empty($crumb['icon'])): ?>
                        <i class="fa <?php echo $crumb['icon']; ?>"></i>
                    <?php endif; ?>
                    <?php echo $crumb['title']; ?>
                </span>
            <?php else: ?>
                <a class="breadcrumb-item" href="<?php echo $crumb['url']; ?>">
                    <?php if (!empty($crumb['icon'])): ?>
                        <i class="fa <?php echo $crumb['icon']; ?>"></i>
                    <?php endif; ?>
                    <?php echo $crumb['title']; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
</div><!-- br-pageheader -->

<div class="br-pagetitle">
    <div>
        <h4 class="tx-gray-800">
            <i class="fa <?php echo $pageIcon; ?> tx-primary mg-r-5"></i>
            <?php echo $pageTitle; ?>
        </h4>
        <?php if (!empty($pageSubtitle)): ?>
            <p class="mg-b-0 tx-gray-600"><?php echo $pageSubtitle; ?></p>
        <?php endif; ?>
    </div>
    <div class="mg-l-auto d-flex align-items-center">
        <?php if ($helpModal): ?>
            <button type="button" class="btn btn-outline-info btn-icon rounded-circle mg-r-10" 
                    data-bs-toggle="modal" data-bs-target="<?php echo $helpModal['target']; ?>" 
                    title="<?php echo $helpModal['title'] ?? 'Ayuda sobre este módulo'; ?>"
                    style="width: 40px; height: 40px;">
                <i class="fa fa-question tx-18"></i>
            </button>
        <?php endif; ?>
        
        <?php foreach ($actionButtons as $button): ?>
            <?php if (isset($button['id']) || isset($button['onclick'])): ?>
                <button type="button"
                   <?php if (isset($button['id'])): ?>id="<?php echo $button['id']; ?>"<?php endif; ?>
                   <?php if (isset($button['onclick'])): ?>onclick="<?php echo $button['onclick']; ?>"<?php endif; ?>
                   class="btn <?php echo $button['class'] ?? 'btn-primary'; ?> btn-oblong tx-11 tx-uppercase tx-mont tx-medium <?php echo $button['marginClass'] ?? 'mg-l-5'; ?>">
                    <?php if (!empty($button['icon'])): ?>
                        <i class="fa <?php echo $button['icon']; ?> mg-r-10"></i>
                    <?php endif; ?>
                    <?php echo $button['text']; ?>
                </button>
            <?php else: ?>
                <a href="<?php echo $button['url']; ?>" 
                   class="btn <?php echo $button['class'] ?? 'btn-primary'; ?> btn-oblong tx-11 tx-uppercase tx-mont tx-medium <?php echo $button['marginClass'] ?? 'mg-l-5'; ?>">
                    <?php if (!empty($button['icon'])): ?>
                        <i class="fa <?php echo $button['icon']; ?> mg-r-10"></i>
                    <?php endif; ?>
                    <?php echo $button['text']; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div><!-- br-pagetitle -->
