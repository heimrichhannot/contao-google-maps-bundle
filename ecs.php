<?php

declare(strict_types=1);

use Contao\EasyCodingStandard\Sniffs\ContaoFrameworkClassAliasSniff;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer;
use SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $date = date('Y');
    $services
        ->set(HeaderCommentFixer::class)
        ->call(
            'configure',
            [
                [
                    'header' => "Copyright (c) $date Heimrich & Hannot GmbH\n\n@license LGPL-3.0-or-later",
                ]
            ]
        );

    $services
        ->set(GlobalNamespaceImportFixer::class)
        ->call('configure', [[
                                 'import_classes' => false,
                                 'import_constants' => false,
                                 'import_functions' => false,
                             ]])
    ;

    $services
        ->set(PhpdocTypesFixer::class)
        ->call('configure', [[
                                 'groups' => ['simple', 'meta'],
                             ]])
    ;

    $services
        ->set(ReferenceUsedNamesOnlySniff::class)
        ->property('searchAnnotations', true)
        ->property('allowFullyQualifiedNameForCollidingClasses', true)
        ->property('allowFullyQualifiedGlobalClasses', true)
        ->property('allowFullyQualifiedGlobalFunctions', true)
        ->property('allowFullyQualifiedGlobalConstants', true)
        ->property('allowPartialUses', false)
    ;

    $services->set(ContaoFrameworkClassAliasSniff::class);
};
