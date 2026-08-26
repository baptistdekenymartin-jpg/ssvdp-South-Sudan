<?php
$pageTitle = 'Gallery';
$pageDescription = 'Explore moments from SSVP activities, programmes, community engagement, training and events across South Sudan.';
require_once __DIR__ . '/includes/header.php';

$galleryCategories = array(
    array('key' => 'all', 'label' => 'All'),
    array('key' => 'baby-feeding', 'label' => 'Baby Feeding'),
    array('key' => 'be-in-hope-home', 'label' => 'BIH'),
    array('key' => 'idps-refugees', 'label' => 'IDPs & Refugees'),
    array('key' => 'income-generating-projects', 'label' => 'IGP'),
    array('key' => 'kitchen-gardening', 'label' => 'Kitchen Gardening'),
    array('key' => 'nyarjwa-clinic', 'label' => 'Nyarjwa Clinic'),
    array('key' => 'primary-nursery-school', 'label' => 'School'),
    array('key' => 'vocational-training', 'label' => 'Vocational Training')
);

$galleryItems = array(
    array('image' => 'assets/images/gallery/baby-feeding/baby-feeding-01.jpg', 'title' => 'Baby Feeding', 'category' => 'baby-feeding', 'category_label' => 'Baby Feeding', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/be-in-hope-home/bih-01.jpg', 'title' => 'Be in Hope Home (BIH)', 'category' => 'be-in-hope-home', 'category_label' => 'Be in Hope Home (BIH)', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/idps-refugees/idps-refugees-01.jpg', 'title' => 'IDPs & Refugees', 'category' => 'idps-refugees', 'category_label' => 'IDPs & Refugees', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/income-generating-projects/igp-01.jpg', 'title' => 'Income Generating Projects', 'category' => 'income-generating-projects', 'category_label' => 'Income Generating Projects', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/kitchen-gardening/kitchen-gardening-01.jpg', 'title' => 'Kitchen Gardening', 'category' => 'kitchen-gardening', 'category_label' => 'Kitchen Gardening', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/nyarjwa-clinic/nyarjwa-clinic-01.jpg', 'title' => 'Nyarjwa Clinic', 'category' => 'nyarjwa-clinic', 'category_label' => 'Nyarjwa Clinic', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/primary-nursery-school/school-01.jpg', 'title' => 'Primary & Nursery School', 'category' => 'primary-nursery-school', 'category_label' => 'Primary & Nursery School', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/vocational-training/vocational-training-01.jpg', 'title' => 'Vocational Training', 'category' => 'vocational-training', 'category_label' => 'Vocational Training', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/baby-feeding/baby-feeding-02.jpg', 'title' => 'Community Nutrition Support', 'category' => 'baby-feeding', 'category_label' => 'Baby Feeding', 'caption' => '', 'location' => '', 'date' => '')
);

$incomeGeneratingItems = array(
    array('image' => 'assets/images/gallery/income-generating-projects/farm/farm-01.jpg', 'title' => 'Farming Livelihood Support', 'category' => 'income-generating-projects', 'caption' => '', 'location' => 'Income Generating Projects / Farm', 'date' => ''),
    array('image' => 'assets/images/gallery/income-generating-projects/farm/farm-02.jpg', 'title' => 'Farm Production Activity', 'category' => 'income-generating-projects', 'caption' => '', 'location' => 'Income Generating Projects / Farm', 'date' => ''),
    array('image' => 'assets/images/gallery/income-generating-projects/farm/farm-03.jpg', 'title' => 'Community Farming Project', 'category' => 'income-generating-projects', 'caption' => '', 'location' => 'Income Generating Projects / Farm', 'date' => ''),
    array('image' => 'assets/images/gallery/income-generating-projects/jam-production/jam-01.jpg', 'title' => 'Jam Production', 'category' => 'income-generating-projects', 'caption' => '', 'location' => 'Income Generating Projects / Jam Production', 'date' => ''),
    array('image' => 'assets/images/gallery/income-generating-projects/jam-production/jam-02.jpg', 'title' => 'Food Production Training', 'category' => 'income-generating-projects', 'caption' => '', 'location' => 'Income Generating Projects / Jam Production', 'date' => ''),
    array('image' => 'assets/images/gallery/income-generating-projects/jam-production/jam-03.jpg', 'title' => 'Jam Processing Activity', 'category' => 'income-generating-projects', 'caption' => '', 'location' => 'Income Generating Projects / Jam Production', 'date' => ''),
    array('image' => 'assets/images/gallery/income-generating-projects/poultry/poultry-01.jpg', 'title' => 'Poultry Production', 'category' => 'income-generating-projects', 'caption' => '', 'location' => 'Income Generating Projects / Poultry', 'date' => ''),
    array('image' => 'assets/images/gallery/income-generating-projects/poultry/poultry-02.jpg', 'title' => 'Poultry Livelihood Activity', 'category' => 'income-generating-projects', 'caption' => '', 'location' => 'Income Generating Projects / Poultry', 'date' => ''),
    array('image' => 'assets/images/gallery/income-generating-projects/poultry/poultry-03.jpg', 'title' => 'Community Poultry Project', 'category' => 'income-generating-projects', 'caption' => '', 'location' => 'Income Generating Projects / Poultry', 'date' => '')
);
$vocationalTrainingItems = array(
    array('image' => 'assets/images/gallery/vocational-training/vt-01.jpg', 'title' => 'Vocational Training Photo 1', 'category' => 'vocational-training', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/vocational-training/vt-02.jpg', 'title' => 'Vocational Training Photo 2', 'category' => 'vocational-training', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/vocational-training/vt-03.jpg', 'title' => 'Vocational Training Photo 3', 'category' => 'vocational-training', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/vocational-training/vt-04.jpg', 'title' => 'Vocational Training Photo 4', 'category' => 'vocational-training', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/vocational-training/vt-05.jpg', 'title' => 'Vocational Training Photo 5', 'category' => 'vocational-training', 'caption' => '', 'location' => '', 'date' => ''),
    array('image' => 'assets/images/gallery/vocational-training/vt-06.jpg', 'title' => 'Vocational Training Photo 6', 'category' => 'vocational-training', 'caption' => '', 'location' => '', 'date' => '')
);
$moreMoments = array(
    array('image' => 'assets/images/gallery/vocational-training/vocational-training-02.jpg', 'title' => 'Vocational Training', 'category' => 'Vocational Training'),
    array('image' => 'assets/images/gallery/kitchen-gardening/kitchen-gardening-02.jpg', 'title' => 'Kitchen Gardening', 'category' => 'Kitchen Gardening'),
    array('image' => 'assets/images/gallery/primary-nursery-school/school-02.jpg', 'title' => 'Primary & Nursery School', 'category' => 'Primary & Nursery School'),
    array('image' => 'assets/images/gallery/nyarjwa-clinic/nyarjwa-clinic-02.jpg', 'title' => 'Nyarjwa Clinic', 'category' => 'Nyarjwa Clinic')
);

