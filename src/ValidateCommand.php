<?php

namespace Imjoehaines\Flowder\Validator;

use Imjoehaines\Flowder\Validator\Validator;
use Imjoehaines\Flowder\Loader\PhpFileLoader;
use Symfony\Component\Console\Command\Command;
use Imjoehaines\Flowder\Loader\LoaderInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Imjoehaines\Flowder\Validator\Exception\LoaderConfigFileNotFoundException;

final class ValidateCommand extends Command
{
    protected function configure()
    {
        $this->setName('validate')
            ->setDescription('Validate Flowder fixture files.')
            ->addArgument(
                'loader config',
                InputArgument::REQUIRED,
                'Path to the loader config file, returning an instance of ' . LoaderInterface::class
            )
            ->addArgument(
                'thing to load',
                InputArgument::REQUIRED,
                'The thing to load, e.g. a PHP file if using ' . PhpFileLoader::class
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loader = $this->getLoader($input->getArgument('loader config'));

        $thingToLoad = $input->getArgument('thing to load');

        $data = $loader->load($thingToLoad);

        $invalidResults = [];

        foreach ($data as $table => $tableData) {
            $validator = new Validator();

            $result = $validator->validate($table, $tableData);

            if ($result->isValid()) {
                $this->outputUsingVerbosity(
                    $output,
                    '<fg=green>✔</> ',
                    '<fg=green>✔</> ' . $table
                );
            } else {
                $invalidResults[$table] = $result;

                $this->outputUsingVerbosity(
                    $output,
                    '<fg=red>✘</> ',
                    '<fg=red>✘ ' . $table . '</>'
                );
            }
        }


        if (!empty($invalidResults)) {
            $output->writeln('');
            $output->writeln('Oops! There are some problems with your fixtures:');

            $formatter = $this->getHelper('formatter');

            foreach ($invalidResults as $table => $result) {
                $title = PHP_EOL . '<error> ' . $table . ' </>';

                $block = $formatter->formatBlock($result->getErrors(), 'error', true);

                $output->writeln($title);
                $output->writeln($block);
            }

            return 1;
        }

        $output->writeln(PHP_EOL);
        $output->writeln('Done!');
    }

    private function getLoader($configPath)
    {
        if (!file_exists($configPath)) {
            throw new LoaderConfigFileNotFoundException($configPath);
        }

        return require $configPath;
    }

    private function outputUsingVerbosity($output, $normalText, $verboseText)
    {
        $output->write(
            $output->getVerbosity() === OutputInterface::VERBOSITY_NORMAL ? $normalText : $verboseText,
            $output->getVerbosity() !== OutputInterface::VERBOSITY_NORMAL
        );
    }
}
