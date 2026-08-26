<?php
declare(strict_types=1);

if (!defined('SSVDP_SITE_CONFIG')) {
    define('SSVDP_SITE_CONFIG', true);
}

$hero = array(
    'label' => 'SERVIENS IN SPE',
    'heading' => "Empowering Youth,\nTransforming\nCommunities",
    'text' => 'Through practical skills, sustainable livelihoods and compassionate community support.',
    'supporting_line' => 'Catholic Service | Youth Empowerment | Human Dignity',
    'value_items' => array(
        array('label' => 'Catholic Service', 'icon' => 'bi-cross'),
        array('label' => 'Youth Empowerment', 'icon' => 'bi-mortarboard'),
        array('label' => 'Human Dignity', 'icon' => 'bi-person-heart')
    ),
    'buttons' => array(
        array('label' => 'Learn More', 'link' => 'about.php', 'class' => 'btn-primary'),
        array('label' => 'Our Programmes', 'link' => 'programmes.php', 'class' => 'btn-outline-light')
    )
);

$heroFeatures = array(
    array(
        'title' => 'Empowering Youth',
        'description' => 'Equipping young people with skills for a brighter future.',
        'icon' => 'bi-mortarboard'
    ),
    array(
        'title' => 'Sustainable Livelihoods',
        'description' => 'Creating opportunities for long-term self-reliance.',
        'icon' => 'bi-graph-up-arrow'
    ),
    array(
        'title' => 'Compassionate Support',
        'description' => 'Standing with communities in times of need.',
        'icon' => 'bi-heart-pulse'
    ),
    array(
        'title' => 'Faith in Action',
        'description' => 'Guided by our Catholic faith to serve with love.',
        'icon' => 'bi-cross'
    )
);

$values = array(
    array(
        'title' => 'Compassion',
        'description' => 'We serve people with care, understanding and respect.',
        'icon' => 'bi-heart'
    ),
    array(
        'title' => 'Service',
        'description' => 'We respond through practical action and person-to-person support.',
        'icon' => 'bi-people'
    ),
    array(
        'title' => 'Integrity',
        'description' => 'We work responsibly, honestly and transparently.',
        'icon' => 'bi-shield-check'
    ),
    array(
        'title' => 'Human Dignity',
        'description' => 'We recognise the worth and potential of every person.',
        'icon' => 'bi-person-heart'
    )
);

$about = array(
    'label' => 'ABOUT SSVP SOUTH SUDAN',
    'heading' => 'Building Skills, Restoring Hope and Strengthening Communities',
    'paragraphs' => array(
        'Welcome to the Society of St. Vincent de Paul South Sudan. We believe in inclusive youth empowerment through practical training, leadership development and mentorship. We also provide compassionate care and emergency support to vulnerable children, mothers and families.',
        'Our programmes equip young people with the skills, tools and opportunities they need to thrive, lead and succeed. Through service, partnership and community participation, we work to bridge the gap between potential and opportunity and contribute to stronger and more self-reliant communities.'
    ),
    'button_label' => 'Discover Our Story',
    'button_link' => 'about.php',
    'image_path' => '',
    'panel_statement' => 'Building skills, restoring hope and strengthening communities.'
);

$programmes = array(
    array(
        'title' => 'Vocational Training',
        'description' => 'Providing practical and employment-oriented skills that help young people improve their livelihoods and become self-reliant.',
        'icon' => 'bi-tools',
        'link' => 'programmes.php'
    ),
    array(
        'title' => 'Education and Youth Empowerment',
        'description' => 'Supporting inclusive education, youth leadership, mentorship and opportunities for personal and professional development.',
        'icon' => 'bi-mortarboard',
        'link' => 'programmes.php'
    ),
    array(
        'title' => 'Health and Community Wellbeing',
        'description' => 'Promoting health awareness, hygiene, family wellbeing and access to essential community health support.',
        'icon' => 'bi-heart-pulse',
        'link' => 'programmes.php'
    ),
    array(
        'title' => 'Humanitarian Support',
        'description' => 'Providing humanitarian assistance and compassionate support to internally displaced persons, refugees and vulnerable families.',
        'icon' => 'bi-box2-heart',
        'link' => 'programmes.php'
    ),
    array(
        'title' => 'Child Nutrition',
        'description' => 'Supporting malnourished children, mothers and caregivers through nutrition activities and baby-feeding programmes.',
        'icon' => 'bi-egg-fried',
        'link' => 'programmes.php'
    ),
    array(
        'title' => 'Street Children Care',
        'description' => 'Providing care, protection, guidance and opportunities for vulnerable children living or working on the streets.',
        'icon' => 'bi-shield-heart',
        'link' => 'programmes.php'
    )
);

