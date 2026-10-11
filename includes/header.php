<?php
/**
 * Shared Header Component
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/flash.php';
require_once __DIR__ . '/csrf.php';

$title = !empty($pageTitle) ? $pageTitle . ' – ' . APP_NAME : APP_NAME . ' – ' . APP_TAGLINE;
$description = !empty($metaDescription) ? $metaDescription : 'Access world-class medical treatments, luxury hotel stays, private transportation, and medical visa assistance worldwide.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?></title>
    <meta name="description" content="<?php echo e($description); ?>">

    <!-- Preconnect Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Local Vanilla CSS Design System -->
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
</head>
<body>

<?php require_once __DIR__ . '/navigation.php'; ?>

<?php if (!empty($heroTitle)): ?>
<section class="hero-section <?php echo e($heroClass ?? ''); ?>">
    <div class="container">
        <h1 class="hero-title"><?php echo e($heroTitle); ?></h1>
        <?php if (!empty($heroSubtitle)): ?>
            <p class="hero-subtitle"><?php echo e($heroSubtitle); ?></p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<main class="main-content" style="padding: 2rem 0;">
    <div class="container">
        <?php echo renderFlashMessages(); ?>
