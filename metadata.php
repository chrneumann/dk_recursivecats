<?php

$sMetadataVersion = '1.1';
$aModule = array(
    'id' => 'dk_recursivecats',
    'title' => 'Recursive Categories',
    'description' => 'List products of category and its subcategories.',
    'version' => '0.0.0',
    'author' => 'Christian Neumann',
    'url' => 'http://www.datenkarussell.de',
    'email' => 'cneumann@datenkarussell.de',
    'extend' => array(
        'alist' => 'dk_recursivecats/controllers/dk_recursivecats_alist',
        'oxarticlelist' => 'dk_recursivecats/models/dk_recursivecats_oxarticlelist',
        'oxseoencoderarticle' =>
            'dk_recursivecats/models/dk_recursivecats_oxseoencoderarticle',
    ),
);