$impactStatistics = array(
    array('label' => 'Beneficiaries Reached', 'value' => '60,772', 'icon' => 'bi-people'),
    array('label' => 'Families Supported', 'value' => '5,424', 'icon' => 'bi-house-heart'),
    array('label' => 'Active Volunteers', 'value' => '10', 'icon' => 'bi-person-badge'),
    array('label' => 'Parishes or Conferences', 'value' => '54', 'icon' => 'bi-building'),
    array('label' => 'Communities Reached', 'value' => '4', 'icon' => 'bi-geo-alt')
);

$featuredActivity = array(
    'label' => 'FEATURED ACTIVITY',
    'title' => "Women's Training in Nutrition, Hygiene and Family Planning",
    'date' => '13 July 2026',
    'location' => 'Nyarjwa Village',
    'participants' => '30 women',
    'category' => "Health, Nutrition and Women's Empowerment",
    'excerpt' => 'Thirty women participated in training on nutrition, hygiene and family planning in Nyarjwa Village on 13 July 2026. The activity was conducted as part of an empowerment programme for mothers and caregivers of children enrolled in the baby-feeding programme.',
    'guests' => 'SSVP senior staff and the President of SSVP South Sudan.',
    'placeholder' => 'Activity photograph will be added after approval.',
    'button_label' => 'Read Full Activity Report',
    'button_link' => 'news.php'
);

$whereWeWork = array(
    'heading' => 'Where We Work',
    'state' => 'Central Equatoria State',
    'archdiocese' => 'Archdiocese of Juba',
    'county' => 'Juba County',
    'communities_reached' => '4',
    'office_location' => 'Luluggo 2, South Juba Town, Juba, South Sudan',
    'paragraph' => 'SSVP South Sudan currently serves communities within Central Equatoria State through the Archdiocese of Juba, supporting vulnerable people through skills development, education, health, nutrition and humanitarian programmes.',
    'button_label' => 'View Areas of Operation',
    'button_link' => 'programmes.php#areas',
    'details' => array(
        array('label' => 'State', 'value' => 'Central Equatoria State', 'icon' => 'bi-map'),
        array('label' => 'Archdiocese', 'value' => 'Archdiocese of Juba', 'icon' => 'bi-building'),
        array('label' => 'County', 'value' => 'Juba County', 'icon' => 'bi-signpost'),
        array('label' => 'Communities reached', 'value' => '4', 'icon' => 'bi-geo-alt'),
        array('label' => 'Office location', 'value' => 'Luluggo 2, South Juba Town, Juba, South Sudan', 'icon' => 'bi-pin-map')
    )
);

$latestNews = array(
    array(
        'title' => "Women's Training in Nutrition, Hygiene and Family Planning",
        'excerpt' => 'Thirty women participated in training on nutrition, hygiene and family planning in Nyarjwa Village.',
        'date' => '13 July 2026',
        'category' => "Health, Nutrition and Women's Empowerment",
        'placeholder' => 'Activity photograph will be added after approval.',
        'image' => 'assets/images/work/wt2.jpg',
        'link' => 'news.php'
    ),
    array(
        'title' => 'Community Programme Update',
        'excerpt' => 'Approved community programme updates will be published here when confirmed content is available.',
        'date' => 'To be updated',
        'category' => 'Programme Update',
        'placeholder' => 'Approved photograph will be added later.',
        'image' => 'assets/images/work/Picture77.jpg',
        'link' => 'news.php'
    ),
    array(
        'title' => 'Youth Skills Development Update',
        'excerpt' => 'Future youth skills development updates will be shared after official review and approval.',
        'date' => 'To be updated',
        'category' => 'Youth Empowerment',
        'placeholder' => 'Approved photograph will be added later.',
        'image' => 'assets/images/work/Picture25.jpg',
        'link' => 'news.php'
    )
);

