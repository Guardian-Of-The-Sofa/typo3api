<?php

namespace Nemo64\Typo3Api\Tca\Field;


use Nemo64\Typo3Api\Builder\Context\TcaBuilderContext;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Nemo64\Typo3Api\Utility\DbFieldDefinition;

class FileField extends AbstractField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'allowedFileExtensions' => '',
            'disallowedFileExtensions' => '', // only makes sense if allowedFileExtensions is empty
            'minitems' => 0,
            'maxitems' => 100,
            'collapseAll' => true,
            'allowHide' => function (Options $options) {
                // if you define minitems, you'd expect there to be at least one item.
                // however: hiding elements will prevent this so i just decided to disable hiding by default then.
                return $options['minitems'] === 0;
            },
            'dbType' => function (Options $options) {
                return DbFieldDefinition::getIntForNumberRange(0, $options['maxitems']);
            },
        ]);

        $resolver->setAllowedTypes('allowedFileExtensions', ['string', 'array']);
        $resolver->setAllowedTypes('disallowedFileExtensions', ['string', 'array']);

        $resolver->setAllowedTypes('minitems', 'int');
        $resolver->setAllowedTypes('maxitems', 'int');
        $resolver->setAllowedTypes('allowHide', 'bool');

        /** @noinspection PhpUnusedParameterInspection */
        $resolver->setNormalizer('allowedFileExtensions', function (Options $options, $allowedFileExtensions) {
            if (is_array($allowedFileExtensions)) {
                $allowedFileExtensions = implode(',', array_filter($allowedFileExtensions, 'strlen'));
            }

            return $allowedFileExtensions;
        });

        /** @noinspection PhpUnusedParameterInspection */
        $resolver->setNormalizer('disallowedFileExtensions', function (Options $options, $disallowedFileExtensions) {
            if (is_array($disallowedFileExtensions)) {
                $disallowedFileExtensions = implode(',', array_filter($disallowedFileExtensions, 'strlen'));
            }

            return $disallowedFileExtensions;
        });

        /** @noinspection PhpUnusedParameterInspection */
        $resolver->setNormalizer('minitems', function (Options $options, $minitems) {
            if ($minitems < 0) {
                throw new InvalidOptionsException("minitems must not be smaller than 0");
            }

            return $minitems;
        });

        $resolver->setNormalizer('maxitems', function (Options $options, $maxitems) {
            if ($maxitems < $options['minitems']) {
                throw new InvalidOptionsException("maxitems must not be smaller than minitems");
            }

            return $maxitems;
        });
    }

    public function getFieldTcaConfig(TcaBuilderContext $tcaBuilder)
    {
        return ExtensionManagementUtility::getFileFieldTCAConfig(
            $this->getOption('name'),
            [
                'minitems' => $this->getOption('minitems'),
                'maxitems' => $this->getOption('maxitems'),
                'appearance' => [
                    'collapseAll' => $this->getOption('collapseAll'),
                    'showPossibleLocalizationRecords' => $this->getOption('localize'),
                    'showRemovedLocalizationRecords' => $this->getOption('localize'),
                    'showAllLocalizationLink' => $this->getOption('localize'),
                    'showSynchronizationLink' => $this->getOption('localize'),
                    'enabledControls' => [
                        'hide' => $this->getOption('allowHide'),
                        'localize' => $this->getOption('localize'),
                    ]
                ]
            ],
            $this->getOption('allowedFileExtensions'),
            $this->getOption('disallowedFileExtensions')
        );
    }
}