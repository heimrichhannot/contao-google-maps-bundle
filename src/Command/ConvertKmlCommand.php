<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Heimrich & Hannot GmbH.
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Command;

use HeimrichHannot\GoogleMapsBundle\Util\KmlConverter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'huh:google-maps:convert-kml',
    description: 'Converts KML files to GeoJSON, preserving styling as simplestyle properties.'
)]
class ConvertKmlCommand extends Command
{
    public function __construct(
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'path',
                InputArgument::IS_ARRAY,
                'Files or directories to convert; directories are scanned recursively.'
            )
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Report what would be written without writing.')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Overwrite existing .geojson files.')
            ->addOption(
                'icon-base',
                null,
                InputOption::VALUE_REQUIRED,
                'Project-relative base path for rewritten icon URLs, e.g. files/maps/ico. Without it, icon URLs are left untouched.',
                ''
            )
            ->addOption(
                'local-host',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Host whose icon URLs are rewritten below --icon-base. Repeatable.'
            )
            ->addOption(
                'keep-folders',
                null,
                InputOption::VALUE_NONE,
                'Keep the KML folder hierarchy as folder/folderPath properties, e.g. to drive a layer switcher.'
            )
            ->addOption(
                'precision',
                null,
                InputOption::VALUE_REQUIRED,
                'Coordinate decimal places.',
                '6'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Convert KML to GeoJSON');

        $paths = $input->getArgument('path') ?: [$this->projectDir];
        $files = $this->collectKmlFiles($paths, $io);

        if (!$files) {
            $io->error('No KML files found.');

            return Command::FAILURE;
        }

        $dryRun = (bool) $input->getOption('dry-run');
        $force = (bool) $input->getOption('force');

        $converter = new KmlConverter(
            rtrim((string) $input->getOption('icon-base'), '/'),
            $input->getOption('local-host'),
            keepFolders: (bool) $input->getOption('keep-folders'),
            precision: max(0, (int) $input->getOption('precision')),
            iconRoot: $this->projectDir,
        );

        $io->text(\sprintf(
            'Converting %d KML file(s)%s',
            \count($files),
            $dryRun ? ' (dry run — nothing will be written)' : ''
        ));
        $io->newLine();

        $converted = 0;
        $failed = 0;
        $totalFeatures = 0;

        foreach ($files as $file) {
            $target = preg_replace('/\.kml$/i', '.geojson', $file);
            $label = $this->relativePath($file);

            if (file_exists($target) && !$force && !$dryRun) {
                $io->writeln(\sprintf('  <comment>SKIP</comment>   %s (exists — use --force)', $label));

                continue;
            }

            try {
                $geoJson = $converter->convertFile($file);
            } catch (\Throwable $exception) {
                $io->writeln(\sprintf('  <error>FAIL</error>   %s — %s', $label, $exception->getMessage()));
                ++$failed;

                continue;
            }

            $count = \count($geoJson['features']);

            if (0 === $count) {
                $io->writeln(\sprintf('  <comment>EMPTY</comment>  %s (no usable geometry)', $label));

                continue;
            }

            $json = json_encode($geoJson, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);

            if (false === $json) {
                $io->writeln(\sprintf('  <error>FAIL</error>   %s — %s', $label, json_last_error_msg()));
                ++$failed;

                continue;
            }

            if (!$dryRun && false === file_put_contents($target, $json."\n")) {
                $io->writeln(\sprintf('  <error>FAIL</error>   %s — could not write target', $label));
                ++$failed;

                continue;
            }

            $io->writeln(\sprintf('  <info>OK</info>     %s → %s (%d features)', $label, basename((string) $target), $count));
            $totalFeatures += $count;
            ++$converted;
        }

        $io->newLine();
        $io->text(\sprintf('%d converted, %d failed, %d features total', $converted, $failed, $totalFeatures));

        if ($external = $converter->getExternalIcons()) {
            $io->warning(\sprintf(
                "%d external icon URL(s) left untouched. Mirror them locally if the map is served over HTTPS, since http:// resources get blocked:\n  %s",
                \count($external),
                implode("\n  ", $external)
            ));
        }

        if ($missing = $converter->getMissingIcons()) {
            $io->warning(\sprintf(
                "%d rewritten icon path(s) do not exist and would render as broken markers:\n  %s",
                \count($missing),
                implode("\n  ", $missing)
            ));
        }

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * @param string[] $paths
     *
     * @return string[]
     */
    private function collectKmlFiles(array $paths, SymfonyStyle $io): array
    {
        $files = [];

        foreach ($paths as $path) {
            $path = $this->absolutePath($path);

            if (is_file($path)) {
                $files[] = $path;

                continue;
            }

            if (!is_dir($path)) {
                $io->warning(\sprintf('Not found, skipping: %s', $path));

                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ('kml' === strtolower($file->getExtension())) {
                    $files[] = $file->getPathname();
                }
            }
        }

        sort($files);

        return array_values(array_unique($files));
    }

    private function absolutePath(string $path): string
    {
        if (is_file($path) || is_dir($path)) {
            return $path;
        }

        return rtrim($this->projectDir, '/').'/'.ltrim($path, '/');
    }

    private function relativePath(string $path): string
    {
        $root = rtrim(str_replace('\\', '/', $this->projectDir), '/').'/';
        $path = str_replace('\\', '/', $path);

        return str_starts_with($path, $root) ? substr($path, \strlen($root)) : $path;
    }
}