$dynamicGalleryItems = ssvdp_public_gallery_items();
if ($dynamicGalleryItems) {
    $galleryItems = array_merge($dynamicGalleryItems, $galleryItems);
}
?>

<section class="gallery-hero section-reveal" aria-labelledby="gallery-page-title">
    <div class="container gallery-hero-inner">
        <p class="section-label">GALLERY</p>
        <h1 id="gallery-page-title">Our Work in Pictures</h1>
        <p>Explore moments from SSVP activities, programmes, community engagement, training and events across South Sudan.</p>
    </div>
</section>

<section class="gallery-section section-reveal" aria-labelledby="latest-gallery-heading" data-gallery-page>
    <div class="container">
        <div class="section-heading gallery-heading">
            <p class="section-label">PHOTO GALLERY</p>
            <h2 id="latest-gallery-heading">Latest Photo Gallery</h2>
            <p>Browse selected moments from SSVP South Sudan programmes and activities.</p>
        </div>

        <div class="gallery-filters" role="group" aria-label="Filter gallery photos">
            <?php foreach ($galleryCategories as $index => $category) : ?>
                <button class="gallery-filter<?php echo $index === 0 ? ' is-active' : ''; ?>" type="button" data-gallery-filter="<?php echo e($category['key']); ?>" aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>"><?php echo e($category['label']); ?></button>
            <?php endforeach; ?>
        </div>

        <div class="gallery-panel-wrap">
            <div class="gallery-photo-grid" data-gallery-grid>
                <?php foreach ($galleryItems as $index => $item) : ?>
                    <button class="gallery-photo-item" type="button" data-gallery-item data-gallery-filter-item data-category="<?php echo e($item['category']); ?>" data-index="<?php echo e((string) $index); ?>" data-src="<?php echo site_url($item['image']); ?>" data-title="<?php echo e($item['title']); ?>" data-caption="<?php echo e($item['caption']); ?>" data-location="<?php echo e($item['location']); ?>" data-date="<?php echo e($item['date']); ?>">
                        <span class="gallery-photo-frame">
                            <img src="<?php echo site_url($item['image']); ?>" alt="<?php echo e($item['title']); ?>" loading="lazy" width="520" height="360" onerror="this.closest('.gallery-photo-frame').classList.add('is-missing'); this.remove();">
                            <span class="gallery-photo-placeholder">Photo coming soon</span>
                        </span>
                        <span class="gallery-photo-title"><?php echo e($item['title']); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="gallery-more-action">
            <a class="btn gallery-more-button" href="<?php echo site_url('gallery.php'); ?>#more-gallery-moments">View More Photos</a>
        </div>
    </div>
</section>

