<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV <?php echo e($profil['nom']); ?></title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.5; margin: 0; padding: 20px; }
        h1 { color: #1e293b; margin-bottom: 5px; font-size: 28px; text-transform: uppercase; }
        h2 { color: #4c1d95; font-size: 18px; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-top: 30px; margin-bottom: 15px; text-transform: uppercase; }
        p { margin: 0 0 10px 0; }
        .header { text-align: center; margin-bottom: 30px; }
        .contact-info { color: #64748b; font-size: 12px; margin-bottom: 20px; }
        .section-item { margin-bottom: 15px; }
        .item-title { font-weight: bold; color: #1e293b; font-size: 15px; }
        .item-subtitle { color: #64748b; font-size: 13px; font-style: italic; }
        .item-date { font-weight: bold; color: #4c1d95; font-size: 13px; }
        .badges-container { margin-top: 10px; }
        .badge { display: inline-block; background-color: #f1f5f9; color: #334155; padding: 3px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px; margin-bottom: 5px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

    <div class="header">
        <h1><?php echo e($profil['nom']); ?></h1>
        <p style="font-size: 18px; color: #64748b; font-weight: bold;"><?php echo e($profil['titre']); ?></p>
        <div class="contact-info">
            <?php echo e($profil['email']); ?> | <?php echo e($profil['telephone']); ?>

        </div>
    </div>

    <!-- DIPLÔMES & CERTIFICATIONS -->
    <h2>Diplômes & Certifications</h2>
    
    <?php $__currentLoopData = $diplomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $diplome): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="section-item">
        <div class="item-title">
            <span class="item-date">[<?php echo e($diplome->year); ?>]</span> | <?php echo e($diplome->title); ?>

        </div>
        <div class="item-subtitle"><?php echo e($diplome->institution); ?></div>
        <?php if($diplome->description): ?>
            <p style="font-size: 13px; margin-top: 3px;"><?php echo e($diplome->description); ?></p>
        <?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php $__currentLoopData = $certifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="section-item">
        <div class="item-title">
            <span class="item-date">[<?php echo e(\Carbon\Carbon::parse($certif->issued_at)->format('Y')); ?>]</span> | <?php echo e($certif->title); ?>

        </div>
        <div class="item-subtitle"><?php echo e($certif->organization); ?></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <!-- EXPÉRIENCES PROFESSIONNELLES -->
    <h2>Parcours Professionnel</h2>
    <?php $__currentLoopData = $parcours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="section-item">
        <div class="item-title"><?php echo e($exp->title); ?> - <?php echo e($exp->organization); ?></div>
        <div class="item-subtitle">
            <?php echo e(\Carbon\Carbon::parse($exp->date_start)->format('m/Y')); ?> 
            - 
            <?php echo e($exp->date_end ? \Carbon\Carbon::parse($exp->date_end)->format('m/Y') : 'Présent'); ?>

        </div>
        <p style="font-size: 13px; margin-top: 5px;"><?php echo e($exp->description); ?></p>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <!-- COMPÉTENCES -->
    <h2>Compétences Techniques</h2>
    <div class="badges-container">
        <?php $__currentLoopData = $competences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $competence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="badge"><?php echo e($competence->name); ?> (<?php echo e($competence->proficiency); ?>%)</span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

</body>
</html>
<?php /**PATH /home/yahya-haroun/Bureau/.gemini/antigravity/scratch/yahya-portfolio/resources/views/pdf/cv.blade.php ENDPATH**/ ?>