$ourWorkPage = array(
    'hero' => array(
        'label' => 'OUR WORK',
        'heading' => 'Transforming Lives Through Compassionate Service',
        'text' => 'Across South Sudan, SSVP delivers humanitarian assistance, healthcare, vocational training, child protection, education and sustainable livelihood programmes that restore dignity and create hope.',
        'image' => 'assets/images/work/our work.jpg',
        'buttons' => array(
            array('label' => 'Explore Programmes', 'link' => 'programmes.php', 'class' => 'btn-primary', 'icon' => 'bi-arrow-right'),
            array('label' => 'View Projects', 'link' => 'projects.php', 'class' => 'btn-outline-light', 'icon' => 'bi-folder2-open')
        )
    ),
    'intro' => array(
        'heading' => 'How We Serve',
        'text' => 'At SSVP South Sudan, we accompany vulnerable individuals, families and communities through practical acts of charity, compassion and sustainable development. Our work responds to immediate humanitarian needs while creating long-term opportunities for self-reliance, dignity and hope. Through healthcare, education, vocational skills training, emergency relief, food security, agricultural initiatives, child and family support, and community development programmes, we strive to improve lives and strengthen communities across the areas where we serve.'
    ),
    'programme_areas' => array(
        'heading' => 'Our Programme Areas',
        'text' => 'Our work combines humanitarian response, social care, education, health and livelihood development to help vulnerable communities build safer and more self-reliant lives.',
        'items' => array(
            array('title' => 'Vocational Training', 'description' => 'Practical skills development for young people and women through tailoring, building and construction, welding, general electricity, automobile mechanics, computer literacy and other trades.', 'icon' => 'bi-tools', 'link' => 'programme.php?programme=vocational-training'),
            array('title' => 'Education', 'description' => 'Basic and primary education, school support and learning opportunities for vulnerable children.', 'icon' => 'bi-book', 'link' => 'programme.php?programme=education'),
            array('title' => 'Healthcare Services', 'description' => 'Primary healthcare, maternal services, medical support and community health services for people who cannot afford essential care.', 'icon' => 'bi-heart-pulse', 'link' => 'programme.php?programme=healthcare'),
            array('title' => 'Child Care and Protection', 'description' => 'Support for street-connected children, child welfare, safe care, education and long-term development through programmes such as Be in Hope Home.', 'icon' => 'bi-shield-heart', 'link' => 'programme.php?programme=child-care'),
            array('title' => 'Food Security and Nutrition', 'description' => 'Supplementary feeding, nutrition support, school snacks and food assistance for malnourished children and vulnerable families.', 'icon' => 'bi-basket', 'link' => 'programme.php?programme=food-nutrition'),
            array('title' => 'Agriculture and Livelihoods', 'description' => 'Agricultural training, women\'s agribusiness, sustainable farming and livelihood support for IDPs and vulnerable communities.', 'icon' => 'bi-tree', 'link' => 'programme.php?programme=agriculture-livelihoods'),
            array('title' => 'Humanitarian Assistance', 'description' => 'Emergency relief, food distributions, plastic-sheet support, cash assistance and services for internally displaced people and refugees.', 'icon' => 'bi-box2-heart', 'link' => 'programme.php?programme=humanitarian-assistance'),
            array('title' => 'Social Enterprise and Community Development', 'description' => 'Income-generating initiatives such as the Lologo jam-production activity, women\'s empowerment, community capacity building and self-reliance programmes.', 'icon' => 'bi-graph-up-arrow', 'link' => 'programme.php?programme=community-development')
        )
    ),
    'featured_projects' => array(
        'heading' => 'Featured Projects',
        'text' => 'Explore some of SSVP South SudanÃ¢â‚¬â„¢s key projects supporting vulnerable communities through skills development, healthcare, agriculture, humanitarian assistance and sustainable livelihoods.',
        'items' => array(
            array('title' => 'IDP Agricultural Training Project', 'location' => 'Kworijik, Luri', 'description' => 'Agricultural training designed to strengthen food security and promote self-reliance among internally displaced communities.', 'image' => 'assets/images/work/Agricultural.jpeg', 'link' => 'projects.php'),
            array('title' => 'Vocational Training Centre', 'location' => '', 'description' => 'Hands-on skills training in tailoring, building and construction, welding, general electricity, automobile mechanics, computer literacy and other practical trades.', 'image' => 'assets/images/work/vocational.jpeg', 'link' => 'projects.php'),
            array('title' => 'Nyarjwa Primary Health Care Centre', 'location' => 'Nyarjwa', 'description' => 'Primary healthcare and maternity services supporting vulnerable families in Nyarjwa and nearby communities.', 'image' => 'assets/images/work/nyarjwa.jpeg', 'link' => 'projects.php', 'class' => 'featured-project-card--nyarjwa'),
            array('title' => 'Emergency Relief for IDPs', 'location' => 'Kworijik, Juba', 'description' => 'Food assistance and essential shelter support for internally displaced households and other vulnerable families.', 'image' => 'assets/images/work/Emergency.jpeg', 'link' => 'projects.php'),
            array('title' => 'Jam Production Initiative', 'location' => 'Lologo II', 'description' => 'Local fruit-jam production supporting food processing, income generation and SSVP\'s charitable programmes.', 'image' => 'assets/images/work/jam.jpeg', 'link' => 'projects.php')
        )
    ),
    'areas_preview' => array(
        'heading' => 'Where We Work',
        'text' => 'SSVP South Sudan serves vulnerable communities through its conferences, institutions and programme locations across Juba and surrounding areas.',
        'locations' => array('Juba', 'Lologo', 'Nyarjwa', 'Rejaf', 'Kworijik', 'Luri'),
        'button_label' => 'View Areas of Operation',
        'button_link' => 'areas-of-operation.php'
    ),
    'impact_summary' => array(
        array('value' => '53', 'label' => 'Conferences', 'icon' => 'bi-diagram-3'),
        array('value' => '4', 'label' => 'Main Communities', 'icon' => 'bi-geo-alt'),
        array('value' => 'Multiple', 'label' => 'Programme Areas', 'icon' => 'bi-grid'),
        array('value' => 'Thousands', 'label' => 'of Lives Reached', 'icon' => 'bi-people')
    ),
    'updates' => $latestNews,
    'cta' => array(
        'heading' => 'Together, We Can Build Stronger Communities',
        'text' => 'Explore our programmes, volunteer your skills or partner with SSVP South Sudan to help vulnerable people build lives of dignity, opportunity and self-reliance.',
        'buttons' => array(
            array('label' => 'Explore Our Work', 'link' => 'programmes.php', 'class' => 'btn-primary', 'icon' => 'bi-arrow-right'),
            array('label' => 'Contact Us', 'link' => 'contact.php', 'class' => 'btn-outline-light', 'icon' => 'bi-envelope')
        )
    )
);
$getInvolved = array(
    'heading' => 'Join Us in Strengthening Communities',
    'text' => 'Work with SSVP South Sudan to expand opportunities, restore hope and support vulnerable people through practical community action.',
    'cards' => array(
        array(
            'title' => 'Become a Volunteer',
            'description' => 'Share your time, skills and experience in support of vulnerable communities.',
            'button_label' => 'Volunteer With Us',
            'link' => 'contact.php',
            'icon' => 'bi-person-plus'
        ),
        array(
            'title' => 'Partner With Us',
            'description' => 'Collaborate with SSVP South Sudan in education, livelihoods, health and humanitarian support.',
            'button_label' => 'Explore Partnerships',
            'link' => 'contact.php',
            'icon' => 'bi-diagram-3'
        ),
        array(
            'title' => 'Contact SSVP',
            'description' => 'Speak with our team to learn more about our programmes and community work.',
            'button_label' => 'Contact Our Team',
            'link' => 'contact.php',
            'icon' => 'bi-envelope'
        )
    )
);

