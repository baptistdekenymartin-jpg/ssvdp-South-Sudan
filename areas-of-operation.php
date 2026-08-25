<?php
$pageTitle = 'Areas of Operation';
$pageDescription = 'SSVDP South Sudan areas of operation.';
$assetVersion = 'areas-process-four-v1';
require_once __DIR__ . '/includes/header.php';

$workPage = $siteConfig['ourWorkPage'];
?>

<div class="work-page route-page areas-operation-page">
    <section class="work-areas section-reveal">
        <div class="work-areas-copy">
            <p class="work-label">AREAS OF OPERATION</p>
            <h1><?php echo e($workPage['areas_preview']['heading']); ?></h1>
            <p><?php echo e($workPage['areas_preview']['text']); ?></p>
        </div>
        <div class="work-location-chips" aria-label="SSVDP areas of operation">
            <?php foreach ($workPage['areas_preview']['locations'] as $location) : ?>
                <span><i class="bi bi-geo-alt" aria-hidden="true"></i><?php echo e($location); ?></span>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="operations-process">
        <div class="operations-process__intro">
            <p class="operations-process__label">HOW OUR PRESENCE WORKS</p>
            <h2>From Local Presence to Lasting Impact</h2>
            <p>Every community served by SSVDP South Sudan follows a journey of listening, planning, action and continued support. Our local presence allows us to respond quickly to community needs while building long-term relationships that restore dignity and strengthen resilience.</p>
        </div>

        <div class="operations-process__steps operations-process__steps--four">
            <article class="operations-process__step">
                <span class="operations-process__marker"><i class="bi bi-ear" aria-hidden="true"></i></span>
                <p class="operations-process__number">01</p>
                <h3>Listen to Communities</h3>
                <p>Our conferences, institutions and local volunteers engage with communities to understand their priorities and identify those most in need.</p>
            </article>
            <article class="operations-process__step">
                <span class="operations-process__marker"><i class="bi bi-clipboard-check" aria-hidden="true"></i></span>
                <p class="operations-process__number">02</p>
                <h3>Plan Together</h3>
                <p>Needs are assessed and appropriate programmes are organised in collaboration with local leaders, partners and community members.</p>
            </article>
            <article class="operations-process__step">
                <span class="operations-process__marker"><i class="bi bi-box2-heart" aria-hidden="true"></i></span>
                <p class="operations-process__number">03</p>
                <h3>Deliver Support</h3>
                <p>Through healthcare, vocational training, education, agriculture, humanitarian assistance and other initiatives, practical support reaches vulnerable families.</p>
            </article>
            <article class="operations-process__step">
                <span class="operations-process__marker"><i class="bi bi-people" aria-hidden="true"></i></span>
                <p class="operations-process__number">04</p>
                <h3>Follow Up</h3>
                <p>SSVDP continues engaging with communities, monitoring progress and strengthening long-term development through ongoing accompaniment.</p>
            </article>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


