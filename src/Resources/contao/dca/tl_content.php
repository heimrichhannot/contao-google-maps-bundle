<?php

$dca = &$GLOBALS['TL_DCA']['tl_content'];

/**
 * Palettes
 */
$dca['palettes'][\HeimrichHannot\GoogleMapsBundle\Backend\Content::ELEMENT_GOOGLE_MAP] =
    '{type_legend},type,headline;{config_legend},map;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';