$aboutPage = array(
    'banner' => array(
        'label' => 'ABOUT SSVP SOUTH SUDAN',
        'heading' => 'Faith in Action, Hope for Communities',
        'text' => 'We are a Catholic Church-affiliated organisation dedicated to serving vulnerable people through compassionate service, education and sustainable development.',
        'motto' => 'Development Through Community Empowerment & Participation',
        'watermark' => 'SSVP',
        'breadcrumb_label' => 'Breadcrumb',
        'breadcrumb' => array(
            array('label' => 'Home', 'link' => 'index.php'),
            array('label' => 'About SSVP', 'link' => '')
        )
    ),
    'introduction' => array(
        'label' => 'ABOUT THE ORGANISATION',
        'heading' => 'Serving Vulnerable Communities With Compassion',
        'paragraphs' => array(
            'The Society of Saint Vincent de Paul South Sudan is a Catholic Church-affiliated, charitable and nonprofit humanitarian and development organisation serving communities in South Sudan.',
            'Since 2009, the organisation has provided rehabilitation, humanitarian support, education, vocational training and capacity-building services to children, young people, women and vulnerable families.',
            'Its work particularly supports poor and disadvantaged communities through programmes that respond to immediate humanitarian needs while also creating opportunities for long-term development and self-reliance.'
        ),
        'visual_statement' => 'Development Through Community Empowerment & Participation',
        'visual_label' => 'Branded SSVP South Sudan community development graphic'
    ),
    'who_we_are' => array(
        'heading' => 'Who We Are',
        'paragraphs' => array(
            'SSVP South Sudan is a Catholic Church-based nonprofit organisation committed to supporting poor and disadvantaged people without discrimination based on sex, colour, religion, gender or social background.',
            'The organisation provides skills development for young people, formal education for children, health services, supplementary feeding for malnourished children and humanitarian assistance to vulnerable families and displaced communities.',
            'Our work is rooted in Christian faith and service. We seek to bear witness to Christ and His Church by demonstrating that faith inspires people to work for the good of humanity.'
        ),
        'highlights' => array(
            array('title' => 'Catholic Service', 'description' => 'Our work is guided by Christian faith, compassion and service to humanity.', 'icon' => 'bi-cross'),
            array('title' => 'Inclusive Support', 'description' => 'We serve vulnerable people without discrimination.', 'icon' => 'bi-people'),
            array('title' => 'Community Empowerment', 'description' => 'We help people develop skills, confidence and opportunities for self-reliance.', 'icon' => 'bi-person-workspace')
        )
    ),
    'organization' => array(
        'paragraphs' => array(
            'SSVP South Sudan operates as a registered local nonprofit organization implementing humanitarian and development initiatives in partnership with communities and supporting organizations.',
            'The organization is led by an Executive Director and supported by professional staff responsible for programme implementation, administration, finance and other operational functions.'
        ),
        'features' => array(
            array('title' => 'Professional Leadership', 'description' => 'Led by an Executive Director and supported by professional staff.', 'icon' => 'bi-person-badge'),
            array('title' => 'Project Implementation', 'description' => 'Implements humanitarian and development projects supported by partner organizations.', 'icon' => 'bi-briefcase'),
            array('title' => 'Community Focus', 'description' => 'Works with communities to strengthen resilience, dignity and participation.', 'icon' => 'bi-people')
        )
    ),    'mission' => array(
        'heading' => 'Our Mission',
        'paragraphs' => array(
            'SSVP South Sudan empowers individuals and communities through quality vocational training, inclusive education and sustainable livelihood opportunities, while providing compassionate care and emergency support to vulnerable children, mothers and families.',
            'Guided by compassion, service and integrity, we build skills, restore hope and strengthen local capacity for lasting peace and human dignity.'
        ),
        'icon' => 'bi-bullseye'
    ),
    'vision' => array(
        'heading' => 'Our Vision',
        'text' => 'A self-reliant, peaceful and compassionate South Sudan where every person, especially young and vulnerable people, can live with dignity, develop their potential and contribute to the common good.',
        'icon' => 'bi-binoculars'
    ),
    'values' => array(
        'label' => 'WHAT GUIDES US',
        'heading' => 'Our Values',
        'text' => 'Our values shape how we serve communities, manage our programmes and relate to the people who place their trust in us.',
        'items' => array(
            array('title' => 'Compassion', 'description' => 'We show love, care and understanding to the people we serve, especially vulnerable children and families.', 'icon' => 'bi-heart'),
            array('title' => 'Inclusivity', 'description' => 'We ensure that everyone is treated fairly and served without discrimination.', 'icon' => 'bi-people'),
            array('title' => 'Integrity', 'description' => 'We act with honesty, strong moral principles and responsibility.', 'icon' => 'bi-shield-check'),
            array('title' => 'Transparency', 'description' => 'We promote openness and accountability in our decisions, actions and services.', 'icon' => 'bi-file-earmark-text'),
            array('title' => 'Peace', 'description' => 'We encourage peaceful coexistence, understanding and cooperation among communities.', 'icon' => 'bi-peace'),
            array('title' => 'Sustainability', 'description' => 'We design our services to create lasting benefits for vulnerable communities.', 'icon' => 'bi-tree'),
            array('title' => 'Accountability', 'description' => 'We ensure just, responsible and transparent reporting of our services and resources.', 'icon' => 'bi-person-check'),
            array('title' => 'Dignity', 'description' => 'We treat every person with respect, equal rights and humanity.', 'icon' => 'bi-hand-thumbs-up'),
            array('title' => 'Excellence', 'description' => 'We plan and deliver our programmes carefully to maintain high-quality services.', 'icon' => 'bi-award')
        )
    ),
    'history' => array(
        'label' => 'OUR HISTORY',
        'heading' => 'Our Vincentian Roots',
        'items' => array(
            array('year' => '1833', 'title' => 'Vincentian Tradition Begins', 'description' => 'The wider Society of Saint Vincent de Paul was founded by Frederic Ozanam and his companions, establishing a Vincentian tradition of lay Catholic service to people experiencing poverty.'),
            array('year' => '1998', 'title' => 'Presence in Southern Sudan', 'description' => 'The Society presence was established within the Diocese in Southern Sudan as part of the wider Vincentian structure.'),
            array('year' => 'Today', 'title' => 'Registered Local Nonprofit Organization', 'description' => 'SSVP South Sudan operates as a registered local nonprofit organization implementing humanitarian and development programmes through professional staff and project-based partnerships.'),
            array('year' => 'Ongoing', 'title' => 'Vincentian Service in Action', 'description' => 'The organization continues to draw inspiration from the Vincentian tradition of service, dignity, solidarity and support to vulnerable communities.')
        ),
        'summary' => array(
            'SSVP South Sudan implements humanitarian and development programmes through professional staff and project-based partnerships.',
            'Its work is rooted in community empowerment, dignity, solidarity and support to vulnerable communities.'
        )
    ),
    'cta' => array(
        'heading' => 'Together, We Can Build Stronger Communities',
        'text' => 'Explore our programmes, volunteer your skills or partner with SSVP South Sudan to help vulnerable people build lives of dignity, opportunity and self-reliance.',
        'buttons' => array(
            array('label' => 'Explore Our Work', 'link' => 'programmes.php', 'class' => 'btn-primary', 'icon' => 'bi-arrow-right'),
            array('label' => 'Contact Us', 'link' => 'contact.php', 'class' => 'btn-outline-light', 'icon' => 'bi-envelope')
        )
    )
);

