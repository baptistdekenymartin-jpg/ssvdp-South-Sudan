<nav class="main-nav main-navigation" id="primary-navigation" aria-label="Primary">
    <ul>
        <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'home' ? 'active' : ''; ?>" href="<?php echo site_url('index.php'); ?>" <?php echo $currentPage === 'home' ? 'aria-current="page"' : ''; ?>>Home</a></li>
        <li class="nav-item has-dropdown">
            <a class="nav-link dropdown-toggle <?php echo $currentPage === 'about' ? 'active' : ''; ?>" href="<?php echo site_url('about.php'); ?>" aria-expanded="false" <?php echo $currentPage === 'about' ? 'aria-current="page"' : ''; ?>>About SSVDP <i class="bi bi-chevron-down" aria-hidden="true"></i></a>
            <ul class="dropdown-menu">
                <li><a href="<?php echo site_url('about.php#about-ssvdp'); ?>">About SSVDP</a></li>
                <li><a href="<?php echo site_url('about.php#how-we-work'); ?>">How We Work</a></li>
                <li><a href="<?php echo site_url('about.php#mission'); ?>">Mission and Vision</a></li>
                <li><a href="<?php echo site_url('about.php#values'); ?>">Our Values</a></li>
                <li><a href="<?php echo site_url('about.php#history'); ?>">Our History</a></li>
            </ul>
        </li>
        <li class="nav-item has-dropdown">
            <a class="nav-link dropdown-toggle <?php echo in_array($currentPage, array('programmes', 'projects', 'areas-of-operation'), true) ? 'active' : ''; ?>" href="<?php echo site_url('programmes.php'); ?>" aria-expanded="false" <?php echo in_array($currentPage, array('programmes', 'projects', 'areas-of-operation'), true) ? 'aria-current="page"' : ''; ?>>Our Work <i class="bi bi-chevron-down" aria-hidden="true"></i></a>
            <ul class="dropdown-menu">
                <li><a href="<?php echo site_url('programmes.php#programmes'); ?>">Programmes</a></li>
                <li><a href="<?php echo site_url('programmes.php#projects'); ?>">Projects</a></li>
                <li><a href="<?php echo site_url('areas-of-operation.php'); ?>">Areas of Operation</a></li>
            </ul>
        </li>
        <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'news' ? 'active' : ''; ?>" href="<?php echo site_url('news.php'); ?>" <?php echo $currentPage === 'news' ? 'aria-current="page"' : ''; ?>>News & Updates</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'gallery' ? 'active' : ''; ?>" href="<?php echo site_url('gallery.php'); ?>" <?php echo $currentPage === 'gallery' ? 'aria-current="page"' : ''; ?>>Gallery</a></li>
        <li class="nav-item has-dropdown">
            <a class="nav-link dropdown-toggle <?php echo $currentPage === 'resources' ? 'active' : ''; ?>" href="<?php echo site_url('resources.php'); ?>" aria-expanded="false" <?php echo $currentPage === 'resources' ? 'aria-current="page"' : ''; ?>>Get Involved <i class="bi bi-chevron-down" aria-hidden="true"></i></a>
            <ul class="dropdown-menu">
                <li><a href="<?php echo site_url('resources.php#volunteer'); ?>">Volunteer With Us</a></li>
                <li><a href="<?php echo site_url('resources.php#partner'); ?>">Partner With Us</a></li>
                <li><a href="<?php echo site_url('resources.php#support'); ?>">Support Our Mission</a></li>
            </ul>
        </li>
        <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>" href="<?php echo site_url('contact.php'); ?>" <?php echo $currentPage === 'contact' ? 'aria-current="page"' : ''; ?>>Contact Us</a></li>
    </ul>
</nav>