<section class="gallery-igp-section section-reveal" id="income-generating-projects-gallery" aria-labelledby="income-generating-projects-gallery-heading">
    <div class="container">
        <div class="section-heading gallery-heading">
            <p class="section-label">Income Generating Projects</p>
            <h2 id="income-generating-projects-gallery-heading">Income Generating Projects</h2>
            <p>Explore SSVP livelihood and income-generating activities supporting communities through farming, food production and poultry.</p>
        </div>
        <div class="gallery-igp-panel-wrap">
            <div class="gallery-igp-grid">
                <?php foreach ($incomeGeneratingItems as $index => $item) : ?>
                    <button class="gallery-photo-item gallery-igp-item" type="button" data-gallery-item data-category="<?php echo e($item['category']); ?>" data-src="<?php echo site_url($item['image']); ?>" data-title="<?php echo e($item['title']); ?>" data-caption="<?php echo e($item['caption']); ?>" data-location="<?php echo e($item['location']); ?>" data-date="<?php echo e($item['date']); ?>">
                        <span class="gallery-photo-frame">
                            <img src="<?php echo site_url($item['image']); ?>" alt="<?php echo e($item['title']); ?>" loading="lazy" width="520" height="325" onerror="this.closest('.gallery-photo-frame').classList.add('is-missing'); this.remove();">
                            <span class="gallery-photo-placeholder">Photo coming soon</span>
                        </span>
                        <span class="gallery-photo-title"><?php echo e($item['title']); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<section class="gallery-vocational-section section-reveal" id="vocational-training-gallery" aria-labelledby="vocational-training-gallery-heading">
    <div class="container">
        <div class="section-heading gallery-heading">
            <p class="section-label">Vocational Training</p>
            <h2 id="vocational-training-gallery-heading">Vocational Training</h2>
            <p>Highlights from SSVP vocational skills training activities and practical learning sessions.</p>
        </div>
        <div class="gallery-vocational-panel-wrap">
            <div class="gallery-vocational-grid">
                <?php foreach ($vocationalTrainingItems as $index => $item) : ?>
                    <button class="gallery-photo-item gallery-vocational-item" type="button" data-gallery-item data-category="<?php echo e($item['category']); ?>" data-src="<?php echo site_url($item['image']); ?>" data-title="<?php echo e($item['title']); ?>" data-caption="<?php echo e($item['caption']); ?>" data-location="<?php echo e($item['location']); ?>" data-date="<?php echo e($item['date']); ?>">
                        <span class="gallery-photo-frame">
                            <img src="<?php echo site_url($item['image']); ?>" alt="<?php echo e($item['title']); ?>" loading="lazy" width="520" height="325" onerror="this.closest('.gallery-photo-frame').classList.add('is-missing'); this.remove();">
                            <span class="gallery-photo-placeholder">Photo coming soon</span>
                        </span>
                        <span class="gallery-photo-title"><?php echo e($item['title']); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="gallery-moments-section section-reveal" id="more-gallery-moments" aria-labelledby="more-gallery-heading">
    <div class="container">
        <div class="section-heading gallery-heading">
            <p class="section-label">More Moments</p>
            <h2 id="more-gallery-heading">More Moments from Our Work</h2>
            <p>Additional albums, activities, older events and future video content can be organised here as the gallery grows.</p>
        </div>
        <div class="gallery-moments-strip">
            <?php foreach ($moreMoments as $item) : ?>
                <article class="gallery-moment-card">
                    <div class="gallery-photo-frame">
                        <img src="<?php echo site_url($item['image']); ?>" alt="<?php echo e($item['title']); ?>" loading="lazy" width="520" height="390" onerror="this.closest('.gallery-photo-frame').classList.add('is-missing'); this.remove();">
                        <span class="gallery-photo-placeholder">Photo coming soon</span>
                    </div>
                    <div>
                        <p><?php echo e($item['category']); ?></p>
                        <h3><?php echo e($item['title']); ?></h3>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="gallery-lightbox" data-gallery-lightbox aria-hidden="true">
    <div class="gallery-lightbox-backdrop" data-gallery-close></div>
    <div class="gallery-lightbox-dialog" role="dialog" aria-modal="true" aria-labelledby="gallery-lightbox-title">
        <button class="gallery-lightbox-close" type="button" data-gallery-close aria-label="Close gallery image"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
        <button class="gallery-lightbox-arrow gallery-lightbox-prev" type="button" data-gallery-prev aria-label="Previous photo"><i class="bi bi-chevron-left" aria-hidden="true"></i></button>
        <figure>
            <div class="gallery-lightbox-image-wrap">
                <img data-gallery-lightbox-image src="" alt="">
                <span class="gallery-lightbox-placeholder">Photo coming soon</span>
            </div>
            <figcaption>
                <h3 id="gallery-lightbox-title" data-gallery-lightbox-title></h3>
                <p data-gallery-lightbox-caption></p>
                <dl class="gallery-lightbox-meta">
                    <div data-gallery-location-wrap><dt>Location</dt><dd data-gallery-lightbox-location></dd></div>
                    <div data-gallery-date-wrap><dt>Date</dt><dd data-gallery-lightbox-date></dd></div>
                </dl>
            </figcaption>
        </figure>
        <button class="gallery-lightbox-arrow gallery-lightbox-next" type="button" data-gallery-next aria-label="Next photo"><i class="bi bi-chevron-right" aria-hidden="true"></i></button>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