$contactEngagement = array(
    'volunteer' => array(
        'heading' => 'Volunteer With Us',
        'text' => 'SSVP South Sudan welcomes qualified individuals who wish to contribute their knowledge, time and professional skills to vulnerable communities.',
        'list_heading' => 'Volunteer opportunities include:',
        'opportunities' => array('Vocational training instruction', 'Building and construction instruction', 'General electricity instruction', 'Automobile mechanics instruction', 'Welding and metal fabrication instruction', 'Computer literacy instruction', 'Air conditioning and refrigeration instruction', 'First aid and auxiliary nursing instruction', 'Social work at Be in Hope Home', 'Nutrition support within the supplementary feeding programme'),
        'button_label' => 'Contact Us About Volunteering',
        'button_link' => 'contact.php',
        'icon' => 'bi-person-plus'
    ),
    'partnerships' => array(
        'heading' => 'Partner With Us',
        'paragraphs' => array(
            'SSVP South Sudan welcomes partnerships with local and international organisations that share its commitment to humanitarian service, education, skills development and community empowerment.',
            'Partnerships are normally established through a Memorandum of Understanding covering a particular programme or service area.'
        ),
        'types' => array(
            array('title' => 'Donor Partnerships', 'description' => 'Partners provide financial or material support for the implementation and continuation of approved programmes.'),
            array('title' => 'Service-Delivery Partnerships', 'description' => 'Organisations collaborate with SSVP or refer participants to services such as vocational training, education, healthcare or community support.')
        ),
        'button_label' => 'Discuss a Partnership',
        'button_link' => 'contact.php',
        'icon' => 'bi-diagram-3'
    ),
);

