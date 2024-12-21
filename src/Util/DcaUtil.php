<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Util;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\DataContainer;
use Contao\Model;
use Contao\System;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Contracts\Translation\TranslatorInterface;

class DcaUtil
{
    protected ContaoFramework $framework;

    protected TranslatorInterface $translator;

    protected Utils $utils;

    public function __construct(ContaoFramework $framework, TranslatorInterface $translator, Utils $utils)
    {
        $this->framework = $framework;
        $this->translator = $translator;
        $this->utils = $utils;
    }

    /**
     * Sets the current date as the date added -> usually used on submit.
     */
    public function setDateAdded(DataContainer $dc): void
    {
        $modelUtil = $this->utils->model();

        if (null === $dc || null === ($model = $modelUtil->findModelInstanceByPk($dc->table, $dc->id)) || $model->dateAdded > 0) {
            return;
        }

        $this->framework->createInstance(Database::class)->prepare("UPDATE $dc->table SET dateAdded=? WHERE id=? AND dateAdded = 0")->execute(time(), $dc->id);
    }

    /**
     * Sets the current date as the date added -> usually used on copy.
     */
    public function setDateAddedOnCopy($insertId, DataContainer $dc)
    {
        $modelUtil = $this->utils->model();

        if (null === $dc || null === ($model = $modelUtil->findModelInstanceByPk($dc->table, $insertId)) || $model->dateAdded > 0) {
            return null;
        }

        $this->framework->createInstance(Database::class)->prepare("UPDATE $dc->table SET dateAdded=? WHERE id=? AND dateAdded = 0")->execute(time(), $insertId);
    }

    /**
     * Adds an override selector to every field in $fields to the dca associated
     * with $destinationTable.
     */
    public function addOverridableFields(array $fields, string $sourceTable, string $destinationTable, array $options = []): void
    {
        $this->framework->getAdapter(Controller::class)->loadDataContainer($sourceTable);
        System::loadLanguageFile($sourceTable);
        $sourceDca = $GLOBALS['TL_DCA'][$sourceTable];

        $this->framework->getAdapter(Controller::class)->loadDataContainer($destinationTable);
        System::loadLanguageFile($destinationTable);
        $destinationDca = &$GLOBALS['TL_DCA'][$destinationTable];

        foreach ($fields as $field) {
            // add override boolean field
            $overrideFieldname = 'override'.ucfirst($field);

            $destinationDca['fields'][$overrideFieldname] = [
                'label' => &$GLOBALS['TL_LANG'][$destinationTable][$overrideFieldname],
                'inputType' => 'checkbox',
                'eval' => ['tl_class' => 'w50', 'submitOnChange' => true, 'isOverrideSelector' => true],
                'sql' => "char(1) NOT NULL default ''",
            ];

            if (isset($options['checkboxDcaEvalOverride']) && \is_array($options['checkboxDcaEvalOverride'])) {
                $destinationDca['fields'][$overrideFieldname]['eval'] = array_merge($destinationDca['fields'][$overrideFieldname]['eval'], $options['checkboxDcaEvalOverride']);
            }

            // important: nested selectors need to be in reversed order -> see
            // DC_Table::getPalette()
            $destinationDca['palettes']['__selector__'] = array_merge([$overrideFieldname], isset($destinationDca['palettes']['__selector__']) && \is_array($destinationDca['palettes']['__selector__']) ? $destinationDca['palettes']['__selector__'] : []);

            // copy field
            $destinationDca['fields'][$field] = $sourceDca['fields'][$field];

            // subpalette
            $destinationDca['subpalettes'][$overrideFieldname] = $field;

            if (!isset($options['skipLocalization']) || !$options['skipLocalization']) {
                $GLOBALS['TL_LANG'][$destinationTable][$overrideFieldname] = [
                    $this->translator->trans('huh.google_maps.utils.misc.override.label', [
                        '%fieldname%' => $GLOBALS['TL_DCA'][$sourceTable]['fields'][$field]['label'][0] ?? $field,
                    ]),
                    $this->translator->trans('huh.google_maps.utils.misc.override.desc', [
                        '%fieldname%' => $GLOBALS['TL_DCA'][$sourceTable]['fields'][$field]['label'][0] ?? $field,
                    ]),
                ];
            }
        }
    }

    /**
     * Retrieves a property of given contao model instances by *ascending* priority,
     * i.e. the last instance of $instances will have the highest priority.
     *
     * CAUTION: This function assumes that you have used addOverridableFields() in
     * this class!! That means, that a value in a model instance is only used if it's
     * either the first instance in $arrInstances or "overrideFieldname" is set to
     * true in the instance.
     *
     * @param string $property  The property name to retrieve
     * @param array  $instances An array of instances in ascending priority. Instances can be passed in the following form:
     *                          ['tl_some_table', $instanceId] or $objInstance
     */
    public function getOverridableProperty(string $property, array $instances)
    {
        $result = null;
        $preparedInstances = [];

        // prepare instances
        foreach ($instances as $instance) {
            if (\is_array($instance)) {
                $modelUtil = $this->utils->model();
                if (null !== ($objInstance = $modelUtil->findModelInstanceByPk($instance[0], $instance[1]))) {
                    $preparedInstances[] = $objInstance;
                }
            } elseif ($instance instanceof Model || \is_object($instance)) {
                $preparedInstances[] = $instance;
            }
        }

        foreach ($preparedInstances as $i => $preparedInstance) {
            if (0 === $i || $preparedInstance->{'override'.ucfirst($property)}) {
                $result = $preparedInstance->{$property};
            }
        }

        return $result;
    }
}