$contactInformation = array(
    'telephone' => '0923330307',
    'telephone_link' => 'tel:0923330307',
    'whatsapp' => '+211 923 330 307',
    'whatsapp_url' => 'https://wa.me/211923330307',
    'emails' => array(
        'info.saintvincentdepaul@gmail.com',
        'simbakuol@yahoo.de'
    ),
    'office' => 'Luluggo 2, South Juba Town, Juba, South Sudan',
    'office_schedule' => '8 hours per day, 5 days per week',
    'facebook' => 'https://www.facebook.com/share/1AwAfW5Ejx/'
);

$siteConfig = array(
    'site_name' => 'Society of St. Vincent de Paul South Sudan',
    'site_tagline' => 'Serviens in Spe',
    'site_description' => 'SSVP South Sudan empowers young people and vulnerable communities through practical skills training, inclusive education, sustainable livelihood opportunities and compassionate humanitarian support.',
    'site_url' => '/ssvdp-south-sudan/',
    'default_page_title' => 'SSVP South Sudan',
    'logo' => 'assets/images/logo/ssvdp-logo-cutout.png',
    'hero' => $hero,
    'heroFeatures' => $heroFeatures,
    'values' => $values,
    'about' => $about,
    'aboutPage' => $aboutPage,
    'programmes' => $programmes,
    'ourWorkPage' => $ourWorkPage,
    'impactStatistics' => $impactStatistics,
    'featuredActivity' => $featuredActivity,
    'whereWeWork' => $whereWeWork,
    'latestNews' => $latestNews,
    'getInvolved' => $getInvolved,
    'contactInformation' => $contactInformation,
    'social_links' => array(
        'facebook' => $contactInformation['facebook']
    ),
    'contact' => array(
        'telephone' => $contactInformation['telephone'],
        'telephone_link' => $contactInformation['telephone_link'],
        'whatsapp' => $contactInformation['whatsapp'],
        'whatsapp_link' => $contactInformation['whatsapp_url'],
        'emails' => $contactInformation['emails'],
        'address' => $contactInformation['office'],
        'office_schedule' => $contactInformation['office_schedule'],
        'volunteer' => $contactEngagement['volunteer'],
        'partnerships' => $contactEngagement['partnerships']
    ),
    'impact' => $impactStatistics,
    'news' => $latestNews,
    'where_we_work' => array(
        'label' => 'Where We Work',
        'heading' => $whereWeWork['heading'],
        'details' => $whereWeWork['details']
    )
);

function site_url($path = '') {
    $base = rtrim($GLOBALS['siteConfig']['site_url'], '/');
    return $base . '/' . ltrim((string) $path, '/');
}

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return '';
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(string $token): bool {
    return session_status() === PHP_SESSION_ACTIVE
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}
